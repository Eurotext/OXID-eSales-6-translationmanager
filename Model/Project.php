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
class Project extends \OxidEsales\Eshop\Core\Model\BaseModel
{

    /**
     * @var string Name of current class
     */
    protected $_sClassName = '\Eurotext\Translationmanager\Model\Project';

    protected $_sUriBase = '/api/v1/';
    protected $_sUriBaseFull = '';
    protected $_sApiKey = '';

    /**
     * The corresponding database table is called "ettm_project".
     * It will be used to render the export/import list.
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('ettm_project');

        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $this->_sApiKey = $oConfig->getShopConfVar('sAPIKEY', $oConfig->getShopId(), 'module:translationmanager6');
        $this->_sUriBaseFull = $oConfig->getShopConfVar('sSERVICEURL', $oConfig->getShopId(), 'module:translationmanager6') . $this->_sUriBase;
    }

    /**
     * Deletes this entry from the database
     *
     * @param int    $action
     * @param string $oxid
     *
     * @return null
     */
    public function onChange($action = null, $oxid = null)
    {
        try {
            switch ($action) {
                case ACTION_INSERT:
                    $this->onChangeInsert();
                    break;
                case ACTION_UPDATE:
                    $this->onChangeUpdate();
                    break;
            }
        } catch (\Exception $e) {
            // Do nothing
        }

        return;
    }

    /**
     * This will be executed when an object is updated.
     *
     * @return null
     */
    public function onChangeUpdate()
    {
        // Projekt name and external id
        $projectName = $this->ettm_project__name->rawValue;
        $projectExternalId = $this->ettm_project__external_id->rawValue;

        // Create request uri
        $sUri = $this->_sUriBase . '/' . $projectExternalId . '.json';

        // Set headers
        $aHeaders = array(
            'Content-Type' => 'application/json',
            'apikey' => $this->_sApiKey,
            'X-Name' => $projectName
        );

        $oClient = new \GuzzleHttp\Client([
            'base_uri' => $this->_sUriBaseFull . 'project/',
            'timeout'  => 6.0,
        ]);

        $aBody = array(
            'description' => '',
        );

        try {
            $oResponse = $oClient->patch(
                $projectExternalId . '.json',
                array(
                    'headers' => $aHeaders,
                    'json' => $aBody,
                )
            );

        } catch (\Exception $e) {
            // Do nothing.
        }

        return;
    }

    /**
     *
     */
    public function startTranslation()
    {

        $projectExternalId = $this->ettm_project__external_id->rawValue;

        // Set headers
        $aHeaders = array(
            'Content-Type' => 'application/json',
            'apikey' => $this->_sApiKey,
            'X-Item-Status' => 'new',
        );

        $oClient = new \GuzzleHttp\Client([
            'base_uri' => $this->_sUriBaseFull . 'transition/project/',
            'timeout'  => 6.0,
        ]);

        try {
            $oResponse = $oClient->patch(
                $projectExternalId . '.json',
                array(
                    'headers' => $aHeaders
                )
            );
            $aResponse = json_decode($oResponse->getBody()->getContents(), false);
            $sExternalId = $aResponse->id;

            $this->assign(
                array(
                    'ettm_project__status' => 50,
                )
            );

            $this->save();

        } catch (\Exception $e) {
          // Do nothing
        }
    }

    /**
     * This will be executed whn an object is inserted.
     *
     * @return null
     */
    public function onChangeInsert()
    {
        // Projekt name
        $projectName = $this->ettm_project__name->rawValue;

        // Set headers
        $aHeaders = array(
            'Content-Type' => 'application/json',
            'apikey' => $this->_sApiKey,
            'X-Type' => 'quote',
            'X-Name' => $projectName
        );

        $oClient = new \GuzzleHttp\Client([
            'base_uri' => $this->_sUriBaseFull,
            'timeout'  => 6.0,
        ]);

        $aBody = array(
            'description' => '',
        );

        try {
            $oResponse = $oClient->post(
                'project.json',
                array(
                    'headers' => $aHeaders,
                    'json' => $aBody,
                )
            );
            $aResponse = json_decode($oResponse->getBody()->getContents(), false);
            $sExternalId = $aResponse->id;

            $this->assign(
                array(
                    'ettm_project__external_id' => $sExternalId,
                )
            );

            $this->save();

        } catch (\Exception $e) {
            // Do nothing
        }

        return;
    }


