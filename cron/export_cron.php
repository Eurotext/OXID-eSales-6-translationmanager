<?php
/**
 * Custom cronjob.
 */


require_once dirname(__FILE__) . "/../../../../bootstrap.php";

// initializes singleton config class
$myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

// executing import/export tasks..
oxNew(\Eurotext\Translationmanager\Model\ExportCron::class)->execute();

// closing page, writing cache and so on..
$myConfig->pageClose();
