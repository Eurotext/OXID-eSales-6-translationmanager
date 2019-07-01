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
class AttributesSelectionMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'translationmanager6_attributes_selection_detail.tpl';

    /**
     * Executes parent method parent::render()
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $this->_aViewData['traslHeaders'] = $this->getHeaders();
        return $this->_sThisTemplate;
    }

    /**
     * Gets table headers and formats the for JS usage.
     *
     * @return array
     */
    public function getHeaders()
    {
        $oAjaxHandler = oxNew('\Eurotext\Translationmanager\Controller\Admin\Popup\AttributesSelectionAjax');
        $aColumns = $oAjaxHandler->getColumns();
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();

        $aJSONHeaders = array(
            'container1' => array(),
            'container2' => array(),
        );

        foreach ($aColumns['container1'] as $index => $aColumn) {
            $sCode = 'ETTM_AJAX_' . strtoupper($aColumn[0]);
            $aJSONHeaders['container1'][] = array(
                'key' => '_' . $index,
                'ident' => (1 === $aColumn[4]) ? true : false,
                'label' => $oLang->translateString($sCode, $oLang->getBaseLanguage(), true),
                'visible' => (1 === $aColumn[2]) ? true : false,
                'sortable' => true
            );
        }

        foreach ($aColumns['container2'] as $index => $aColumn) {
            $sCode = 'ETTM_AJAX_' . strtoupper($aColumn[0]);
            $aJSONHeaders['container2'][] = array(
                'key' => '_' . $index,
                'ident' => (1 === $aColumn[4]) ? true : false,
                'label' => $oLang->translateString($sCode, $oLang->getBaseLanguage(), true),
                'visible' => (1 === $aColumn[2]) ? true : false,
                'sortable' => true
            );
        }

        return $aJSONHeaders;
    }
}
