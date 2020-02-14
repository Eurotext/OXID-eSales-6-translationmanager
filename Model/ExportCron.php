<?php

/**
 * Settings class
 *
 */

namespace Eurotext\Translationmanager\Model;

/**
 * Settings class
 *
 */
class ExportCron extends \OxidEsales\Eshop\Core\Model\BaseModel
{

    /**
     * Standard cron executing function.
     */
    public function execute()
    {
        // 0. Get some options
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $maxExports = intval($oConfig->getShopConfVar('sEXPORTJOBIPJ', $oConfig->getShopId(), 'module:translationmanager6'));

        // 1. Query all project with status 30
        $aProjects = array();
        $this->_queryProjects($aProjects);

        // 2.1. For each project we get a list of cms page to export
        $this->_queryElements('cms', $aProjects, $maxExports);

        // 2.2. For each project we get a list of attribute to export
        $this->_queryElements('attribute', $aProjects, $maxExports);

        // 2.3. For each project we get a list of categories to export
        $this->_queryElements('category', $aProjects, $maxExports);

        // 2.4. For each project we get a list of articles to export
        $this->_queryElements('article', $aProjects, $maxExports);

        // 3.0 Prepare items.
        $aItems = array();

        // 3.1 Prepare cms items
        $this->_prepareCMSItems($aProjects, $aItems);

        // 3.2 Prepare category items
        $this->_prepareCategoryItems($aProjects, $aItems);

        // 3.3 Prepare attribute items
        $this->_prepareAttributeItems($aProjects, $aItems);

        // 3.4 Prepare article items
        $this->_prepareArticleItems($aProjects, $aItems);

        // 4.0 Export to remote
        $this->_exportItems($aProjects, $aItems);

        // 5.0 Update projects progress
        $this->_updateProjectsProgress($aProjects);
    }

    /**
     * Recalculates the progress of export for all projects.
     *
     * @param array $aProjects List of project
     *
     * @return void
     */
    protected function _updateProjectsProgress(&$aProjects)
    {
        foreach ($aProjects as $aProject) {
            // Only recalculate for projects that are marked as dirty
            if ($aProject['DIRTY']) {
                $oProject = oxNew('\Eurotext\Translationmanager\Model\Project');
                $oProject->load($aProject['OXID']);
                $oProject->updateExportProgress();
            }
        }

        return;
    }

    /**
     * Returns integer code of langs in oxid.
     *
     * @param array $aProjects Projects list
     * @param array $aItems    List of items to be exported.
     *
     * @return void
     */
    protected function _exportItems(&$aProjects, &$aItems)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sAPIKEY = $oConfig->getShopConfVar('sAPIKEY', $oConfig->getShopId(), 'module:translationmanager6');
        $sSERVICEURL = $oConfig->getShopConfVar('sSERVICEURL', $oConfig->getShopId(), 'module:translationmanager6');
        $aTableMapping = array(
            'oxattribute' => 'ettm_project2attribute',
            'oxobject2attribute' => 'ettm_project2attribute',
            'oxarticles' => 'ettm_project2article',
            'oxartextends' => 'ettm_project2article',
            'oxcategories' => 'ettm_project2category',
            'oxcontents' => 'ettm_project2cms',
        );

        echo '<pre>';
        echo "Projects: \n";
        print_r($aProjects);
        echo "Items: \n";
        print_r($aItems);

