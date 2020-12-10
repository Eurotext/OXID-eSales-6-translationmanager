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
class TranslationCron extends \OxidEsales\Eshop\Core\Model\BaseModel
{

    /**
     * Standard cron executing function.
     */
    public function execute()
    {
        // 1. Query all project with status 50
        $aProjects = [];
        $this->_queryProjects($aProjects);

        // 2. Update heir status
        $this->_updateProjectsProgress($aProjects);
    }

    /**
     * Query projects for export.
     *
     * @param array $aProjects Reference to projects array.
     */
    protected function _queryProjects(&$aProjects)
    {
        $sTable = 'ettm_project';
        $sProjectQuery = "SELECT * FROM $sTable WHERE $sTable.STATUS = 50";
        $oRs = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->select($sProjectQuery, []);

        if ($oRs !== false && $oRs->count() > 0) {
            while (!$oRs->EOF) {
                $aProject = $oRs->fields;
                $aProjects[] = $aProject;
                $oRs->fetchRow();
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
    protected function _updateProjectsProgress(&$aProjects)
    {
        foreach ($aProjects as $aProject) {
            $oProject = oxNew('\Eurotext\Translationmanager\Model\Project');
            $oProject->load($aProject['OXID']);
            $oProject->updateTranslationProgress();
        }

        return;
    }
}