    /**
     * Delete this object from the database, returns true if entry was deleted.
     *
     * @param string $oxid Object ID(default null)
     *
     * @return bool
     */
    public function delete($oxid = null)
    {
        $this->onChangeDelete($oxid);
        return parent::delete($oxid);
    }

    /**
     * This will be executed when an object is deleted.
     *
     * @param string $sOxId
     *
     * @return void
     */
    public function onChangeDelete($sOxId)
    {
        // Projekt name and external id
        $sOxId = $sOxId ? : $this->getId();

        $oProject = oxNew('\Eurotext\Translationmanager\Model\Project');
        $oProject->load($sOxId);

        $projectExternalId = $oProject->ettm_project__external_id->rawValue;

        // Set headers
        $aHeaders = array(
            'Content-Type' => 'application/json',
            'apikey' => $this->_sApiKey,
        );

        $oClient = new \GuzzleHttp\Client([
            'base_uri' => $this->_sUriBaseFull . 'project/',
            'timeout'  => 6.0,
        ]);

        try {
            $oResponse = $oClient->delete(
                $projectExternalId . '.json',
                array(
                    'headers' => $aHeaders
                )
            );

        } catch (\Exception $e) {
            // Do nothing
        }

        //Delte child elements
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute(
            "DELETE FROM `ettm_project2article` WHERE PROJECT_ID = ?",
            array($sOxId)
        );
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute(
            "DELETE FROM `ettm_project2attribute` WHERE PROJECT_ID = ?",
            array($sOxId)
        );
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute(
            "DELETE FROM `ettm_project2category` WHERE PROJECT_ID = ?",
            array($sOxId)
        );
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute(
            "DELETE FROM `ettm_project2cms` WHERE PROJECT_ID = ?",
            array($sOxId)
        );

        return;
    }

    /**
     * Calculates and saves progress of the project.
     *
     * @return int
     */
    public function updateTranslationProgress()
    {
        // 1. Get items from remote
        $projectExternalId = $this->ettm_project__external_id->rawValue;
        $this->load($this->getId());

        // Set headers
        $aHeaders = array(
            'Content-Type' => 'application/json',
            'apikey' => $this->_sApiKey,
        );

        $oClient = new \GuzzleHttp\Client([
            'base_uri' => $this->_sUriBaseFull . 'project/',
            'timeout'  => 4.0,
        ]);

        // 2. Go through statuses, count finished / all
        $totalCount = 0;
        $finishedCount = 0;

        try {
            $oResponse = $oClient->get(
                $projectExternalId . '.json',
                array(
                    'headers' => $aHeaders
                )
            );
            $aResponse = json_decode($oResponse->getBody()->getContents(), true);

            $totalCount = count($aResponse['items']);

            foreach ($aResponse['items'] as $aItem) {
                if ('finished' === $aItem['status']) {
                    $finishedCount++;
                }
            }

        } catch (\Exception $e) {
            // Do nothing
        }

        // 3. Save total, finished and percent
        $percentageFinished = (int)(((float)$finishedCount / (float)$totalCount) * 100.0);
        $aParams = array(
            'ettm_project__total_items' => $totalCount,
            'ettm_project__finished_items' => $finishedCount,
            'ettm_project__percent_finished' => $percentageFinished,
        );

        if (100 === $percentageFinished) {
            $aParams['ettm_project__total_items'] = 0;
            $aParams['ettm_project__finished_items'] = 0;
            $aParams['ettm_project__percent_finished'] = 0;
            $aParams['ettm_project__status'] = 60;
        }

        $this->assign($aParams);
        $this->save();

        return $percentageFinished;
    }

