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
class Import extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'translationmanager6_import.tpl';

    /**
     * Executes parent method parent::render()
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        return $this->_sThisTemplate;
    }
}
