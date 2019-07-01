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
class ImportDetail extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'translationmanager6_import_detail.tpl';

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

        $sOxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
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

            $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
            $languages = $oLang->getLanguageArray();

            $aEttmLanguages = array();
            foreach ($languages as $language) {
                $aEttmLanguages[$language->abbr] = $language->name;
            }

            $this->_aViewData['ettmoriglaguages'] = $aEttmLanguages;

            $this->_aViewData['editlangs'] = $newLang;
            $this->_aViewData['readonly'] = true;

        } else {
            $this->_aViewData['editlangs'] = array();
        }

        return $this->_sThisTemplate;
    }


    /**
     * Update or creates a project.
     *
     * @return void
     */
    public function save()
    {
        parent::save();
        return;
    }

    /**
     * Starts the import process.
     *
     * @return void
     */
    public function startImport()
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
        $oProject->loadImportItems();
        $oProject->updateImportProgress();
        $oProject->assign(array('ettm_project__status' => 70));
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
