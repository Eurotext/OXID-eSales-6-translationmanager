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
        $aProjects = [];
        $this->_queryProjects($aProjects);
        echo '<pre>';
        print_r($aProjects);
        echo '</pre>';

        echo '<h2>Exportierbare Items vorbereiten</h2>';
        // Get articles
        $this->_getItems(
            $aProjects,
            $maxExports,
            'oxarticles',
            'product',
            'articlesfields',
            'OXID',
            'OXTITLE',
            'ettm_project2article',
            'OXARTICLEID'
        );

        // Get articleextends
        $this->_getItems(
            $aProjects,
            $maxExports,
            'oxartextends',
            'product',
            'artextendsfields',
            'OXID',
            'OXLONGDESC',
            'ettm_project2article',
            'OXARTICLEID'
        );

        // Get articleseo
        $this->_getItems(
            $aProjects,
            $maxExports,
            'oxobject2seodata',
            'marketing',
            'articleseofields',
            'OXOBJECTID',
            'OXDESCRIPTION',
            'ettm_project2article',
            'OXARTICLEID'
        );

        // Get categories
        $this->_getItems(
            $aProjects,
            $maxExports,
            'oxcategories',
            'specialized-text',
            'categoryfields',
            'OXID',
            'OXTITLE',
            'ettm_project2category',
            'OXCATEGORYID'
        );

        // Get categoriesseo
        $this->_getItems(
            $aProjects,
            $maxExports,
            'oxobject2seodata',
            'marketing',
            'categoryseofields',
            'OXOBJECTID',
            'OXDESCRIPTION',
            'ettm_project2category',
            'OXCATEGORYID'
        );

        // Get cms.
        $this->_getItems(
            $aProjects,
            $maxExports,
            'oxcontents',
            'marketing',
            'cmsfields',
            'OXID',
            'OXTITLE',
            'ettm_project2cms',
            'OXCMSID'
        );

        // Get cms seo.
        $this->_getItems(
            $aProjects,
            $maxExports,
            'oxobject2seodata',
            'marketing',
            'cmsseofields',
            'OXOBJECTID',
            'OXDESCRIPTION',
            'ettm_project2cms',
            'OXCMSID'
        );

        // Get attribute names.
        $this->_getItems(
            $aProjects,
            $maxExports,
            'oxattribute',
            'term',
            'attributesfields',
            'OXID',
            'OXTITLE',
            'ettm_project2attribute',
            'OXATTRIBUTEID'
        );

        // Get attribute values.
        $this->_getItems(
            $aProjects,
            $maxExports,
            'oxobject2attribute',
            'term',
            'o2attributesfields',
            'OXATTRID',
            'OXVALUE',
            'ettm_project2attribute',
            'OXATTRIBUTEID'
        );

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
                []
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
                            $headers = [
                                'Content-Type' => 'application/json',
                                'apikey' => $sAPIKEY,
                            ];
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
                                    [
                                        'headers' => $headers,
                                        'json' => $aExportableArray,
                                    ]
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

                            $sUpdatedExportItems[$aUnderItem['__meta']['export_item_table']][] = [
                                $aUnderItem['__meta']['project_item_id'],
                                10,
                                $iExportable,
                                $iSkip,
                                $iWasTransmitted,
                                $iWasFailed,
                            ];
                        }
                    }
                }
            }
        }
    }

    /**
     * Prepares the items of the project.
     *
     * @param array  $aProjects                 Array with projects.
     * @param int    $iMaxExports               How many exports can be done in the cronjob.
     * @param string $viewName                  Name of the view table of the item table.
     * @param string $textType                  What kind of type is the text. that is important for eurotext, to correctly translate it.
     * @param string $settingName               What fields should be exported.
     * @param string $idFieldName               What is the name of the column that stores ids.
     * @param string $translationCheckFieldName In which column to look whether the item is translated.
     * @param string $joinTableName             What table to join on item table.
     * @param string $joinTableFieldName        What field to use for join.
     */
    protected function _getItems(&$aProjects, $iMaxExports, $viewName, $textType, $settingName, $idFieldName, $translationCheckFieldName, $joinTableName, $joinTableFieldName)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        if (isset($_GET['shopId'])) {
            $iShopId = intval($_GET['shopId']);
        } else {
            $iShopId = $oConfig->getShopId();
        }

        $maxExports = intval($oConfig->getShopConfVar('sEXPORTJOBIPJ', $iShopId, 'module:translationmanager6'));
        $aExportableItems = [];
        $aLangMapping = $this->_getLangCodesMapping();
        // Exportable fields setting.
        $aItemFields = $oConfig->getShopConfVar($settingName, $iShopId, 'module:translationmanager6');

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
                } else {
                    $sItemJoinTranslationsTable .= "LEFT JOIN $sTempTable AS {$sTempTable}_{$aTargetLang} ON {$sTempTable}_{$aTargetLang}.`{$idFieldName}` = main_table.`{$idFieldName}` \n";
                }
            }

            $sQuery = "
                SELECT `{$joinTableName}`.`OXID` AS `PROJECT_ITEM_ID`,  '{$textType}' AS `type` $aItemSelects $sItemSelectTranslationsTable
                FROM `{$joinTableName}`
                JOIN $sItemTable AS main_table ON main_table.`{$idFieldName}` = `{$joinTableName}`.`{$joinTableFieldName}`
                $sItemJoinTranslationsTable
                WHERE `{$joinTableName}`.`PROJECT_ID` = '$sProjectId' AND `{$joinTableName}`.`STATUS` = 0
                LIMIT {$iMaxExports}
            ";

            $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sQuery, [$aProject['OXID']]);

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
                $translations = [];
                foreach ($aTargetLangs as $aTargetLang) {
                    $translations[$aTargetLang] = intval($aExportableItem['translated_'.$aTargetLang]);
                    unset($aExportableItem['translated_'.$aTargetLang]);
                }

                foreach ($aTargetLangs as $aTargetLang) {
                    $aProject['ITEMS'][$sOriginLang][$aTargetLang][$type][] = array_merge(
                        [
                            '__meta' => [
                                'project_item_id' =>  $id,
                                'export_item_table' => $joinTableName,
                                'oxid_item_id' => $oxid,
                                'oxid_shop_id' => $iShopId,
                                'oxid_item_table' => $viewName,
                                'target_lang' => $aTargetLang,
                                'skip' => ($bOnlyTranslated && (1 === $translations[$aTargetLang])) ? 1 : 0
                            ]
                        ],
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
        $aRetArray = [];
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
        $aRetArray = [];
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
    protected function _queryProjects(&$aProjects)
    {
        $sTable = 'ettm_project';
        $sProjectQuery = "SELECT * FROM $sTable WHERE $sTable.STATUS = 30";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sProjectQuery, []);

        if ($oRs !== false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $aProject = $oRs->fields;
                $aProject['DIRTY'] = false; // set to true, if any item in this project has been exported.
                $aProject['skipped'] = 0;
                $aProject['ITEMS'] = [];
                $aProject['ITEMS'][$aProject['LANG_ORIGIN']] = [];
                $aTargetLangs = unserialize($aProject['LANG_TARGET']);
                foreach ($aTargetLangs as $aTargetLang) {
                    $aProject['ITEMS'][$aProject['LANG_ORIGIN']][$aTargetLang] = [
                        'specialized-text' => [],
                        'marketing' => [],
                        'term' => [],
                        'product' => []
                    ];
                }
                $aProjects[] = $aProject;
                $oRs->fetchRow();
            }
        }
    }
}
