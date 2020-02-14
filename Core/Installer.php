<?php
/**
 * Settings class
 *
 */

namespace Eurotext\Translationmanager\Core;

use OxidEsales\Eshop\Core\DatabaseProvider;

/**
 * Settings class
 *
 */
class Installer
{

    /**
     * Actions to execute on module activation.
     *
     * @return void
     */
    public static function onActivate()
    {
        self::_createProjectTable();
        return;
    }


    /**
    * Actions to execute on module deactivation.
    *
    * @return void
    */
    public static function onDeActivate()
    {
        return;
    }

    /**
     * Create a db table for project entity.
     */
    private static function _createProjectTable()
    {

        $sCreateProjectMainTable = "CREATE TABLE IF NOT EXISTS `ettm_project` (
          `OXID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique Project ID',
          `OXSHOPID` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Shop ID',
          `EXTERNAL_ID` int(5) NOT NULL COMMENT 'Project Id in EuroText Database',
          `TOTAL_ITEMS` int(11) NOT NULL COMMENT 'Total amount of items in EuroText Datebase',
          `FINISHED_ITEMS` int(11) NOT NULL COMMENT 'Amount of items with finished status in EuroText Database',
          `PERCENT_FINISHED` int(11) NOT NULL COMMENT 'Calculated percentage of project readiness',
          `NAME` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Project name',
          `LANG_ORIGIN` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Original language',
          `LANG_TARGET` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Target languages',
          `CREATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp when created',
          `UPDATED_AT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp when updated',
          `STATUS` int(11) NOT NULL DEFAULT '0' COMMENT 'Project status',
          `ONLY_UNTRANSLATED` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - select all items for translations, 1 - select only those without translation',
          `START_AFTER_EXPORT` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - dont start, 1 - start',
          `TRANSMITTED` tinyint(4) NOT NULL DEFAULT '0',
          `SKIPPED` tinyint(4) NOT NULL DEFAULT '0',
          `FAILED` tinyint(4) NOT NULL DEFAULT '0',
          PRIMARY KEY (`OXID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        DatabaseProvider::getDb()->execute($sCreateProjectMainTable);

        $sCreateProjectToCmsTable = "CREATE TABLE IF NOT EXISTS `ettm_project2cms` (
          `OXID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `PROJECT_ID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `OXCMSID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `STATUS` int(11) NOT NULL DEFAULT '0',
          `EXPORTABLE` tinyint(4) NOT NULL DEFAULT '0',
          `SKIPPED` tinyint(4) NOT NULL DEFAULT '0',
          `TRANSMITTED` tinyint(4) NOT NULL DEFAULT '0',
          `FAILED` tinyint(4) NOT NULL DEFAULT '0',
          PRIMARY KEY (`OXID`),
          KEY `ETTM_PROJECT_ID_OXCMSID` (`PROJECT_ID`,`OXCMSID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        DatabaseProvider::getDb()->execute($sCreateProjectToCmsTable);

        $sCreateProjectToCategoryTable = "CREATE TABLE IF NOT EXISTS `ettm_project2category` (
          `OXID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `PROJECT_ID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `OXCATEGORYID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `STATUS` int(11) NOT NULL DEFAULT '0',
          `EXPORTABLE` tinyint(4) NOT NULL DEFAULT '0',
          `SKIPPED` tinyint(4) NOT NULL DEFAULT '0',
          `TRANSMITTED` tinyint(4) NOT NULL DEFAULT '0',
          `FAILED` tinyint(4) NOT NULL DEFAULT '0',
          PRIMARY KEY (`OXID`),
          KEY `ETTM_PROJECT_ID_OXCATEGORYID` (`PROJECT_ID`,`OXCATEGORYID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        DatabaseProvider::getDb()->execute($sCreateProjectToCategoryTable);

        $sCreateProjectToAttributeTable = "CREATE TABLE IF NOT EXISTS `ettm_project2attribute` (
          `OXID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `PROJECT_ID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `OXATTRIBUTEID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `STATUS` int(11) NOT NULL DEFAULT '0',
          `EXPORTABLE` tinyint(4) NOT NULL DEFAULT '0',
          `SKIPPED` tinyint(4) NOT NULL DEFAULT '0',
          `TRANSMITTED` tinyint(4) NOT NULL DEFAULT '0',
          `FAILED` tinyint(4) NOT NULL DEFAULT '0',
          PRIMARY KEY (`OXID`),
          KEY `ETTM_PROJECT_ID_OXATTRIBUTEID` (`PROJECT_ID`,`OXATTRIBUTEID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        DatabaseProvider::getDb()->execute($sCreateProjectToAttributeTable);

        $sCreateProjectToArticleTable = "CREATE TABLE IF NOT EXISTS `ettm_project2article` (
          `OXID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `PROJECT_ID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `OXARTICLEID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `STATUS` int(11) NOT NULL DEFAULT '0',
          `EXPORTABLE` tinyint(4) NOT NULL DEFAULT '0',
          `SKIPPED` tinyint(4) NOT NULL DEFAULT '0',
          `TRANSMITTED` tinyint(4) NOT NULL DEFAULT '0',
          `FAILED` tinyint(4) NOT NULL DEFAULT '0',
          PRIMARY KEY (`OXID`),
          KEY `ETTM_PROJECT_ID_OXARTICLEID` (`PROJECT_ID`,`OXARTICLEID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        DatabaseProvider::getDb()->execute($sCreateProjectToArticleTable);

        $sCreateImportItems = "CREATE TABLE IF NOT EXISTS `ettm_importjobs` (
          `OXID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `PROJECT_ID` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `STATUS` tinyint(1) NOT NULL DEFAULT '0',
          `EXTERNAL_PROJECT_ID` int(11) NOT NULL,
          `EXTERNAL_ID` int(11) NOT NULL,
          `CREATED_AT` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        DatabaseProvider::getDb()->execute($sCreateImportItems);
    }
}