        foreach ($aItems as $iIndex => $aItem) {
            $exportable = !$aItem['innermeta']['skip'];
            $skipped = $aItem['innermeta']['skip'];
            $iTransmitted = 0;
            $iError = 0;

            $aItemInnerMeta = $aItem['innermeta'];
            unset($aItem['innermeta']);

            $aCurrentProject = &$aProjects[$aItemInnerMeta['project_index']];
            $aCurrentProject['DIRTY'] = true;

            $iExternalProjectid = $aCurrentProject['EXTERNAL_ID'];

            $uri = '/api/v1/project/' . $iExternalProjectid . '/item.json';
            $headers = array(
                'Content-Type' => 'application/json',
                'apikey' => $sAPIKEY,
            );
            $client = new \GuzzleHttp\Client([
                'base_uri' => $sSERVICEURL,
                'timeout'  => 2.0,
            ]);

            echo 'Preparing to send Item ' . $iIndex . '. Is skippable: ';
            echo ($skipped)?'true':'false';

            // Send to eurotext only if exportable
            if ($exportable) {
                try {
                    $headers['X-Source'] = $aItem['headers']['from'];
                    $headers['X-Target'] = $aItem['headers']['to'];
                    $headers['X-TextType'] = $aItem['headers']['textType'];
                    unset($aItem['headers']);
                    $client->post(
                        $uri,
                        array(
                            'headers' => $headers,
                            'json' => $aItem,
                        )
                    );
                    $iTransmitted = 1;
                    echo ' Successfully uploaded <br>';
                } catch (\Exception $e) {
                    // Do nothing
                    $iError = 1;
                    echo ' Error with upload <br>';
                }
            } else {
                echo ' Skipping <br>';
            }

            $iExportable = ($exportable)?1:0;
            $iSkipped = ($skipped)?1:0;

            // Update status and counter
            $sTableName = $aItem['__meta']['item_table'];
            $sRowId = $aItemInnerMeta['join_id'];
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute(
                "UPDATE $sTableName
                    SET STATUS = 10, EXPORTABLE=EXPORTABLE+?, SKIPPED=SKIPPED+?, TRANSMITTED=TRANSMITTED+?, FAILED=FAILED+?
                    WHERE OXID = ?",
                array($iExportable, $iSkipped, $iTransmitted, $iError, $sRowId)
            );
            echo 'Count up for ' . $sTableName . ' with ID ' . $sRowId . '<br>';
        }
        return;
    }

    /**
     * Returns integer code of langs in oxid.
     *
     * @return array
     */
    protected function _getLangCodesMapping()
    {
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $aLanguages = $oLang->getLanguageArray();
        $aRetArray = array();
        foreach ($aLanguages as $aLanguage) {
            $aRetArray[$aLanguage->oxid] = $aLanguage->id;
        }
        return $aRetArray;
    }

    /**
     * Returns integer code of langs in oxid.
     *
     * @return array
     */
    protected function _getInverseLangCodesMapping()
    {
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $aLanguages = $oLang->getLanguageArray();
        $aRetArray = array();
        foreach ($aLanguages as $aLanguage) {
            $aRetArray[$aLanguage->id] = $aLanguage->oxid;
        }
        return $aRetArray;
    }


    /**
     * Returns selected lang codes from eurotext database.
     *
     * @return array
     */
    protected function _getEurotextMapping()
    {
        $oMapping = oxNew('\Eurotext\Translationmanager\Model\Mapping');
        return $oMapping->getMapping();
    }

    /**
     * Create and export cms items.
     *
     * @param array $aProjects Projects array
     * @param array $aItems    Items array
     */
    protected function _prepareCMSItems(&$aProjects, &$aItems)
    {
        foreach ($aProjects as $iProjectIndex => $aProject) {
            foreach ($aProject['cms_items'] as $aItem) {
                // Create and merge attribute name items.
                $aItems = array_merge($aItems, $this->_prepareCMSPage($aProject, $iProjectIndex, $aItem));
                // Create and merge attribute name items.
                $aItems = array_merge($aItems, $this->_prepareCMSSeo($aProject, $iProjectIndex, $aItem));
            } // ./foreach cms pages
        } // ./foreach projects
    }

    /**
     * Create attribute name items.
     *
     * @param array $aProject      Projects array
     * @param int   $iProjectIndex Items array
     * @param array $aItem
     *
     * @return array
     */
    protected function _prepareCMSPage($aProject, $iProjectIndex, $aItem)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aLangMapping = $this->_getLangCodesMapping();
        $aInverseLangMapping = $this->_getInverseLangCodesMapping();
        $aEurotextMapping = $this->_getEurotextMapping();
        $sCmsTable = getViewName('oxcontents', $aLangMapping[$aProject['LANG_ORIGIN']]);
        $sJoinTable = 'ettm_project2cms';
        $is_only_untranslated = (1 === intval($aProject['ONLY_UNTRANSLATED']));

        // 1. Get main page data
        $sPageQuery = "SELECT $sCmsTable.*
            FROM $sCmsTable
            JOIN $sJoinTable ON $sCmsTable.OXID = $sJoinTable.OXCMSID
            WHERE $sJoinTable.OXID = ?";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sPageQuery, array($aItem['OXID']));

        // 2. Create export items
        $aReturn = array();
        if ($oRs !== false && $oRs->count() > 0) {
            $aTargetLanguages = unserialize($aProject['LANG_TARGET']);
            foreach ($aTargetLanguages as $sTargetLanguage) {
                // Check if already translated
                if ($is_only_untranslated) {
                    $translated = $this->_isItemTranslated('oxcontents', $aLangMapping[$sTargetLanguage], $oRs->fields['OXID']);
                } else {
                    $translated = false;
                }

                $aItemT = array(
                    'headers' => array(
                        'from' => $aEurotextMapping[$aProject['LANG_ORIGIN']],
                        'to' => $aEurotextMapping[$sTargetLanguage],
                        'textType' => 'marketing',
                    ),
                    'innermeta' => array(
                        'join_id' => $aItem['OXID'],
                        'project_index' => $iProjectIndex,
                        'skip' => $translated,
                    ),
                    '__meta' => array(
                        'OXID' => $oRs->fields['OXID'],
                        'view' => 'oxcontents',
                        'item_table' => 'ettm_project2cms',
                        'origin_language' => $aLangMapping[$aProject['LANG_ORIGIN']],
                        'origin_shop_id' => $oConfig->getShopId(),
                    ),
                );

                // Get CMS Fields
                $aFields = $oConfig->getShopConfVar('cmsfields', $oConfig->getShopId(), 'module:translationmanager6');
                if (0 < count($aFields)) {
                    foreach ($aFields as $sField) {
                        $aItemT[$sField] = $oRs->fields[$sField];
                    }
                }
                $aReturn[] = $aItemT;
            }
        }

        return $aReturn;
    }

    /**
     * Create attribute name items.
     *
     * @param array $aProject      Projects array
     * @param int   $iProjectIndex Items array
     * @param array $aItem
     *
     * @return array
     */
    protected function _prepareCMSSeo($aProject, $iProjectIndex, $aItem)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aLangMapping = $this->_getLangCodesMapping();
        $aInverseLangMapping = $this->_getInverseLangCodesMapping();
        $aEurotextMapping = $this->_getEurotextMapping();
        $sCmsTable = getViewName('oxobject2seodata', $aLangMapping[$aProject['LANG_ORIGIN']]);
        $sJoinTable = 'ettm_project2cms';
        $is_only_untranslated = (1 === intval($aProject['ONLY_UNTRANSLATED']));

        // 1. Get main page data
        $sPageQuery = "SELECT $sCmsTable.*
            FROM $sCmsTable
            JOIN $sJoinTable ON $sCmsTable.OXOBJECTID = $sJoinTable.OXCMSID
            WHERE $sJoinTable.OXID = ? AND $sCmsTable.OXSHOPID = ? AND $sCmsTable.OXLANG = ?";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sPageQuery, array($aItem['OXID'], $oConfig->getShopId(), $aLangMapping[$aProject['LANG_ORIGIN']]));

        // 2. Create export items
        $aReturn = array();
        if ($oRs !== false && $oRs->count() > 0) {
            $aTargetLanguages = unserialize($aProject['LANG_TARGET']);
            foreach ($aTargetLanguages as $sTargetLanguage) {
                // Check if already translated
                if ($is_only_untranslated) {
                    $translated = $this->_isItemTranslated('oxobject2seodata', $aLangMapping[$sTargetLanguage], $oRs->fields['OXID'], 'OXDESCRIPTION');
                } else {
                    $translated = false;
                }

                $aItemT = array(
                    'headers' => array(
                        'from' => $aEurotextMapping[$aProject['LANG_ORIGIN']],
                        'to' => $aEurotextMapping[$sTargetLanguage],
                        'textType' => 'marketing',
                    ),
                    'innermeta' => array(
                        'join_id' => $aItem['OXID'],
                        'project_index' => $iProjectIndex,
                        'skip' => $translated,
                    ),
                    '__meta' => array(
                        'OXID' => $oRs->fields['OXOBJECTID'],
                        'view' => 'oxobject2seodata',
                        'item_table' => 'ettm_project2cms',
                        'origin_language' => $aLangMapping[$aProject['LANG_ORIGIN']],
                        'origin_shop_id' => $oConfig->getShopId(),
                    ),
                );

                // Get CMS Fields
                $aFields = $oConfig->getShopConfVar('cmsseofields', $oConfig->getShopId(), 'module:translationmanager6');
                if (0 < count($aFields)) {
                    foreach ($aFields as $sField) {
                        $aItemT[$sField] = $oRs->fields[$sField];
                    }
                }
                $aReturn[] = $aItemT;
            }
        }

        return $aReturn;
    }


    /**
     * Create and export cms items.
     *
     * @param array $aProjects Projects array
     * @param array $aItems    Items array
     */
    protected function _prepareCategoryItems(&$aProjects, &$aItems)
    {
        foreach ($aProjects as $iProjectIndex => $aProject) {
            foreach ($aProject['category_items'] as $aItem) {
                // Create and merge attribute name items.
                $aItems = array_merge($aItems, $this->_prepareCategory($aProject, $iProjectIndex, $aItem));
                // Create and merge attribute name items.
                $aItems = array_merge($aItems, $this->_prepareCategorySeo($aProject, $iProjectIndex, $aItem));

            } // ./foreach cms pages
        } // ./foreach projects
    }

    /**
     * Create attribute name items.
     *
     * @param array $aProject      Projects array
     * @param int   $iProjectIndex Items array
     * @param array $aItem
     *
     * @return array
     */
    protected function _prepareCategory($aProject, $iProjectIndex, $aItem)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        $aLangMapping = $this->_getLangCodesMapping();
        $aInverseLangMapping = $this->_getInverseLangCodesMapping();
        $aEurotextMapping = $this->_getEurotextMapping();
        $sTable = getViewName('oxcategories', $aLangMapping[$aProject['LANG_ORIGIN']]);
        $sJoinTable = 'ettm_project2category';
        $is_only_untranslated = (1 === intval($aProject['ONLY_UNTRANSLATED']));

        // 1. Get main page data
        $sPageQuery = "SELECT $sTable.*
            FROM $sTable
            JOIN $sJoinTable ON $sTable.OXID = $sJoinTable.OXCATEGORYID
            WHERE $sJoinTable.OXID = ?";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sPageQuery, array($aItem['OXID']));

        // 2. Create export items
        $aReturn = array();
        if ($oRs !== false && $oRs->count() > 0) {
            $aTargetLanguages = unserialize($aProject['LANG_TARGET']);
            foreach ($aTargetLanguages as $sTargetLanguage) {
                // Check if already translated
                if ($is_only_untranslated) {
                    $translated = $this->_isItemTranslated('oxcategories', $aLangMapping[$sTargetLanguage], $oRs->fields['OXID']);
                } else {
                    $translated = false;
                }

                $aItemT = array(
                    'headers' => array(
                        'from' => $aEurotextMapping[$aProject['LANG_ORIGIN']],
                        'to' => $aEurotextMapping[$sTargetLanguage],
                        'textType' => 'specialized-text',
                    ),
                    'innermeta' => array(
                        'join_id' => $aItem['OXID'],
                        'project_index' => $iProjectIndex,
                        'skip' => $translated,
                    ),
                    '__meta' => array(
                        'OXID' => $oRs->fields['OXID'],
                        'view' => 'oxcategories',
                        'item_table' => 'ettm_project2category',
                        'origin_language' => $aLangMapping[$aProject['LANG_ORIGIN']],
                        'origin_shop_id' => $oConfig->getShopId(),
                    ),
                );

                // Get Category Fields
                $aFields = $oConfig->getShopConfVar('categoryfields', $oConfig->getShopId(), 'module:translationmanager6');
                if (0 < count($aFields)) {
                    foreach ($aFields as $sField) {
                        $aItemT[$sField] = $oRs->fields[$sField];
                    }
                }
                $aReturn[] = $aItemT;
            }
        }

        return $aReturn;
    }

    /**
     * Create attribute name items.
     *
     * @param array $aProject      Projects array
     * @param int   $iProjectIndex Items array
     * @param array $aItem
     *
     * @return array
     */
    protected function _prepareCategorySeo($aProject, $iProjectIndex, $aItem)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aLangMapping = $this->_getLangCodesMapping();
        $aInverseLangMapping = $this->_getInverseLangCodesMapping();
        $aEurotextMapping = $this->_getEurotextMapping();
        $sTable = getViewName('oxobject2seodata', $aLangMapping[$aProject['LANG_ORIGIN']]);
        $sJoinTable = 'ettm_project2category';
        $is_only_untranslated = (1 === intval($aProject['ONLY_UNTRANSLATED']));

        // 1. Get main page data
        $sPageQuery = "SELECT $sTable.*
            FROM $sTable
            JOIN $sJoinTable ON $sTable.OXOBJECTID = $sJoinTable.OXCATEGORYID
            WHERE $sJoinTable.OXID = ? AND $sTable.OXSHOPID = ? AND $sTable.OXLANG = ?";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sPageQuery, array($aItem['OXID'], $oConfig->getShopId(), $aLangMapping[$aProject['LANG_ORIGIN']]));

        // 2. Create export items
        $aReturn = array();
        if ($oRs !== false && $oRs->count() > 0) {
            $aTargetLanguages = unserialize($aProject['LANG_TARGET']);
            foreach ($aTargetLanguages as $sTargetLanguage) {
                // Check if already translated
                if ($is_only_untranslated) {
                    $translated = $this->_isItemTranslated('oxobject2seodata', $aLangMapping[$sTargetLanguage], $oRs->fields['OXID'], 'OXDESCRIPTION');
                } else {
                    $translated = false;
                }

                $aItemT = array(
                    'headers' => array(
                        'from' => $aEurotextMapping[$aProject['LANG_ORIGIN']],
                        'to' => $aEurotextMapping[$sTargetLanguage],
                        'textType' => 'specialized-text',
                    ),
                    'innermeta' => array(
                        'join_id' => $aItem['OXID'],
                        'project_index' => $iProjectIndex,
                        'skip' => $translated,
                    ),
                    '__meta' => array(
                        'OXID' => $oRs->fields['OXOBJECTID'],
                        'view' => 'oxobject2seodata',
                        'item_table' => 'ettm_project2category',
                        'origin_language' => $aLangMapping[$aProject['LANG_ORIGIN']],
                        'origin_shop_id' => $oConfig->getShopId(),
                    ),
                );

                // Get CMS Fields
                $aFields = $oConfig->getShopConfVar('categoryseofields', $oConfig->getShopId(), 'module:translationmanager6');
                if (0 < count($aFields)) {
                    foreach ($aFields as $sField) {
                        $aItemT[$sField] = $oRs->fields[$sField];
                    }
                }
                $aReturn[] = $aItemT;
            }
        }

        return $aReturn;
    }

    /**
     * Create attribute items.
     *
     * @param array $aProjects Projects array
     * @param array $aItems    Items array
     */
    protected function _prepareAttributeItems(&$aProjects, &$aItems)
    {
        foreach ($aProjects as $iProjectIndex => $aProject) {
            foreach ($aProject['attribute_items'] as $aItem) {
                // Create and merge attribute name items.
                $aItems = array_merge($aItems, $this->_prepareAttributeNames($aProject, $iProjectIndex, $aItem));
                // Create and merge attribute value items.
                $aItems = array_merge($aItems, $this->_prepareAttributeValues($aProject, $iProjectIndex, $aItem));
            } // ./foreach cms pages
        } // ./foreach projects
    }

    /**
     * Create attribute name items.
     *
     * @param array $aProject      Projects array
     * @param int   $iProjectIndex Items array
     * @param array $aItem
     *
     * @return array
     */
    protected function _prepareAttributeNames($aProject, $iProjectIndex, $aItem)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aLangMapping = $this->_getLangCodesMapping();
        $aInverseLangMapping = $this->_getInverseLangCodesMapping();
        $aEurotextMapping = $this->_getEurotextMapping();
        $sTable = getViewName('oxattribute', $aLangMapping[$aProject['LANG_ORIGIN']]);
        $s2Table = getViewName('oxobject2attribute', $aLangMapping[$aProject['LANG_ORIGIN']]);
        $sJoinTable = 'ettm_project2attribute';
        $is_only_untranslated = (1 === intval($aProject['ONLY_UNTRANSLATED']));

        // 1. Get main page data
        $sPageQuery = "SELECT $sTable.*
            FROM $sTable
            JOIN $sJoinTable ON $sTable.OXID = $sJoinTable.OXATTRIBUTEID
            WHERE $sJoinTable.OXID = ?";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sPageQuery, array($aItem['OXID']));

        // 2. Create export items
        $aReturn = array();
        if ($oRs !== false && $oRs->count() > 0) {
            $aTargetLanguages = unserialize($aProject['LANG_TARGET']);
            foreach ($aTargetLanguages as $sTargetLanguage) {
                // Check if already translated
                if ($is_only_untranslated) {
                    $translated = $this->_isItemTranslated('oxattribute', $aLangMapping[$sTargetLanguage], $oRs->fields['OXID']);
                } else {
                    $translated = false;
                }

                $aItemT = array(
                    'headers' => array(
                        'from' => $aEurotextMapping[$aProject['LANG_ORIGIN']],
                        'to' => $aEurotextMapping[$sTargetLanguage],
                        'textType' => 'term',
                    ),
                    'innermeta' => array(
                        'join_id' => $aItem['OXID'],
                        'project_index' => $iProjectIndex,
                        'skip' => $translated,
                    ),
                    '__meta' => array(
                        'OXID' => $oRs->fields['OXID'],
                        'view' => 'oxattribute',
                        'item_table' => 'ettm_project2attribute',
                        'origin_language' => $aLangMapping[$aProject['LANG_ORIGIN']],
                        'origin_shop_id' => $oConfig->getShopId(),
                    ),
                );

                // Get Attributes Fields
                $aFields = $oConfig->getShopConfVar('attributesfields', $oConfig->getShopId(), 'module:translationmanager6');
                if (0 < count($aFields)) {
                    foreach ($aFields as $sField) {
                        $aItemT[$sField] = $oRs->fields[$sField];
                    }
                }
                $aReturn[] = $aItemT;
            }
        }

        return $aReturn;
    }

    /**
     * Create attribute value items.
     *
     * @param array $aProject      Projects array
     * @param int   $iProjectIndex Items array
     * @param array $aItem
     *
     * @return array
     */
    protected function _prepareAttributeValues($aProject, $iProjectIndex, $aItem)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aLangMapping = $this->_getLangCodesMapping();
        $aInverseLangMapping = $this->_getInverseLangCodesMapping();
        $aEurotextMapping = $this->_getEurotextMapping();
        $sTable = getViewName('oxattribute', $aLangMapping[$aProject['LANG_ORIGIN']]);
        $s2Table = getViewName('oxobject2attribute', $aLangMapping[$aProject['LANG_ORIGIN']]);
        $sJoinTable = 'ettm_project2attribute';
        $is_only_untranslated = (1 === intval($aProject['ONLY_UNTRANSLATED']));

        // 3. Get attribute value
        $s2PageQuery = "SELECT $s2Table.*
        FROM $s2Table
        JOIN $sJoinTable ON $s2Table.OXATTRID = $sJoinTable.OXATTRIBUTEID
        WHERE $sJoinTable.OXID = ?";

        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($s2PageQuery, array($aItem['OXID']));

        // 4. Create additional items
        $aReturn = array();
        if ($oRs !== false && $oRs->count() > 0) {
            $aTargetLanguages = unserialize($aProject['LANG_TARGET']);
            foreach ($aTargetLanguages as $sTargetLanguage) {
                // Check if already translated
                if ($is_only_untranslated) {
                    $translated = $this->_isItemTranslated('oxobject2attribute', $aLangMapping[$sTargetLanguage], $oRs->fields['OXID'], 'OXVALUE');
                } else {
                    $translated = false;
                }

                $aItemT = array(
                    'headers' => array(
                        'from' => $aEurotextMapping[$aProject['LANG_ORIGIN']],
                        'to' => $aEurotextMapping[$sTargetLanguage],
                        'textType' => 'term',
                    ),
                    'innermeta' => array(
                        'join_id' => $aItem['OXID'],
                        'project_index' => $iProjectIndex,
                        'skip' => $translated,
                    ),
                    '__meta' => array(
                        'OXID' => $oRs->fields['OXID'],
                        'view' => 'oxobject2attribute',
                        'item_table' => 'ettm_project2attribute',
                        'origin_language' => $aLangMapping[$aProject['LANG_ORIGIN']],
                        'origin_shop_id' => $oConfig->getShopId(),
                    ),
                );

                // Get Attributes Fields
                $aFields = $oConfig->getShopConfVar('o2attributesfields', $oConfig->getShopId(), 'module:translationmanager6');
                if (0 < count($aFields)) {
                    foreach ($aFields as $sField) {
                        $aItemT[$sField] = $oRs->fields[$sField];
                    }
                }
                $aReturn[] = $aItemT;
            }
        }

        return $aReturn;
    }

    /**
     * Create attribute items.
     *
     * @param array $aProjects Projects array
     * @param array $aItems    Items array
     */
    protected function _prepareArticleItems(&$aProjects, &$aItems)
    {
        foreach ($aProjects as $iProjectIndex => $aProject) {
            foreach ($aProject['article_items'] as $aItem) {
                // Create items for article main table.
                $aItems = array_merge($aItems, $this->_prepareArticlesMain($aProject, $iProjectIndex, $aItem));
                // Create items for article extends tables.
                $aItems = array_merge($aItems, $this->_prepareArticlesExtends($aProject, $iProjectIndex, $aItem));
                // Create items for article extends tables.
                $aItems = array_merge($aItems, $this->_prepareArticleSeo($aProject, $iProjectIndex, $aItem));

            } // ./foreach cms pages
        } // ./foreach projects
    }

    /**
     * Create attribute name items.
     *
     * @param array $aProject      Projects array
     * @param int   $iProjectIndex Items array
     * @param array $aItem
     *
     * @return array
     */
    protected function _prepareArticleSeo($aProject, $iProjectIndex, $aItem)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aLangMapping = $this->_getLangCodesMapping();
        $aInverseLangMapping = $this->_getInverseLangCodesMapping();
        $aEurotextMapping = $this->_getEurotextMapping();
        $sTable = getViewName('oxobject2seodata', $aLangMapping[$aProject['LANG_ORIGIN']]);
        $sJoinTable = 'ettm_project2article';
        $is_only_untranslated = (1 === intval($aProject['ONLY_UNTRANSLATED']));

        // 1. Get main page data
        $sPageQuery = "SELECT $sTable.*
            FROM $sTable
            JOIN $sJoinTable ON $sTable.OXOBJECTID = $sJoinTable.OXARTICLEID
            WHERE $sJoinTable.OXID = ? AND $sTable.OXSHOPID = ? AND $sTable.OXLANG = ?";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sPageQuery, array($aItem['OXID'], $oConfig->getShopId(), $aLangMapping[$aProject['LANG_ORIGIN']]));

        // 2. Create export items
        $aReturn = array();
        if ($oRs !== false && $oRs->count() > 0) {
            $aTargetLanguages = unserialize($aProject['LANG_TARGET']);
            foreach ($aTargetLanguages as $sTargetLanguage) {
                // Check if already translated
                if ($is_only_untranslated) {
                    $translated = $this->_isItemTranslated('oxobject2seodata', $aLangMapping[$sTargetLanguage], $oRs->fields['OXID'], 'OXDESCRIPTION');
                } else {
                    $translated = false;
                }

                $aItemT = array(
                    'headers' => array(
                        'from' => $aEurotextMapping[$aProject['LANG_ORIGIN']],
                        'to' => $aEurotextMapping[$sTargetLanguage],
                        'textType' => 'specialized-text',
                    ),
                    'innermeta' => array(
                        'join_id' => $aItem['OXID'],
                        'project_index' => $iProjectIndex,
                        'skip' => $translated,
                    ),
                    '__meta' => array(
                        'OXID' => $oRs->fields['OXOBJECTID'],
                        'view' => 'oxobject2seodata',
                        'item_table' => 'ettm_project2article',
                        'origin_language' => $aLangMapping[$aProject['LANG_ORIGIN']],
                        'origin_shop_id' => $oConfig->getShopId(),
                    ),
                );

                // Get CMS Fields
                $aFields = $oConfig->getShopConfVar('articleseofields', $oConfig->getShopId(), 'module:translationmanager6');
                if (0 < count($aFields)) {
                    foreach ($aFields as $sField) {
                        $aItemT[$sField] = $oRs->fields[$sField];
                    }
                }
                $aReturn[] = $aItemT;
            }
        }

        return $aReturn;
    }

    /**
     * Create articles main items.
     *
     * @param array $aProject      Projects array
     * @param int   $iProjectIndex Items array
     * @param array $aItem
     *
     * @return array
     */
    protected function _prepareArticlesMain($aProject, $iProjectIndex, $aItem)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aLangMapping = $this->_getLangCodesMapping();
        $aInverseLangMapping = $this->_getInverseLangCodesMapping();
        $aEurotextMapping = $this->_getEurotextMapping();
        $sTable = getViewName('oxarticles', $aLangMapping[$aProject['LANG_ORIGIN']]);
        $s2Table = getViewName('oxartextends', $aLangMapping[$aProject['LANG_ORIGIN']]);
        $sJoinTable = 'ettm_project2article';
        $is_only_untranslated = (1 === intval($aProject['ONLY_UNTRANSLATED']));

        // 1. Get main page data
        $sPageQuery = "SELECT $sTable.*
            FROM $sTable
            JOIN $sJoinTable ON $sTable.OXID = $sJoinTable.OXARTICLEID
            WHERE $sJoinTable.OXID = ?";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sPageQuery, array($aItem['OXID']));

        // 2. Create export items
        $aReturn = array();
        if ($oRs !== false && $oRs->count() > 0) {
            $aTargetLanguages = unserialize($aProject['LANG_TARGET']);
            foreach ($aTargetLanguages as $sTargetLanguage) {
                if ($is_only_untranslated) {
                    $translated = $this->_isItemTranslated('oxarticles', $aLangMapping[$sTargetLanguage], $oRs->fields['OXID']);
                } else {
                    $translated = false;
                }

                $aItemT = array(
                    'headers' => array(
                        'from' => $aEurotextMapping[$aProject['LANG_ORIGIN']],
                        'to' => $aEurotextMapping[$sTargetLanguage],
                        'textType' => 'product',
                    ),
                    'innermeta' => array(
                        'join_id' => $aItem['OXID'],
                        'project_index' => $iProjectIndex,
                        'skip' => $translated,
                    ),
                    '__meta' => array(
                        'OXID' => $oRs->fields['OXID'],
                        'artnr' => $oRs->fields['OXARTNUM'],
                        'view' => 'oxarticles',
                        'item_table' => 'ettm_project2article',
                        'origin_language' => $aLangMapping[$aProject['LANG_ORIGIN']],
                        'origin_shop_id' => $oConfig->getShopId(),
                    ),
                );

                // Get Attributes Fields
                $aFields = $oConfig->getShopConfVar('articlesfields', $oConfig->getShopId(), 'module:translationmanager6');
                if (0 < count($aFields)) {
                    foreach ($aFields as $sField) {
                        $aItemT[$sField] = $oRs->fields[$sField];
                    }
                }
                $aReturn[] = $aItemT;
            }
        }

        return $aReturn;
    }

    /**
     * Create articles main items.
     *
     * @param array $aProject      Projects array
     * @param int   $iProjectIndex Items array
     * @param array $aItem
     *
     * @return array
     */
    protected function _prepareArticlesExtends($aProject, $iProjectIndex, $aItem)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aLangMapping = $this->_getLangCodesMapping();
        $aInverseLangMapping = $this->_getInverseLangCodesMapping();
        $aEurotextMapping = $this->_getEurotextMapping();
        $sTable = getViewName('oxarticles', $aLangMapping[$aProject['LANG_ORIGIN']]);
        $s2Table = getViewName('oxartextends', $aLangMapping[$aProject['LANG_ORIGIN']]);
        $sJoinTable = 'ettm_project2article';
        $is_only_untranslated = (1 === intval($aProject['ONLY_UNTRANSLATED']));

        // 3. Create oxartextends
        $s2PageQuery = "SELECT $s2Table.*
            FROM $s2Table
            JOIN $sJoinTable ON $s2Table.OXID = $sJoinTable.OXARTICLEID
            WHERE $sJoinTable.OXID = ?";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($s2PageQuery, array($aItem['OXID']));

        // 4. Create export items
        $aReturn = array();
        if ($oRs !== false && $oRs->count() > 0) {
            $aTargetLanguages = unserialize($aProject['LANG_TARGET']);
            foreach ($aTargetLanguages as $sTargetLanguage) {
                if ($is_only_untranslated) {
                    $translated = $this->_isItemTranslated('oxartextends', $aLangMapping[$sTargetLanguage], $oRs->fields['OXID'], 'OXLONGDESC');
                } else {
                    $translated = false;
                }

                $aItemT = array(
                    'headers' => array(
                        'from' => $aEurotextMapping[$aProject['LANG_ORIGIN']],
                        'to' => $aEurotextMapping[$sTargetLanguage],
                        'textType' => 'product',
                    ),
                    'innermeta' => array(
                        'join_id' => $aItem['OXID'],
                        'project_index' => $iProjectIndex,
                        'skip' => $translated,
                    ),
                    '__meta' => array(
                        'OXID' => $oRs->fields['OXID'],
                        'view' => 'oxartextends',
                        'item_table' => 'ettm_project2article',
                        'origin_language' => $aLangMapping[$aProject['LANG_ORIGIN']],
                        'origin_shop_id' => $oConfig->getShopId(),
                    ),
                );

                // Get Attributes Fields
                $aFields = $oConfig->getShopConfVar('artextendsfields', $oConfig->getShopId(), 'module:translationmanager6');
                if (0 < count($aFields)) {
                    foreach ($aFields as $sField) {
                        $aItemT[$sField] = $oRs->fields[$sField];
                    }
                }
                $aReturn[] = $aItemT;
            }
        }

        return $aReturn;
    }

    /**
     * Query projects for export.
     *
     * @param array $aProjects Reference to projects array.
     */
    protected function _queryProjects(&$aProjects)
    {
        $sTable = 'ettm_project';
        $sProjectQuery = "SELECT * FROM $sTable WHERE $sTable.STATUS = 30";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sProjectQuery, array());

        if ($oRs !== false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $aProject = $oRs->fields;
                $aProject['DIRTY'] = false; // set to true, if any item in this project has been exported.
                $aProject['skipped'] = 0;
                $aProject['cms_items'] = array();
                $aProject['category_items'] = array();
                $aProject['attribute_items'] = array();
                $aProject['article_items'] = array();
                $aProjects[] = $aProject;
                $oRs->fetchRow();
            }
        }
    }

    /**
     * Query element for export
     *
     * @param string $name       Entitiry name.
     * @param array  $aProjects  Reference to projects array.
     * @param int    $maxExports Max ammount of exports.
     */
    protected function _queryElements($name, &$aProjects, &$maxExports)
    {
        foreach ($aProjects as $iProjectIndex => $aProject) {
            $sTable = 'ettm_project2' . $name;
            $sCMSQuery = "SELECT * FROM $sTable WHERE $sTable.STATUS = 0 AND $sTable.PROJECT_ID = ?";
            $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sCMSQuery, array($aProject['OXID']));
            if ($oRs !== false && $oRs->count() > 0) {
                while (!$oRs->EOF && (0 < $maxExports)) {
                    $aProjects[$iProjectIndex][$name . '_items'][] = $oRs->fields;
                    $oRs->fetchRow();
                    $maxExports--;
                }
            }
        }
    }

    /**
     * Check if element already has a translation.
     *
     * @param string $sTableName    Name of the target table or view.
     * @param string $sLanguageCode Code of the target language. e.g. 'de'.
     * @param string $sOxid         Id of the element.
     * @param string $sControlField Which field check for translation..
     *
     * @return bool
     */
    protected function _isItemTranslated($sTableName, $sLanguageCode, $sOxid, $sControlField = 'OXTITLE')
    {
        $sTable = getViewName($sTableName, $sLanguageCode);

        if ('oxobject2seodata' === $sTableName) {
            $sIdField = 'OXOBJECTID';
        } else {
            $sIdField = 'OXID';
        }

        $sQuery = "SELECT * FROM $sTable WHERE $sTable.$sIdField = ? AND $sTable.$sControlField = ''";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sQuery, array($sOxid));

        if ($oRs !== false && $oRs->count() > 0) {
            return false;
        } else {
            return true;
        }
    }
}
