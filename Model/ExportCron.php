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

        if (isset($_GET['shopId'])) {
            $iShopId = intval($_GET['shopId']);
        } else {
            $iShopId = $oConfig->getShopId();
        }

        $maxExports = intval($oConfig->getShopConfVar('sEXPORTJOBIPJ', $iShopId, 'module:translationmanager6'));

        echo "<h1>Exportablauf für Shop {$iShopId}</h1>";
        echo '<h2>Projektobjekte vorbereiten</h2>';

        // 1. Query all project with status 30
        $aProjects = array();
        $this->_queryProjects($aProjects, $iShopId);
        echo '<pre>';
        print_r($aProjects);
        echo '</pre>';

        echo '<h2>Exportierbare Items vorbereiten</h2>';

        // 1. Prepare articles join ids
        $groups = [
            'articles' => [],
            'cms' => [],
            'categories' => [],
            'attributes' => [],
        ];
        $this->_getItemsGroups(
            $aProjects,
            $maxExports,
            $groups
        );

        echo '<h2>Vorbereitete Elementengruppen</h2>';
        echo '<pre>';
        print_r($groups);
        echo '</pre>';

        // Get data.
        $this->_getItems(
            $aProjects,
            $groups['articles'],
            'oxarticles',
            'product',
            'articlesfields',
            'OXID',
            'OXTITLE',
            'ettm_project2article',
            'OXARTICLEID'
        );

        $this->_getItems(
            $aProjects,
            $groups['articles'],
            'oxartextends',
            'product',
            'artextendsfields',
            'OXID',
            'OXLONGDESC',
            'ettm_project2article',
            'OXARTICLEID'
        );

        $this->_getItems(
            $aProjects,
            $groups['articles'],
            'oxobject2attribute',
            'term',
            'o2attributesfields',
            'OXOBJECTID',
            'OXATTRID',
            'ettm_project2article',
            'OXARTICLEID'
        );

        $this->_getItems(
            $aProjects,
            $groups['articles'],
            'oxobject2seodata',
            'marketing',
            'articleseofields',
            'OXOBJECTID',
            'OXDESCRIPTION',
            'ettm_project2article',
            'OXARTICLEID'
        );

        $this->_getItems(
            $aProjects,
            $groups['categories'],
            'oxcategories',
            'specialized-text',
            'categoryfields',
            'OXID',
            'OXTITLE',
            'ettm_project2category',
            'OXCATEGORYID'
        );

        $this->_getItems(
            $aProjects,
            $groups['categories'],
            'oxobject2seodata',
            'marketing',
            'categoryseofields',
            'OXOBJECTID',
            'OXDESCRIPTION',
            'ettm_project2category',
            'OXCATEGORYID'
        );

        $this->_getItems(
            $aProjects,
            $groups['cms'],
            'oxcontents',
            'marketing',
            'cmsfields',
            'OXID',
            'OXTITLE',
            'ettm_project2cms',
            'OXCMSID'
        );

        $this->_getItems(
            $aProjects,
            $groups['cms'],
            'oxcontents',
            'marketing',
            'cmsfields',
            'OXID',
            'OXTITLE',
            'ettm_project2cms',
            'OXCMSID'
        );

        $this->_getItems(
            $aProjects,
            $groups['cms'],
            'oxobject2seodata',
            'marketing',
            'cmsseofields',
            'OXOBJECTID',
            'OXDESCRIPTION',
            'ettm_project2cms',
            'OXCMSID'
        );

        $this->_getItems(
            $aProjects,
            $groups['attributes'],
            'oxattribute',
            'term',
            'attributesfields',
            'OXID',
            'OXTITLE',
            'ettm_project2attribute',
            'OXATTRIBUTEID'
        );

        echo '<h2>Projects before export</h2>';
        echo '<pre>';
        print_r($aProjects);
        echo '</pre>';

        echo '<h2>Batch Export</h2>';
        $sUpdatedExportItems = [];
        $this->_batchExport($aProjects, $sUpdatedExportItems);
        echo '<pre>';
        print_r($aProjects);
        echo '</pre>';

        echo '<h2>Batch Statusupdate</h2>';
        $this->_updateStatus($sUpdatedExportItems);

        echo '<h2>Update project progress</h2>';
        $this->_updateProjectsProgress($aProjects);

    }

    /**
     * Updates the status of the projekt item.
     *
     * @param array $sUpdatedExportItems An array of items to be exported.
     */
    protected function _updateStatus($sUpdatedExportItems)
    {

        foreach ($sUpdatedExportItems as $sTableName => $aValues) {
            echo '<pre>';
            $sql = '';
            $sql .= "INSERT INTO `{$sTableName}` ( `OXID`, `STATUS`, `EXPORTABLE`, `SKIPPED`, `TRANSMITTED`, `FAILED` ) VALUES \n";
            $tempArray = [];
            foreach ($aValues as $value) {
                $filter = function ($singleValue) {
                    return '\'' . $singleValue . '\'';
                };
                $filteredValues = array_map($filter, $value);
                $tempArray[] = '(' . implode(',', $filteredValues) . ')';
            }
            $sql .= implode(",\n", $tempArray);
            $sql .= "\nON DUPLICATE KEY UPDATE 
                `STATUS`=VALUES(`STATUS`), 
                `EXPORTABLE`=VALUES(`EXPORTABLE`), 
                `SKIPPED`=VALUES(`SKIPPED`), 
                `TRANSMITTED`=VALUES(`TRANSMITTED`), 
                `FAILED`=VALUES(`FAILED`);";
            echo $sql;
            echo '</pre>';

            // Execute.
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute(
                $sql,
                array()
            );
        }
    }

    /**
     * Export collected items in batch to eurotext api.
     *
     * @param array $aProjects
     * @param array $sUpdatedExportItems
     */
    protected function _batchExport(&$aProjects, &$sUpdatedExportItems)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        if (isset($_GET['shopId'])) {
            $iShopId = intval($_GET['shopId']);
        } else {
            $iShopId = $oConfig->getShopId();
        }

        $sAPIKEY = $oConfig->getShopConfVar('sAPIKEY', $iShopId, 'module:translationmanager6');
        $sSERVICEURL = $oConfig->getShopConfVar('sSERVICEURL', $iShopId, 'module:translationmanager6');
        $aEurotextMapping = $this->_getEurotextMapping();

        foreach ($aProjects as &$aProject) {
            $aItems = $aProject['ITEMS'];
            $sExternalProjectId = $aProject['EXTERNAL_ID'];
            foreach ($aItems as $sOriginLang => $sOriginLangValues) {
                foreach ($sOriginLangValues as $sTargetLang => $sTargetLangValues) {
                    foreach ($sTargetLangValues as $sItemtype => $aItem) {
                        $aProject['DIRTY'] = true;
                        $aProject['DIRTY_status'] = 'status_should_be_updated';

                        // 1. Get candidates to send to remote.
                        $aExportableArray = [];
                        foreach ($aItem as $aUnderItem) {
                            if (!$aUnderItem['__meta']['skip']) {
                                $aExportableArray[] = $aUnderItem;
                            }
                        }

                        $bExport = true;
                        if (0 < count($aExportableArray)) {
                            echo "Export item from translation from {$sOriginLang} to {$sTargetLang} of type {$sItemtype} to project {$sExternalProjectId}<br>";
                        } else {
                            echo "No items to export to translation from {$sOriginLang} to {$sTargetLang} of type {$sItemtype} to project {$sExternalProjectId}<br>";
                            $bExport = false;
                        }

                        if ($bExport) {
                            // 2. Send them to remote.
                            $uri = '/api/v1/project/' . $sExternalProjectId . '/item.json';
                            $headers = array(
                                'Content-Type' => 'application/json',
                                'apikey' => $sAPIKEY,
                            );
                            $client = new \GuzzleHttp\Client([
                                'base_uri' => $sSERVICEURL,
                                'timeout'  => 2.0,
                            ]);
                            try {
                                $headers['X-Source'] = $aEurotextMapping[$sOriginLang];
                                $headers['X-Target'] = $aEurotextMapping[$sTargetLang];
                                $headers['X-TextType'] = $sItemtype;
                                $client->post(
                                    $uri,
                                    array(
                                        'headers' => $headers,
                                        'json' => $aExportableArray,
                                    )
                                );
                                $iTransmitted = 1;
                                $iFailed = 0;
                                echo 'Successfully uploaded <br>';
                            } catch (\Exception $e) {
                                // Do nothing
                                $iTransmitted = 0;
                                $iFailed = 1;
                                echo 'Error with upload: '.$e->getMessage().'<br>';
                            }
                        }

                        // Go deeper to gatherup all under items.
                        foreach ($aItem as $aUnderItem) {
                            if (!isset($sUpdatedExportItems[$aUnderItem['__meta']['export_item_table']])) {
                                $sUpdatedExportItems[$aUnderItem['__meta']['export_item_table']] = [];
                            }

                            // Fields
                            // ID
                            // STATUS
                            // EXPORTABLE
                            // SKIPPED
                            // TRANSMITTED
                            // FAILED
                            $iSkip = (1 === $aUnderItem['__meta']['skip'])?1:0;
                            $iExportable = (0 === $aUnderItem['__meta']['skip'])?1:0;
                            $iWasTransmitted = ($iExportable && $iTransmitted)?1:0;
                            $iWasFailed = ($iExportable && $iFailed)?1:0;

                            $sUpdatedExportItems[$aUnderItem['__meta']['export_item_table']][] = array(
                                $aUnderItem['__meta']['project_item_id'],
                                10,
                                $iExportable,
                                $iSkip,
                                $iWasTransmitted,
                                $iWasFailed,
                            );
                        }
                    }
                }
            }
        }
    }


    protected function _getItemsGroups(&$aProjects, $iMaxExports, &$groups) {

        // Get shopid.
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        if (isset($_GET['shopId'])) {
            $iShopId = intval($_GET['shopId']);
        } else {
            $iShopId = $oConfig->getShopId();
        }

        // Get a list of not yet processed elements.
        $aArticleIds = [];
        $aCmsIds = [];
        $aCategoryIds = [];
        $aAttributeIds = [];
        $totalElementCount = 0;

        // Try to find articles that need to be processed.
        if ($totalElementCount < $iMaxExports) {
            $counter = 0;
            foreach ($aProjects as $aProject) {
                if ($totalElementCount < $iMaxExports) {
                    $sProjectId = $aProject['OXID'];
                    $limit = $iMaxExports - $totalElementCount;
                    $sql = "SELECT * FROM `ettm_project2article` WHERE `STATUS` = 0 AND `PROJECT_ID` = '{$sProjectId}' LIMIT {$limit}";
                    $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sql, []);
                    if ($oRs !== false && $oRs->count() > 0) {
                        while (!$oRs->EOF) {
                            $aArticleIds[] = $oRs->fields;
                            $counter++;
                            $totalElementCount++;
                            $oRs->fetchRow();
                        }
                        break;
                    }
                }
            }

            echo "<h2>Found {$counter} articles to export (unless skipped)</h2>";
            echo '<pre>';
            print_r($aArticleIds);
            echo '</pre>';
        }

        // If no articles found, look for categories.
        if ($totalElementCount < $iMaxExports) {
            $counter = 0;
            foreach ($aProjects as $aProject) {
                if ($totalElementCount < $iMaxExports) {
                    $sProjectId = $aProject['OXID'];
                    $limit = $iMaxExports - $totalElementCount;
                    $sql = "SELECT * FROM `ettm_project2category` WHERE `STATUS` = 0 AND `PROJECT_ID` = '{$sProjectId}' LIMIT {$limit}";
                    $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sql, []);
                    if ($oRs !== false && $oRs->count() > 0) {
                        while (!$oRs->EOF) {
                            $aCategoryIds[] = $oRs->fields;
                            $counter++;
                            $totalElementCount++;
                            $oRs->fetchRow();
                        }
                        break;
                    }
                }
            }

            echo "<h2>Found {$counter} categories to export (unless skipped)</h2>";
            echo '<pre>';
            print_r($aCategoryIds);
            echo '</pre>';
        }

        // Look for cms pages.
        if ($totalElementCount < $iMaxExports) {
            $counter = 0;
            foreach ($aProjects as $aProject) {
                if ($totalElementCount < $iMaxExports) {
                    $sProjectId = $aProject['OXID'];
                    $limit = $iMaxExports - $totalElementCount;
                    $sql = "SELECT * FROM `ettm_project2cms` WHERE `STATUS` = 0 AND `PROJECT_ID` = '{$sProjectId}' LIMIT {$limit}";
                    $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sql, []);
                    if ($oRs !== false && $oRs->count() > 0) {
                        while (!$oRs->EOF) {
                            $aCmsIds[] = $oRs->fields;
                            $counter++;
                            $totalElementCount++;
                            $oRs->fetchRow();
                        }
                        break;
                    }
                }
            }

            echo "<h2>Found {$counter} cms pages to export (unless skipped)</h2>";
            echo '<pre>';
            print_r($aCmsIds);
            echo '</pre>';
        }

        // Prepare attributes.
        if ($totalElementCount < $iMaxExports) {
            $counter = 0;
            foreach ($aProjects as $aProject) {
                if ($totalElementCount < $iMaxExports) {
                    $sProjectId = $aProject['OXID'];
                    $limit = $iMaxExports - $totalElementCount;
                    $sql = "SELECT * FROM `ettm_project2attribute` WHERE `STATUS` = 0 AND `PROJECT_ID` = '{$sProjectId}' LIMIT {$limit}";
                    $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sql, []);
                    if ($oRs !== false && $oRs->count() > 0) {
                        while (!$oRs->EOF) {
                            $aAttributeIds[] = $oRs->fields;
                            $counter++;
                            $totalElementCount++;
                            $oRs->fetchRow();
                        }
                        break;
                    }
                }
            }

            echo "<h2>Found {$counter} attributes to export (unless skipped)</h2>";
            echo '<pre>';
            print_r($aAttributeIds);
            echo '</pre>';
        }


        $groups['articles'] = $aArticleIds;
        $groups['cms'] = $aCmsIds;
        $groups['categories'] = $aCategoryIds;
        $groups['attributes'] = $aAttributeIds;
    }

    /**
     * Prepares the items of the project.
     *
     * @param array  $aProjects                 Array with projects.
     * @param array  $group                     Ids group.
     * @param string $viewName                  Name of the view table of the item table.
     * @param string $textType                  What kind of type is the text. that is important for eurotext, to correctly translate it.
     * @param string $settingName               What fields should be exported.
     * @param string $idFieldName               What is the name of the column that stores ids.
     * @param string $translationCheckFieldName In which column to look whether the item is translated.
     * @param string $joinTableName             What table to join on item table.
     * @param string $joinTableFieldName        What field to use for join.
     */
    protected function _getItems(&$aProjects, $group, $viewName, $textType, $settingName, $idFieldName, $translationCheckFieldName, $joinTableName, $joinTableFieldName)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        if (isset($_GET['shopId'])) {
            $iShopId = intval($_GET['shopId']);
        } else {
            $iShopId = $oConfig->getShopId();
        }

        if (0 === count($group)) {
            echo 'Gruppe ist leer. Return' . "<br>";
            return;
        }

        $ids = [];
        foreach ($group as $element) {
            if (!array_key_exists($element['PROJECT_ID'], $ids)) {
                $ids[$element['PROJECT_ID']] = [];
            }
            $ids[$element['PROJECT_ID']][] = '\'' . $element['OXID'] . '\'';
        }
        foreach ($ids as &$projectids) {
            $projectids = implode(',', $projectids);
        }

        $aExportableItems = array();
        $aLangMapping = $this->_getLangCodesMapping();
        // Exportable fields setting.
        $aItemFields = $oConfig->getShopConfVar($settingName, $iShopId, 'module:translationmanager6');

        if (0 === count($aItemFields)) {
            echo 'Keine Felder für Export ausgewählt. Return. <br>';
            return;
        }

        foreach ($aProjects as &$aProject) {
            $sProjectId = $aProject['OXID'];

            $sOriginLang = $aProject['LANG_ORIGIN'];
            $aTargetLangs = unserialize($aProject['LANG_TARGET']);
            $bOnlyTranslated = (1 === intval($aProject['ONLY_UNTRANSLATED']));

            // Item handler area
            $sItemTable = getViewName($viewName, $aLangMapping[$sOriginLang], $iShopId);
            $aItemSelects = ', main_table.'.$idFieldName;
            if (0 < count($aItemFields)) {
                foreach ($aItemFields as $aItemField) {
                    $aItemSelects .= ", main_table." . $aItemField;
                }
            }

            if ('oxobject2attribute' === $viewName) {
                $aItemSelects .= ", main_table.OXATTRID";
            }

            $sItemSelectTranslationsTable = '';
            $sItemJoinTranslationsTable = '';
            foreach ($aTargetLangs as $aTargetLang) {
                $sTempTable = getViewName($viewName, $aLangMapping[$aTargetLang], $iShopId);
                $sItemSelectTranslationsTable .= ", (CASE 
                    WHEN {$sTempTable}_{$aTargetLang}.`{$translationCheckFieldName}` = '' THEN 0
                    WHEN {$sTempTable}_{$aTargetLang}.`{$translationCheckFieldName}` IS NULL THEN 0
                    ELSE 1
                END) AS translated_$aTargetLang";

                if ('oxobject2seodata' === $viewName) {
                    $sItemJoinTranslationsTable .= "LEFT JOIN $sTempTable AS {$sTempTable}_{$aTargetLang} ON {$sTempTable}_{$aTargetLang}.`{$idFieldName}` = main_table.`{$idFieldName}` AND {$sTempTable}_{$aTargetLang}.OXLANG='$aTargetLang' AND {$sTempTable}_{$aTargetLang}.OXSHOPID = '$iShopId' \n";
                }  else {
                    $sItemJoinTranslationsTable .= "LEFT JOIN $sTempTable AS {$sTempTable}_{$aTargetLang} ON {$sTempTable}_{$aTargetLang}.`{$idFieldName}` = main_table.`{$idFieldName}` \n";
                }
            }

            $sQuery = "
                SELECT `{$joinTableName}`.`OXID` AS `PROJECT_ITEM_ID`,  '{$textType}' AS `type` $aItemSelects $sItemSelectTranslationsTable
                FROM `{$joinTableName}`
                JOIN $sItemTable AS main_table ON main_table.`{$idFieldName}` = `{$joinTableName}`.`{$joinTableFieldName}`
                $sItemJoinTranslationsTable
                WHERE `{$joinTableName}`.`OXID` IN ({$ids[$sProjectId]})
            ";

            $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sQuery, array($aProject['OXID']));

            if ($oRs !== false && $oRs->count() > 0) {
                while (!$oRs->EOF) {
                    $aExportableItems[] = $oRs->fields;
                    $oRs->fetchRow();
                }
            }

            // Sort this items into projects array.
            foreach ($aExportableItems as $aExportableItem) {
                $id = $aExportableItem['PROJECT_ITEM_ID'];
                unset($aExportableItem['PROJECT_ITEM_ID']);
                $oxid = $aExportableItem[$idFieldName];
                unset($aExportableItem[$idFieldName]);
                $type = $aExportableItem['type'];
                unset($aExportableItem['type']);
                $translations = array();

                if (array_key_exists('OXATTRID', $aExportableItem)) {
                    $secondaryId = $aExportableItem['OXATTRID'];
                    unset($aExportableItem['OXATTRID']);
                } else {
                    $secondaryId = '';
                }

                foreach ($aTargetLangs as $aTargetLang) {
                    $translations[$aTargetLang] = intval($aExportableItem['translated_'.$aTargetLang]);
                    unset($aExportableItem['translated_'.$aTargetLang]);
                }

                foreach ($aTargetLangs as $aTargetLang) {
                    $aProject['ITEMS'][$sOriginLang][$aTargetLang][$type][] = array_merge(
                        array(
                            '__meta' => array(
                                'project_item_id' =>  $id,
                                'export_item_table' => $joinTableName,
                                'oxid_item_id' => $oxid,
                                'oxid_shop_id' => $iShopId,
                                'oxid_item_table' => $viewName,
                                'oxid_secondary_id' => $secondaryId,
                                'target_lang' => $aTargetLang,
                                'skip' => ($bOnlyTranslated && (1 === $translations[$aTargetLang])) ? 1 : 0
                            )
                        ),
                        $aExportableItem
                    );
                }
            }
        }
    }


    /**
     * Recalculates the progress of export for all projects.
     *
     * @param array $aProjects List of project
     *
     * @return void
     */
    protected function _updateProjectsProgress($aProjects)
    {
        foreach ($aProjects as $aProject) {
            // Only recalculate for projects that are marked as dirty
            $sId = $aProject['OXID'];
            echo "Time to test and Project {$sId} DIRTY Flag is " . (($aProject['DIRTY'])?'true':'false') . '<br>';
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
     * Query projects for export.
     *
     * @param array $aProjects Reference to projects array.
     */
    protected function _queryProjects(&$aProjects, $iShopId = null)
    {

        if (!$iShopId) {
            if (isset($_GET['shopId'])) {
                $iShopId = intval($_GET['shopId']);
            } else {
                $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
                $iShopId = $oConfig->getShopId();
            }
        }

        $sTable = 'ettm_project';
        $sProjectQuery = "SELECT * FROM $sTable WHERE $sTable.STATUS = 30 AND $sTable.OXSHOPID = ?";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sProjectQuery, array($iShopId));

        if ($oRs !== false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $aProject = $oRs->fields;
                $aProject['DIRTY'] = false; // set to true, if any item in this project has been exported.
                $aProject['skipped'] = 0;
                $aProject['ITEMS'] = array();
                $aProject['ITEMS'][$aProject['LANG_ORIGIN']] = array();
                $aTargetLangs = unserialize($aProject['LANG_TARGET']);
                foreach ($aTargetLangs as $aTargetLang) {
                    $aProject['ITEMS'][$aProject['LANG_ORIGIN']][$aTargetLang] = array(
                        'specialized-text' => array(),
                        'marketing' => array(),
                        'term' => array(),
                        'product' => array()
                    );
                }
                $aProjects[] = $aProject;
                $oRs->fetchRow();
            }
        }
    }
}
