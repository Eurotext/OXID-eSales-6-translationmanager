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

    /**
     * Standard cron executing function.
     */
    public function execute()
    {
        // 0. Get some options
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $maxImports = intval($oConfig->getShopConfVar('sIMPORTJOBIPJ', $oConfig->getShopId(), 'module:translationmanager6'));

        $aUpdateProjects = array();

        // 1. Query all untouched jobs
        $aJobs = array();
        $this->_queryJobs($aJobs, $maxImports);

        // 2. Get all data
        foreach ($aJobs as $index => $aJob) {
            $this->_getItemDetails($aJobs, $index);
        }

        echo '<pre>';
        print_r($aJobs);

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
        $aEurotextReverseMapping = $this->_getEurotextMapping();

        $targetLanguageCode = $aCodesMapping[$aEurotextReverseMapping[$aJob['HEADERS']['X-Target'][0]]];
        $sTargetTable = getViewName($aJob['BODY']['data']['__meta']['view'], $targetLanguageCode);
        $sTargetOxid = $aJob['BODY']['data']['__meta']['OXID'];
        $sShopId = $aJob['BODY']['data']['__meta']['origin_shop_id'];

        $aData = $aJob['BODY']['data'];
        unset($aData['__meta']);

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
            $aUpdateFields[] = $sKey . ' = ?';
            $aParams[] = $sValue;
        }
        $sUpdateFields = implode(', ', $aUpdateFields);
        $aParams[] = $sTargetOxid;

        // 1. Write to table
        if ('oxobject2seodata' === $sTargetTable) {
            $aParams[] = $sShopId;
            $aParams[] = $targetLanguageCode;

            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute(
                "UPDATE $sTargetTable SET $sUpdateFields WHERE OXOBJECTID = ? AND OXSHOPID= ? AND OXLANG = ?",
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

        // 3. Add project for status update
        $sProjectId = $aJob['PROJECT_ID'];
        if (!in_array($sProjectId, $aUpdateProjects)) {
            $aUpdateProjects[] = $sProjectId;
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
        $sAPIKEY = $oConfig->getShopConfVar('sAPIKEY', $oConfig->getShopId(), 'module:translationmanager6');
        $sSERVICEURL = $oConfig->getShopConfVar('sSERVICEURL', $oConfig->getShopId(), 'module:translationmanager6');

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
     * Query unimported jobs.
     *
     * @param array $aJobs      List of Jobs.
     * @param int   $maxImports Max imports per call.
     */
    protected function _queryJobs(&$aJobs, &$maxImports)
    {
        $sTable = 'ettm_importjobs';
        $sQuery = "SELECT * FROM $sTable WHERE $sTable.STATUS = 0 LIMIT $maxImports";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sQuery, array());

        if ($oRs !== false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $aJob = $oRs->fields;
                $aJobs[] = $aJob;
                $oRs->fetchRow();
            }
        }
    }
}