    /**
     * This functions gets all project items from remote database (EuroText) and
     * creates an entry in our local database.
     * Every entry will be used in cron job for import process. Every entry has
     * a status, that can be 1 or 0.
     * status 1 means the entry has been processen and can be ignored with cronjob.
     * status 0 means the entry should be processed by cron job, because it hasn't
     * been imported yet.
     *
     * ASSUMTION 1: all items have status "finished"
     *
     * @return void
     */
    public function loadImportItems()
    {
        $sId = $this->getId();

        // Get this external id.
        $sExternalId = $this->ettm_project__external_id->rawValue;

        // Get all items from remote.
        $aHeaders = array(
            'Content-Type' => 'application/json',
            'apikey' => $this->_sApiKey,
        );

        $oClient = new \GuzzleHttp\Client([
            'base_uri' => $this->_sUriBaseFull . 'project/',
            'timeout'  => 4.0,
        ]);

        $aItems = array();

        try {
            $oResponse = $oClient->get(
                $sExternalId . '.json',
                array(
                    'headers' => $aHeaders
                )
            );
            $aResponse = json_decode($oResponse->getBody()->getContents(), true);

            $aItems = $aResponse['items'];
        } catch (\Exception $e) {
            // Do nothing
        }

        // Create a marker for each in local database.
        foreach ($aItems as $sItemId => $aItem) {
            $oImportJob = oxNew('\Eurotext\Translationmanager\Model\ImportJob');
            $oImportJob->assign(array(
                'ettm_importjobs__project_id' => $sId,
                'ettm_importjobs__external_id' => $sItemId,
                'ettm_importjobs__external_project_id' => $sExternalId,
            ));
            $oImportJob->save();
        }

        return count($aItems);
    }


    /**
     * Checks if the project has any translatable elements added. if any are added
     * the the project is ready for export.
     *
     * @return void
     */
    public function checkIfReady()
    {
        $oProjectToArticle = oxNew('\Eurotext\Translationmanager\Model\ProjectToArticle');
        $oProjectToAttribute = oxNew('\Eurotext\Translationmanager\Model\ProjectToAttribute');
        $oProjectToCms = oxNew('\Eurotext\Translationmanager\Model\ProjectToCms');
        $oProjectToCategory = oxNew('\Eurotext\Translationmanager\Model\ProjectToCategory');

        $iAttributesCount = $oProjectToAttribute->countItemsForProject($this->getId());
        $iArticlesCount = $oProjectToArticle->countItemsForProject($this->getId());
        $iCategoriesCount = $oProjectToCategory->countItemsForProject($this->getId());
        $iCmsCount = $oProjectToCms->countItemsForProject($this->getId());
        $iTotalCount = $iAttributesCount + $iArticlesCount + $iCategoriesCount + $iCmsCount;

        $aParams = array();
        if (0 < $iTotalCount) {
            $aParams['ettm_project__status'] = 20;
        } else {
            $aParams['ettm_project__status'] = 10;
        }

        $this->assign($aParams);
        $this->save();

        return;
    }

    /**
     * Updates export progress.
     *
     * @return void
     */
    public function updateImportProgress()
    {
        $sProjectId = $this->getId();
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        $sJoinTable = 'ettm_importjobs';

        $sCountConnections = "SELECT COUNT(*) FROM $sJoinTable WHERE PROJECT_ID = ?";
        $result = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($sCountConnections, array($sProjectId));
        $iTotalCount = (int)$result->fields[0];

        $sCountFinishedConnections = "SELECT COUNT(*) FROM $sJoinTable WHERE PROJECT_ID = ? AND STATUS = 10";
        $result = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($sCountFinishedConnections, array($sProjectId));
        $iFinishedTotalCount = (int)$result->fields[0];


        // Counters
        $aParams = array();
        $percentageFinished = (int)(((float)$iFinishedTotalCount / (float)$iTotalCount) * 100.0);
        $aParams['ettm_project__total_items'] = $iTotalCount;
        $aParams['ettm_project__finished_items'] = $iFinishedTotalCount;
        $aParams['ettm_project__percent_finished'] = $percentageFinished;

        if (100 === $percentageFinished) {
            $aParams['ettm_project__total_items'] = 0;
            $aParams['ettm_project__finished_items'] = 0;
            $aParams['ettm_project__percent_finished'] = 0;
            $aParams['ettm_project__status'] = 80;
        }

        $this->assign($aParams);
        $this->save();

        return;
    }

