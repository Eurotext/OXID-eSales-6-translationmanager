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
class Help extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'translationmanager6_help.tpl';

    /**
     * Executes parent method parent::render()
     *
     * @return string
     */
    public function render()
    {
        return $this->_sThisTemplate;
    }
}
