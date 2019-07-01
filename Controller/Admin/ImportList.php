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
class ImportList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'translationmanager6_import_list.tpl';

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = '\Eurotext\Translationmanager\Model\Project';

    /**
     * Executes parent method parent::render()
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $languages = $oLang->getLanguageArray();

        $aEttmLanguages = array();
        foreach ($languages as $language) {
            $aEttmLanguages[$language->abbr] = $language->name;
        }

        $this->_aViewData['ettmoriglaguages'] = $aEttmLanguages;

        return $this->_sThisTemplate;
    }

    /**
     * Adding folder check
     *
     * @param array  $aWhere  SQL condition array
     * @param string $sqlFull SQL query string
     *
     * @return string $sQ
     */
    protected function _prepareWhereQuery($aWhere, $sqlFull)
    {
        $sQ = parent::_prepareWhereQuery($aWhere, $sqlFull);
        $sMainTable = getViewName('ettm_project', \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editlanguage'));

        $sQ .= " AND $sMainTable.STATUS >= 40";

        return $sQ;
    }
}
