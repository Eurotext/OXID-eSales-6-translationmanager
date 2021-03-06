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
class AttributesSelectionAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = [
        'container1' => [
            ['oxtitle', 'oxattribute', 1, 1, 0],
            ['oxid', 'oxattribute', 0, 0, 1],
        ],
        'container2' => [
            ['oxtitle', 'oxattribute', 1, 1, 0],
            ['oxid', 'oxattribute', 0, 0, 1],
        ],
    ];

    /**
     * Empty function, developer should override this method according requirements
     *
     * @return string
     */
    protected function _getQuery()
    {
        $sAttributeTable = $this->_getViewName('oxattribute');
        $sJoinTable = $this->_getViewName('ettm_project2attribute');
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sContainerName = $oConfig->getRequestParameter('cmpid');
        $sProjectId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($oConfig->getRequestParameter('projectid'));

        $oShop = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveShop();
        $oShopId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($oShop->getId());

        $sAttribute2ShopTable = $this->_getViewName('oxattribute2shop');
        $bIsMulti = isset($oShop->oxshops__oxismultishop) ? ((bool) $oShop->oxshops__oxismultishop->value) : false;
        //$bIsSub = isset($oShop->oxshops__oxissubshop) ? ((bool) $oShop->oxshops__oxissubshop->value) : false;

        if (!$bIsMulti) {
            $sShopJoin = "JOIN $sAttribute2ShopTable ON $sAttribute2ShopTable.OXMAPOBJECTID = $sAttributeTable.OXMAPID AND $sAttribute2ShopTable.OXSHOPID = $oShopId ";
        } else {
            $sShopJoin = "";
        }

        if ('container1' === $sContainerName) {
            $sQAdd = " from $sAttributeTable
                LEFT JOIN $sJoinTable
                ON $sJoinTable.OXATTRIBUTEID = $sAttributeTable.OXID AND $sJoinTable.PROJECT_ID = $sProjectId
                $sShopJoin
                WHERE $sJoinTable.OXID IS NULL";
        } else {
            $sQAdd = " from $sAttributeTable
                LEFT JOIN $sJoinTable
                ON $sJoinTable.OXATTRIBUTEID = $sAttributeTable.OXID
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
        $aAttributes = $this->_getActionIds('oxattribute.oxid');
        $sProjectId = $oConfig->getRequestParameter('projectid');
        $sAttributeTable = $this->_getViewName('oxattribute');

        if (1 === intval(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all'))) {
            $aAttributes = $this->_getAll($this->_addFilter("SELECT $sAttributeTable.OXID " . $this->_getQuery()));
        }

        foreach ($aAttributes as $sAttributeId) {
            $oProjectToAttribute = oxNew('\Eurotext\Translationmanager\Model\ProjectToAttribute');
            $oProjectToAttribute->assign(
                [
                    'ettm_project2attribute__project_id' => $sProjectId,
                    'ettm_project2attribute__oxattributeid' => $sAttributeId,
                ]
            );
            $oProjectToAttribute->save();
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
        $aAttributes = $this->_getActionIds('oxattribute.oxid');
        $sJoinTable = $this->_getViewName('ettm_project2attribute');
        $sProjectId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($oConfig->getRequestParameter('projectid'));
        $sAttributeTable = $this->_getViewName('oxattribute');

        if (1 === intval(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all'))) {
            $aAttributes = $this->_getAll($this->_addFilter("SELECT $sAttributeTable.OXID " . $this->_getQuery()));
        }

        foreach ($aAttributes as $sAttributeId) {
            $sAttributeId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($sAttributeId);
            $sDeleteConnection = "DELETE FROM $sJoinTable WHERE $sJoinTable.PROJECT_ID = $sProjectId AND $sJoinTable.OXATTRIBUTEID = $sAttributeId";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($sDeleteConnection);
        }
        return;
    }
}
