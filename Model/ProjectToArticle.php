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
class ProjectToArticle extends \OxidEsales\Eshop\Core\Model\BaseModel
{

    /**
     * @var string Name of current class
     */
    protected $_sClassName = '\Eurotext\Translationmanager\Model\ProjectToArticle';

    /**
     * The corresponding database table is called "ettm_project".
     * It will be used to render the export/import list.
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('ettm_project2article');
    }

    /**
     * Performs a query to find out how many items of this type
     * are assigned to the given project.
     *
     * @param string $sProjectId Project ID.
     *
     * @return int Amount of items of type Article
     */
    public function countItemsForProject($sProjectId)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sJoinTable = getViewName('ettm_project2article', \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editlanguage'));
        $sCountConnections = "SELECT COUNT(*) FROM $sJoinTable WHERE $sJoinTable.PROJECT_ID = ?";
        $result = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($sCountConnections, [$sProjectId]);
        return (int)$result->fields[0];
    }

    /**
     * Performs a query to find out how many items of this type
     * are assigned to the given project.
     *
     * @param string $sProjectId Project ID.
     *
     * @return int Amount of items of type Article
     */
    public function countFinishedItemsForProject($sProjectId)
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sJoinTable = getViewName('ettm_project2article', \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editlanguage'));
        $sCountConnections = "SELECT COUNT(*) FROM $sJoinTable WHERE $sJoinTable.PROJECT_ID = ? AND $sJoinTable.STATUS > 0";
        $result = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($sCountConnections, [$sProjectId]);
        return (int)$result->fields[0];
    }
}
