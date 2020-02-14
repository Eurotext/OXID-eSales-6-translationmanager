<?php
/**
 * Settings class
 *
 */

namespace Eurotext\Translationmanager\Controller\Admin;

/**
 * Settings class
 *
 */
class ExportDetail extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'translationmanager6_export_detail.tpl';

    /**
     * Available languages.
     *
     * @var array
     */
    private $_aLanguageList = array();

    /**
     * Executes parent method parent::render()
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $sOxId = $this->_aViewData['oxid'] = $this->getEditObjectId();
        $this->_aViewData['projectstatus'] = 0;

        // Current language
        // List available languages
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $languages = $oLang->getLanguageArray();
        $aEttmLanguages = array();
        foreach ($languages as $language) {
            $aEttmLanguages[$language->abbr] = $language->name;
        }
        // Available languages
        $this->_aViewData['ettmoriglaguages'] = $aEttmLanguages;

        // Selected target languages
        $this->_aViewData['editlangs'] = array();

        if (isset($sOxId) && (-1 !== intval($sOxId))) {
            // load object
            $oProject = oxNew('\Eurotext\Translationmanager\Model\Project');
            $oProject->load($sOxId);
            $this->_aViewData['edit'] = $oProject;

            $langs = unserialize($oProject->ettm_project__lang_target->rawValue);
            $newLang = array();
            foreach ($langs as $lang) {
                $newLang[] = (object) $lang;
            }

            $this->_aViewData['editlangs'] = $newLang;
        }

        // Count
        $oProjectToArticle = oxNew('\Eurotext\Translationmanager\Model\ProjectToArticle');
        $oProjectToAttribute = oxNew('\Eurotext\Translationmanager\Model\ProjectToAttribute');
        $oProjectToCms = oxNew('\Eurotext\Translationmanager\Model\ProjectToCms');
        $oProjectToCategory = oxNew('\Eurotext\Translationmanager\Model\ProjectToCategory');

        $iAttributesCount = $oProjectToAttribute->countItemsForProject($sOxId);
        $iArticlesCount = $oProjectToArticle->countItemsForProject($sOxId);
        $iCategoriesCount = $oProjectToCategory->countItemsForProject($sOxId);
        $iCmsCount = $oProjectToCms->countItemsForProject($sOxId);

        $this->_aViewData['attributescount'] = $iAttributesCount;
        $this->_aViewData['articlescount'] = $iArticlesCount;
        $this->_aViewData['categoriescount'] = $iCategoriesCount;
        $this->_aViewData['cmscount'] = $iCmsCount;

        $this->_aViewData['totalcount'] = $iAttributesCount + $iArticlesCount + $iCategoriesCount + $iCmsCount;

        return $this->_sThisTemplate;
    }


    /**
     * Create a new project or update an existing one.
     *
     * @return string
     */
    public function save()
    {
        parent::save();
        $sOxId = $this->getEditObjectId();
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aParams = $oConfig->getRequestParameter('editval');
        $aParams['ettm_project__oxshopid'] = $oConfig->getShopId();
        $aLangParams = $oConfig->getRequestParameter('editlangs');

        $oProject = oxNew('\Eurotext\Translationmanager\Model\Project');
        if (-1 !== intval($sOxId)) {
            // If oxid is set, that means we are editing an existing project .
            // In this case load the data from database.
            $oProject->load($sOxId);
        } else {
            $aParams['ettm_project__oxid'] = null;
        }

        $aParams['ettm_project__created_at'] = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::get('oxUtilsDate')->getTime());
        $aParams['ettm_project__updated_at'] = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::get('oxUtilsDate')->getTime());

        $aParams['ettm_project__status'] = 10;

        $aLanguages = array();
        foreach ($aLangParams as $sGivenLanguage) {
            if (!in_array($sGivenLanguage, $aLanguages)) {
                $aLanguages[] = $sGivenLanguage;
            }
        }
        $aParams['ettm_project__lang_target'] = serialize($aLanguages);

        $oProject->assign($aParams);
        $oProject->save();

        // set oxid if inserted
        $sOxId = $oProject->getId();
        $this->setEditObjectId($sOxId);

        $oProject->checkIfReady();

        return;
    }

    /**
     * Checks if the project has any translatable elements added. if any are added
     * the the project is ready for export.
     *
     * @return void
     */
    public function checkIfReady()
    {
        $sOxId = $this->getEditObjectId();
        $oProject = oxNew('\Eurotext\Translationmanager\Model\Project');
        if (-1 !== intval($sOxId)) {
            // If oxid is set, that means we are editing an existing project .
            // In this case load the data from database.
            $oProject->load($sOxId);
        } else {
            return;
        }
        $oProject->checkIfReady();

        $this->_aViewData['updatelist'] = 1;

        return;
    }

    /**
     * Start export
     *
     * @return void
     */
    public function startExport()
    {
        $sOxId = $this->getEditObjectId();
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $aParams = $oConfig->getRequestParameter('editval');

        $oProject = oxNew('\Eurotext\Translationmanager\Model\Project');
        if (-1 !== intval($sOxId)) {
            // If oxid is set, that means we are editing an existing project .
            // In this case load the data from database.
            $oProject->load($sOxId);
        } else {
            return;
        }

        $oProject->updateExportProgress();
        $oProject->assign(array('ettm_project__status' => 30));

        // If checkbox selected
        if (isset($aParams['ettm_project__only_untranslated']) && 1 === intval($aParams['ettm_project__only_untranslated'])) {
            $oProject->assign(array('ettm_project__only_untranslated' => 1));
        }

        // If start translations right after export is finished.
        if (isset($aParams['ettm_project__start_after_export']) && 1 === intval($aParams['ettm_project__start_after_export'])) {
            $oProject->assign(array('ettm_project__start_after_export' => 1));
        }

        $oProject->save();

        $this->_aViewData['updatelist'] = 1;
        return;
    }

    /**
     * Start export
     *
     * @return void
     */
    public function startTranslation()
    {
        $sOxId = $this->getEditObjectId();
        $oProject = oxNew('\Eurotext\Translationmanager\Model\Project');
        if (-1 !== intval($sOxId)) {
            // If oxid is set, that means we are editing an existing project .
            // In this case load the data from database.
            $oProject->load($sOxId);
        } else {
            return;
        }

        $oProject->startTranslation();

        $oProject->updateTranslationProgress();
        $oProject->save();

        $this->_aViewData['updatelist'] = 1;
        return;
    }
}