    /**
     * Updates export progress.
     *
     * @return void
     */
    public function updateExportProgress()
    {
        $oProjectToArticle = oxNew('\Eurotext\Translationmanager\Model\ProjectToArticle');
        $oProjectToAttribute = oxNew('\Eurotext\Translationmanager\Model\ProjectToAttribute');
        $oProjectToCms = oxNew('\Eurotext\Translationmanager\Model\ProjectToCms');
        $oProjectToCategory = oxNew('\Eurotext\Translationmanager\Model\ProjectToCategory');

        $iAttributesCount = $oProjectToAttribute->countItemsForProject($this->getId());
        $iFinishedAttributesCount = $oProjectToAttribute->countFinishedItemsForProject($this->getId());

        $iArticlesCount = $oProjectToArticle->countItemsForProject($this->getId());
        $iFinishedArticlesCount = $oProjectToArticle->countFinishedItemsForProject($this->getId());

        $iCategoriesCount = $oProjectToCategory->countItemsForProject($this->getId());
        $iFinishedCategoriesCount = $oProjectToCategory->countFinishedItemsForProject($this->getId());

        $iCmsCount = $oProjectToCms->countItemsForProject($this->getId());
        $iFinishedCmsCount = $oProjectToCms->countFinishedItemsForProject($this->getId());

        $iTotalCount = $iAttributesCount + $iArticlesCount + $iCategoriesCount + $iCmsCount;
        $iFinishedTotalCount = $iFinishedAttributesCount + $iFinishedArticlesCount + $iFinishedCategoriesCount + $iFinishedCmsCount;

        // Counters
        $aParams = array();
        $percentageFinished = (int)(((float)$iFinishedTotalCount / (float)$iTotalCount) * 100.0);
        $aParams['ettm_project__total_items'] = $iTotalCount;
        $aParams['ettm_project__finished_items'] = $iFinishedTotalCount;
        $aParams['ettm_project__percent_finished'] = $percentageFinished;

        if (100 === $percentageFinished) {
            $aParams['ettm_project__total_items'] = 0;
            $aParams['ettm_project__finished_items'] = 0;
            $aParams['ettm_project__percent_finished'] = 0;

            if (1 === intval($this->ettm_project__start_after_export->rawValue)) {
                $this->startTranslation();
            } else {
                $aParams['ettm_project__status'] = 40;
            }
        }

        // Update counters
        $sQuery = "SELECT SUM(SKIPPED) AS SKIPPED, SUM(TRANSMITTED) AS TRANSMITTED, SUM(FAILED) AS FAILED FROM (
        SELECT SUM(SKIPPED) AS SKIPPED, SUM(TRANSMITTED) AS TRANSMITTED, SUM(FAILED) AS FAILED FROM ettm_project2cms WHERE PROJECT_ID = ? AND STATUS = 10
        UNION ALL
        SELECT SUM(SKIPPED) AS SKIPPED, SUM(TRANSMITTED) AS TRANSMITTED, SUM(FAILED) AS FAILED FROM ettm_project2attribute WHERE PROJECT_ID = ? AND STATUS = 10
        UNION ALL
        SELECT SUM(SKIPPED) AS SKIPPED, SUM(TRANSMITTED) AS TRANSMITTED, SUM(FAILED) AS FAILED FROM ettm_project2category WHERE PROJECT_ID = ? AND STATUS = 10
        UNION ALL
        SELECT SUM(SKIPPED) AS SKIPPED, SUM(TRANSMITTED) AS TRANSMITTED, SUM(FAILED) AS FAILED FROM ettm_project2article WHERE PROJECT_ID = ? AND STATUS = 10
        ) AS t";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sQuery, array($this->getId(), $this->getId(), $this->getId(), $this->getId()));
        if ($oRs !== false && $oRs->count() > 0) {
            $aParams['ettm_project__transmitted'] = $oRs->fields['TRANSMITTED'];
            $aParams['ettm_project__skipped'] = $oRs->fields['SKIPPED'];
            $aParams['ettm_project__failed'] = $oRs->fields['FAILED'];
        }

        $this->assign($aParams);
        $this->save();

        return;
    }
}
