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
class ArticlesSelectionAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = [
        'container1' => [
            ['oxartnum', 'oxarticles', 1, 0, 0],
            ['oxtitle', 'oxarticles', 1, 1, 0],
            ['oxean', 'oxarticles', 1, 0, 0],
            ['oxmpn', 'oxarticles', 0, 0, 0],
            ['oxprice', 'oxarticles', 0, 0, 0],
            ['oxstock', 'oxarticles', 0, 0, 0],
            ['oxid', 'oxarticles', 0, 0, 1],
        ],
        'container2' => [
            ['oxartnum', 'oxarticles', 1, 0, 0],
            ['oxtitle', 'oxarticles', 1, 1, 0],
            ['oxean', 'oxarticles', 1, 0, 0],
            ['oxmpn', 'oxarticles', 0, 0, 0],
            ['oxprice', 'oxarticles', 0, 0, 0],
            ['oxstock', 'oxarticles', 0, 0, 0],
            ['oxid', 'oxarticles', 0, 0, 1],
        ]
    ];

    /**
     * Empty function, developer should override this method according requirements
     *
     * @return string
     */
    protected function _getQuery()
    {
        $sArticleTable = $this->_getViewName('oxarticles');
        $sCategoryTable = $this->_getViewName('oxobject2category');
        $sJoinTable = $this->_getViewName('ettm_project2article');
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sContainerName = $oConfig->getRequestParameter('cmpid');
        $sProjectId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($oConfig->getRequestParameter('projectid'));

        $oShop = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveShop();
        $oShopId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($oShop->getId());

        $sArticle2ShopTable = $this->_getViewName('oxarticles2shop');
        $bIsMulti = isset($oShop->oxshops__oxismultishop) ? ((bool) $oShop->oxshops__oxismultishop->value) : false;
        //$bIsSub = isset($oShop->oxshops__oxissubshop) ? ((bool) $oShop->oxshops__oxissubshop->value) : false;

        if (!$bIsMulti) {
            $sShopJoin = "JOIN $sArticle2ShopTable ON $sArticle2ShopTable.OXMAPOBJECTID = $sArticleTable.OXMAPID AND $sArticle2ShopTable.OXSHOPID = $oShopId ";
        } else {
            $sShopJoin = "";
        }

        $sTimefilter = "";

        if (!empty($oConfig->getRequestParameter('atrmode'))) {
            $originalStart = (!empty($oConfig->getRequestParameter('start')))?$oConfig->getRequestParameter('start'):date('d.m.Y', time());
            $originalEnd = (!empty($oConfig->getRequestParameter('end')))?$oConfig->getRequestParameter('end'):date('d.m.Y', time());

            $start = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote(date('Y-m-d', strtotime($originalStart)) . ' 00:00:00');
            $end = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote(date('Y-m-d', strtotime($originalEnd)) . ' 23:59:59');

            if ('updated' === $oConfig->getRequestParameter('atrmode')) {
                $sTimefilter = " AND $sArticleTable.OXTIMESTAMP >= $start AND $sArticleTable.OXTIMESTAMP <= $end";
            }

            if ('created' === $oConfig->getRequestParameter('atrmode')) {
                $sTimefilter = " AND $sArticleTable.OXINSERT >= $start AND $sArticleTable.OXINSERT <= $end";
            }
        }

        // Has target langs selected?
        $sJoins = '';
        if ($oConfig->getRequestParameter('targetlangs') && 1 !== intval($oConfig->getRequestParameter('nofilter'))) {
            $aTargetLangIds = explode(',', $oConfig->getRequestParameter('targetlangs'));
            foreach ($aTargetLangIds as $iTargetLangId) {
                // Get view and create join statement
                $sTableName = getViewName('oxarticles', $iTargetLangId);
                $sJoins .= "JOIN $sTableName ON $sTableName.OXID = $sArticleTable.OXID AND $sTableName.OXTITLE = ''";
            }
        }

        if ('container1' === $sContainerName) {
            $sAddtionalJoint = '';
            if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('catid')) {
                // A category is selected
                $sCatId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote(
                    \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('catid')
                );
                $sQAdd = " FROM $sArticleTable
                    LEFT JOIN $sJoinTable
                    ON $sJoinTable.OXARTICLEID = $sArticleTable.OXID AND $sJoinTable.PROJECT_ID = $sProjectId
                    INNER JOIN $sCategoryTable
                    ON $sCategoryTable.OXOBJECTID = $sArticleTable.OXID AND $sCategoryTable.OXCATNID = $sCatId
                    $sJoins
                    $sShopJoin
                    WHERE $sJoinTable.OXID IS NULL";
            } else {
                // No category is selected.
                $sQAdd = " from $sArticleTable
                    LEFT JOIN $sJoinTable
                    ON $sJoinTable.OXARTICLEID = $sArticleTable.OXID AND $sJoinTable.PROJECT_ID = $sProjectId
                    $sJoins
                    WHERE $sJoinTable.OXID IS NULL";
            }
            $sQAdd = $sQAdd.$sTimefilter;
        } else {
            $sQAdd = " from $sArticleTable
                LEFT JOIN $sJoinTable
                ON $sJoinTable.OXARTICLEID = $sArticleTable.OXID
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
        $aArticles = $this->_getActionIds('oxarticles.oxid');
        $sProjectId = $oConfig->getRequestParameter('projectid');
        $sArticleTable = $this->_getViewName('oxarticles');

        if (1 === intval(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all'))) {
            $aArticles = $this->_getAll($this->_addFilter("SELECT $sArticleTable.OXID " . $this->_getQuery()));
        }

        foreach ($aArticles as $sArticleID) {
            $oProjectToCMS = oxNew('\Eurotext\Translationmanager\Model\ProjectToArticle');
            $oProjectToCMS->assign(
                [
                    'ettm_project2article__project_id' => $sProjectId,
                    'ettm_project2article__oxarticleid' => $sArticleID,
                ]
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
        $aArticles = $this->_getActionIds('oxarticles.oxid');
        $sJoinTable = $this->_getViewName('ettm_project2article');
        $sProjectId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($oConfig->getRequestParameter('projectid'));
        $sArticleTable = $this->_getViewName('oxarticles');

        if (1 === intval(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all'))) {
            $aArticles = $this->_getAll($this->_addFilter("SELECT $sArticleTable.OXID " . $this->_getQuery()));
        }

        foreach ($aArticles as $sArticleID) {
            $sArticleID = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($sArticleID);
            $sDeleteConnection = "DELETE FROM $sJoinTable WHERE $sJoinTable.PROJECT_ID = $sProjectId AND $sJoinTable.OXARTICLEID = $sArticleID";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($sDeleteConnection);
        }
        return;
    }
}
