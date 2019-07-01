<?php

/**
 * Settings class
 *
 */

namespace Eurotext\Translationmanager\Model;

/**
 * Settings class
 *
 */
class ImportJob extends \OxidEsales\Eshop\Core\Model\BaseModel
{

    /**
     * @var string Name of current class
     */
    protected $_sClassName = '\Eurotext\Translationmanager\Model\ImportJob';

    /**
     * The corresponding database table is called "ettm_project".
     * It will be used to render the export/import list.
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('ettm_importjobs');
    }
}
