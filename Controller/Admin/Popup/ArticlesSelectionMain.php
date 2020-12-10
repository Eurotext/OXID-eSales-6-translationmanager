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
class ArticlesSelectionMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'translationmanager6_articles_selection_detail.tpl';

    /**
     * Executes parent method parent::render()
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $this->_aViewData['traslHeaders'] = $this->getHeaders();
        $iShopId = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveShop()->getId();
        $this->_getCategoryTree('artcattree', '', '', false, $iShopId);

        // Selected target languages
        $this->_aViewData['editlangs'] = [];

        $sOxId = $this->getEditObjectId();
        if (isset($sOxId) && (-1 !== intval($sOxId))) {
            // load object
            $oProject = oxNew('\Eurotext\Translationmanager\Model\Project');
            $oProject->load($sOxId);

            $langs = unserialize($oProject->ettm_project__lang_target->rawValue);

            $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
            $aLanguages = $oLang->getLanguageArray();
            $aRetArray = [];
            foreach ($aLanguages as $aLanguage) {
                $aRetArray[$aLanguage->abbr] = $aLanguage->id;
            }

            $newLang = [];
            foreach ($langs as $lang) {
                $newLang[] = $aRetArray[$lang];
            }

            $this->_aViewData['editlangs'] = implode(',', $newLang);
        }



        return $this->_sThisTemplate;
    }

    /**
     * Gets table headers and formats the for JS usage.
     *
     * @return array
     */
    public function getHeaders()
    {
        $oAjaxHandler = oxNew('\Eurotext\Translationmanager\Controller\Admin\Popup\ArticlesSelectionAjax');
        $aColumns = $oAjaxHandler->getColumns();
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();

        $aJSONHeaders = [
            'container1' => [],
            'container2' => [],
        ];


        foreach ($aColumns['container1'] as $index => $aColumn) {
            $sCode = 'ETTM_AJAX_' . strtoupper($aColumn[0]);
            $aJSONHeaders['container1'][] = [
                'key' => '_' . $index,
                'ident' => (1 === $aColumn[4]) ? true : false,
                'label' => $oLang->translateString($sCode, $oLang->getBaseLanguage(), true),
                'visible' => (1 === $aColumn[2]) ? true : false,
                'sortable' => true,
            ];
        }

        foreach ($aColumns['container2'] as $index => $aColumn) {
            $sCode = 'ETTM_AJAX_' . strtoupper($aColumn[0]);
            $aJSONHeaders['container2'][] = [
                'key' => '_' . $index,
                'ident' => (1 === $aColumn[4]) ? true : false,
                'label' => $oLang->translateString($sCode, $oLang->getBaseLanguage(), true),
                'visible' => (1 === $aColumn[2]) ? true : false,
                'sortable' => true,
            ];
        }

        return $aJSONHeaders;
    }
}
