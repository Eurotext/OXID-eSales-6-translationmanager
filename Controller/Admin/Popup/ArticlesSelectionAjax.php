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
    protected $_aColumns = array(
        'container1' => array(
            array('oxartnum', 'oxarticles', 1, 0, 0),
            array('oxtitle', 'oxarticles', 1, 1, 0),
            array('oxean', 'oxarticles', 1, 0, 0),
            array('oxmpn', 'oxarticles', 0, 0, 0),
            array('oxprice', 'oxarticles', 0, 0, 0),
            array('oxstock', 'oxarticles', 0, 0, 0),
            array('oxid', 'oxarticles', 0, 0, 1),
        ),
        'container2' => array(
            array('oxartnum', 'oxarticles', 1, 0, 0),
            array('oxtitle', 'oxarticles', 1, 1, 0),
            array('oxean', 'oxarticles', 1, 0, 0),
            array('oxmpn', 'oxarticles', 0, 0, 0),
            array('oxprice', 'oxarticles', 0, 0, 0),
            array('oxstock', 'oxarticles', 0, 0, 0),
            array('oxid', 'oxarticles', 0, 0, 1),
        )
    );

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
                $sQAdd = " from $sArticleTable
                    LEFT JOIN $sJoinTable
                    ON $sJoinTable.OXARTICLEID = $sArticleTable.OXID AND $sJoinTable.PROJECT_ID = $sProjectId
                    INNER JOIN $sCategoryTable
                    ON $sCategoryTable.OXOBJECTID = $sArticleTable.OXID AND $sCategoryTable.OXCATNID = $sCatId
                    $sJoins
                    WHERE $sJoinTable.OXID IS NULL";
            } else {
                // No category is selected.
                $sQAdd = " from $sArticleTable
                    LEFT JOIN $sJoinTable
                    ON $sJoinTable.OXARTICLEID = $sArticleTable.OXID AND $sJoinTable.PROJECT_ID = $sProjectId
                    $sJoins
                    WHERE $sJoinTable.OXID IS NULL";
            }



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
                array(
                    'ettm_project2article__project_id' => $sProjectId,
                    'ettm_project2article__oxarticleid' => $sArticleID,
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
