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
class Export extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'translationmanager6_export.tpl';

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
