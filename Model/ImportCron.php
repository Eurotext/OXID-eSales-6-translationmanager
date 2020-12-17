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
class ImportCron extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    protected $whitelist = [
    ];

    public function __construct() {

        // Get list of cms fields
        $sQuery = "SHOW COLUMNS FROM oxcontents WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            array()
        );
        foreach ($resultSet as $result) {
            $this->whitelist[] = $result[0];
        }

        $sQuery = "SHOW COLUMNS FROM oxcategories WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            array()
        );
        foreach ($resultSet as $result) {
            $this->whitelist[] = $result[0];
        }

        $sQuery = "SHOW COLUMNS FROM oxattribute WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            array()
        );
        foreach ($resultSet as $result) {
            $this->whitelist[] = $result[0];
        }

        $sQuery = "SHOW COLUMNS FROM oxobject2attribute WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            array()
        );
        foreach ($resultSet as $result) {
            $this->whitelist[] = $result[0];
        }

        $sQuery = "SHOW COLUMNS FROM oxarticles WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            array()
        );
        foreach ($resultSet as $result) {
            $this->whitelist[] = $result[0];
        }

        $sQuery = "SHOW COLUMNS FROM oxartextends WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            array()
        );
        foreach ($resultSet as $result) {
            $this->whitelist[] = $result[0];
        }

        $sQuery = "SHOW COLUMNS FROM oxobject2seodata WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXOBJECTID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            array()
        );
        foreach ($resultSet as $result) {
            $this->whitelist[] = $result[0];
        }
    }

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
        $maxImports = intval($oConfig->getShopConfVar('sIMPORTJOBIPJ', $iShopId, 'module:translationmanager6'));

        $aUpdateProjects = array();
        echo '<h1>Importablauf</h1>';

        // 1. Query all untouched jobs

        echo '<h2>Import-JOBS vorbereiten</h2>';
        $aJobs = array();
        $this->_queryJobs($aJobs, $maxImports, $iShopId);
        echo '<pre>';
        print_r($aJobs);
        echo '<pre>';

        echo '<h2>Import-Details herunterladen</h2>';

        // 2. Get all data
        foreach ($aJobs as $index => $aJob) {
            $this->_getItemDetails($aJobs, $index);
        }
        echo '<pre>';
        print_r($aJobs);
        echo '<pre>';

        // 3. Write to database
        foreach ($aJobs as $index => $aJob) {
            $this->_rewriteTableRows($aJob, $aUpdateProjects);
        }

        // 4. Update proejects progress
        $this->_updateProjectsProgress($aUpdateProjects);
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
        foreach ($aProjects as $aProjectId) {
            $oProject = oxNew('\Eurotext\Translationmanager\Model\Project');
            $oProject->load($aProjectId);
            $oProject->updateImportProgress();
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
     * Returns selected lang codes from eurotext database.
     *
     * @return array
     */
    protected function _getEurotextMapping()
    {
        $oMapping = oxNew('\Eurotext\Translationmanager\Model\Mapping');
        return $oMapping->getReverseMapping();
    }

    /**
     * Returns all item infos, including translated data.
     *
     * @param array $aJob            Some job infos.
     * @param array $aUpdateProjects List of project that need to update their status..
     *
     * @return void
     */
    protected function _rewriteTableRows($aJob, &$aUpdateProjects)
    {
        // 0. Get some helpers
        $aCodesMapping = $this->_getLangCodesMapping();

        // For each item.
        foreach ($aJob['BODY']['data'] as $aItem) {
            $iTargetLanguageCode = $aCodesMapping[$aItem['__meta']['target_lang']];
            $iShopId = $aItem['__meta']['oxid_shop_id'];
            $sBaseTargetTable = $aItem['__meta']['oxid_item_table'];
            $sTargetTable = getViewName($sBaseTargetTable, $iTargetLanguageCode, $iShopId);
            $sTargetOxid = $aItem['__meta']['oxid_item_id'];
            $oxSecondaryId = $aItem['__meta']['oxid_secondary_id'];
            unset($aItem['__meta']);

            if (0 < count($aItem)) {
                echo "Import to table: " . $sTargetTable . "<br>";
                echo '<pre>';
                print_r($aItem);
                echo '</pre>';
            } else {
                echo "Nothing to import. Skip<br>";
            }

            $aData = $aItem;

            $aUpdateFields = array();
            $aParams = array();
            if (0 === count($aData)) {
                // 2. Mark job as done with error
                \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute(
                    "UPDATE `ettm_importjobs` SET STATUS=10 WHERE OXID = ?",
                    array($aJob['OXID'])
                );
                // 3. Add project for status update
                $sProjectId = $aJob['PROJECT_ID'];
                if (!in_array($sProjectId, $aUpdateProjects)) {
                    $aUpdateProjects[] = $sProjectId;
                }
                return;
            }
            foreach ($aData as $sKey => $sValue) {
                // If the the key is whitelisted
                if (in_array($sKey, $this->whitelist)) {
                    $aUpdateFields[] = $sKey . ' = ?';
                    $aParams[] = $sValue;
                }
            }
            $sUpdateFields = implode(', ', $aUpdateFields);
            $aParams[] = $sTargetOxid;

            // 1. Write to table only if there are updateable fields.
            if (0 < count($aUpdateFields) ) {
                if ('oxobject2seodata' === $sTargetTable) {
                    $aParams[] = $iShopId;
                    $aParams[] = $iTargetLanguageCode;

                    \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute(
                        "UPDATE $sTargetTable SET $sUpdateFields WHERE OXOBJECTID = ? AND OXSHOPID= ? AND OXLANG = ?",
                        $aParams
                    );
                } elseif ('oxobject2attribute' === $sBaseTargetTable) {
                    $aParams[] = $oxSecondaryId;

                    \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute(
                        "UPDATE $sTargetTable SET $sUpdateFields WHERE OXOBJECTID = ? AND OXATTRID = ?",
                        $aParams
                    );
                } else {
                    \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute(
                        "UPDATE $sTargetTable SET $sUpdateFields WHERE OXID = ?",
                        $aParams
                    );
                }

                // 2. Mark job as done
                \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute(
                    "UPDATE `ettm_importjobs` SET STATUS=10 WHERE OXID = ?",
                    array($aJob['OXID'])
                );
            }

            // 3. Add project for status update
            $sProjectId = $aJob['PROJECT_ID'];
            if (!in_array($sProjectId, $aUpdateProjects)) {
                $aUpdateProjects[] = $sProjectId;
            }
        }
    }

    /**
     * Returns all item infos, including translated data.
     *
     * @param array $aJobs Some job infos.
     * @param int   $index Index of the job.
     */
    protected function _getItemDetails(&$aJobs, $index)
    {
        $aJob = $aJobs[$index];
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        if (isset($_GET['shopId'])) {
            $iShopId = intval($_GET['shopId']);
        } else {
            $iShopId = $oConfig->getShopId();
        }
        $sAPIKEY = $oConfig->getShopConfVar('sAPIKEY', $iShopId, 'module:translationmanager6');
        $sSERVICEURL = $oConfig->getShopConfVar('sSERVICEURL', $iShopId, 'module:translationmanager6');

        $iExternalProjectid = $aJob['EXTERNAL_PROJECT_ID'];
        $iExternalItemId = $aJob['EXTERNAL_ID'];

        $uri = '/api/v1/project/' . $iExternalProjectid . '/item/' . $iExternalItemId . '.json';
        $headers = array(
            'Content-Type' => 'application/json',
            'apikey' => $sAPIKEY,
        );
        $client = new \GuzzleHttp\Client([
            'base_uri' => $sSERVICEURL,
            'timeout'  => 2.0,
        ]);

        try {
            $oResponse = $client->get(
                $uri,
                array(
                    'headers' => $headers
                )
            );
            $aJobs[$index]['BODY'] = json_decode($oResponse->getBody(), true);
            $aJobs[$index]['HEADERS'] = $oResponse->getHeaders();
            $aJobs[$index]['QUERY_STATUS'] = 'success';
        } catch (\Exception $e) {
            // Do nothing
            $aJobs[$index]['QUERY_STATUS'] = 'fail';
        }
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
     * Query unimported jobs.
     *
     * @param array $aJobs      List of Jobs.
     * @param int   $maxImports Max imports per call.
     */
    protected function _queryJobs(&$aJobs, &$maxImports, $iShopId = null)
    {
        if (!$iShopId) {
            if (isset($_GET['shopId'])) {
                $iShopId = intval($_GET['shopId']);
            } else {
                $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
                $iShopId = $oConfig->getShopId();
            }
        }

        $sTable = 'ettm_importjobs';
        $sProjectTable = 'ettm_project';
        $sQuery = "SELECT $sTable.* 
                    FROM $sTable 
                    JOIN $sProjectTable ON $sProjectTable.OXID = $sTable.PROJECT_ID
                    WHERE $sTable.STATUS = 0 AND $sProjectTable.OXSHOPID = ? AND $sProjectTable.STATUS = 70
                    LIMIT $maxImports";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sQuery, array($iShopId));

        if ($oRs !== false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $aJob = $oRs->fields;
                $aJobs[] = $aJob;
                $oRs->fetchRow();
            }
        }
    }
}
