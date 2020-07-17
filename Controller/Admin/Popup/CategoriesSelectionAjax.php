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
class CategoriesSelectionAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array(
        'container1' => array(
            array('oxtitle', 'oxcategories', 1, 1, 0),
            array('oxdesc', 'oxcategories', 1, 1, 0),
            array('oxid', 'oxcategories', 0, 0, 0),
            array('oxid', 'oxcategories', 0, 0, 1),
        ),
        'container2' => array(
            array('oxtitle', 'oxcategories', 1, 1, 0),
            array('oxdesc', 'oxcategories', 1, 1, 0),
            array('oxid', 'oxcategories', 0, 0, 0),
            array('oxid', 'ettm_project2category', 0, 0, 1),
            array('oxid', 'oxcategories', 0, 0, 1),
        ),
    );

    /**
     * Empty function, developer should override this method according requirements
     *
     * @return string
     */
    protected function _getQuery()
    {
        $sCategoriesTable = $this->_getViewName('oxcategories');
        $sJoinTable = $this->_getViewName('ettm_project2category');
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sContainerName = $oConfig->getRequestParameter('cmpid');
        $sProjectId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($oConfig->getRequestParameter('projectid'));

        $oShop = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveShop();
        $oShopId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($oShop->getId());

        $sCategories2ShopTable = $this->_getViewName('oxcategories2shop');
        $bIsMulti = isset($oShop->oxshops__oxismultishop) ? ((bool) $oShop->oxshops__oxismultishop->value) : false;
        //$bIsSub = isset($oShop->oxshops__oxissubshop) ? ((bool) $oShop->oxshops__oxissubshop->value) : false;

        if (!$bIsMulti) {
            $sShopJoin = "JOIN $sCategories2ShopTable ON $sCategories2ShopTable.OXMAPOBJECTID = $sCategoriesTable.OXMAPID AND $sCategories2ShopTable.OXSHOPID = $oShopId ";
        } else {
            $sShopJoin = "";
        }

        if ('container1' === $sContainerName) {
            $sQAdd = " from $sCategoriesTable
                LEFT JOIN $sJoinTable
                ON $sJoinTable.OXCATEGORYID = $sCategoriesTable.OXID AND $sJoinTable.PROJECT_ID = $sProjectId
                $sShopJoin
                WHERE $sJoinTable.OXID IS NULL";
        } else {
            $sQAdd = " from $sCategoriesTable
                LEFT JOIN $sJoinTable
                ON $sJoinTable.OXCATEGORYID = $sCategoriesTable.OXID
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
        $aItems = $this->_getActionIds('oxcategories.oxid');
        $sProjectId = $oConfig->getRequestParameter('projectid');
        $sCategoriesTable = $this->_getViewName('oxcategories');

        if (1 === intval(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all'))) {
            $aItems = $this->_getAll($this->_addFilter("SELECT $sCategoriesTable.OXID " . $this->_getQuery()));
        }

        foreach ($aItems as $sItemId) {
            $oProjectToCategory = oxNew('\Eurotext\Translationmanager\Model\ProjectToCategory');
            $oProjectToCategory->assign(
                array(
                    'ettm_project2category__project_id' => $sProjectId,
                    'ettm_project2category__oxcategoryid' => $sItemId,
                )
            );
            $oProjectToCategory->save();
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
        $aItems = $this->_getActionIds('oxcategories.oxid');
        $sJoinTable = $this->_getViewName('ettm_project2category');
        $sProjectId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($oConfig->getRequestParameter('projectid'));
        $sCategoriesTable = $this->_getViewName('oxcategories');

        if (1 === intval(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all'))) {
            $aItems = $this->_getAll($this->_addFilter("SELECT $sCategoriesTable.OXID " . $this->_getQuery()));
        }

        foreach ($aItems as $sItemId) {
            $sItemId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($sItemId);
            $sDeleteConnection = "DELETE FROM $sJoinTable WHERE $sJoinTable.PROJECT_ID = $sProjectId AND $sJoinTable.OXCATEGORYID = $sItemId";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($sDeleteConnection);
        }
        return;
    }
}
