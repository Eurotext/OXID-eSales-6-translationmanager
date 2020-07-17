<?php
/**
 * Settings class
 *
 */

namespace Eurotext\Translationmanager\Controller\Admin\Popup;

/**
 * Settings class
 *
 */
class CmsSelectionAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array(
        'container1' => array(
            array('oxloadid', 'oxcontents', 1, 0, 0),
            array('oxtitle', 'oxcontents', 1, 1, 0),
            array('oxactive', 'oxcontents', 1, 0, 0),
            array('oxshopid', 'oxcontents', 1, 0, 0),
            array('oxid', 'oxcontents', 0, 0, 1),
        ),
        'container2' => array(
            array('oxloadid', 'oxcontents', 1, 0, 0),
            array('oxtitle', 'oxcontents', 1, 1, 0),
            array('oxactive', 'oxcontents', 1, 0, 0),
            array('oxshopid', 'oxcontents', 1, 0, 0),
            array('oxid', 'oxcontents', 0, 0, 1),
        ),
    );

    /**
     * Empty function, developer should override this method according requirements
     *
     * @return string
     */
    protected function _getQuery()
    {
        $sCmsTable = $this->_getViewName('oxcontents');
        $sJoinTable = $this->_getViewName('ettm_project2cms');
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sContainerName = $oConfig->getRequestParameter('cmpid');
        $sProjectId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($oConfig->getRequestParameter('projectid'));

        $oShop = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveShop();
        $oShopId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($oShop->getId());

        $bIsMulti = isset($oShop->oxshops__oxismultishop) ? ((bool) $oShop->oxshops__oxismultishop->value) : false;
        //$bIsSub = isset($oShop->oxshops__oxissubshop) ? ((bool) $oShop->oxshops__oxissubshop->value) : false;

        if (!$bIsMulti) {
            $sShopWhere = " AND $sCmsTable.OXSHOPID = $oShopId ";
        } else {
            $sShopWhere = "";
        }


        if ('container1' === $sContainerName) {
            $sQAdd = " from $sCmsTable
                LEFT JOIN $sJoinTable
                ON $sJoinTable.OXCMSID = $sCmsTable.OXID AND $sJoinTable.PROJECT_ID = $sProjectId
                WHERE $sJoinTable.OXID IS NULL $sShopWhere";
        } else {
            $sQAdd = " from $sCmsTable
                LEFT JOIN $sJoinTable
                ON $sJoinTable.OXCMSID = $sCmsTable.OXID
                WHERE $sJoinTable.OXID IS NOT NULL AND $sJoinTable.PROJECT_ID = $sProjectId";
        }

        return $sQAdd;
    }

    /**
     * Drop action implementation. Creates a binding for selected CMS.
     *
     * @return null
     */
    public function addItem()
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aCMS = $this->_getActionIds('oxcontents.oxid');
        $sProjectId = $oConfig->getRequestParameter('projectid');
        $sCmsTable = $this->_getViewName('oxcontents');

        if (1 === intval(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all'))) {
            $aCMS = $this->_getAll($this->_addFilter("SELECT $sCmsTable.OXID " . $this->_getQuery()));
        }

        foreach ($aCMS as $sCMSOXID) {
            $oProjectToCMS = oxNew('\Eurotext\Translationmanager\Model\ProjectToCms');
            $oProjectToCMS->assign(
                array(
                    'ettm_project2cms__project_id' => $sProjectId,
                    'ettm_project2cms__oxcmsid' => $sCMSOXID,
                )
            );
            $oProjectToCMS->save();
        }
        return;
    }

    /**
     * Drop action implementation. Removes the binding for selected CMS.
     *
     * @return null
     */
    public function removeItem()
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aCMS = $this->_getActionIds('oxcontents.oxid');
        $sJoinTable = $this->_getViewName('ettm_project2cms');
        $sProjectId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($oConfig->getRequestParameter('projectid'));
        $sCmsTable = $this->_getViewName('oxcontents');

        if (1 === intval(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all'))) {
            $aCMS = $this->_getAll($this->_addFilter("SELECT $sCmsTable.OXID " . $this->_getQuery()));
        }

        foreach ($aCMS as $sCMSOXID) {
            $sCMSOXID = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($sCMSOXID);
            $sDeleteConnection = "DELETE FROM $sJoinTable WHERE $sJoinTable.PROJECT_ID = $sProjectId AND $sJoinTable.OXCMSID = $sCMSOXID";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($sDeleteConnection);
        }
        return;
    }
}
