<?php

$sMetadataVersion = '2.0';
$aModule = array(
    'id'           => 'translationmanager6',
    'title'        => 'Eurotext Translationmanager',
    'description'  => array(
        'de' => 'Modul für die Übersetzung von Shoptexten.',
        'en' => 'Module for the text translation.',
    ),
    'thumbnail'    => 'logo.jpg',
    'version'      => '2.0',
    'author'       => 'mobilemojo – Apps & eCommerce UG (haftungsbeschränkt) & Co. KG',
    'url'          => 'https://www.mobilemojo.de',
    'email'        => 'info@mobilemojo.de',
    'controllers' => array(
        'translationmanager_export' => \Eurotext\Translationmanager\Controller\Admin\Export::class,
        'translationmanager_export_detail' => \Eurotext\Translationmanager\Controller\Admin\ExportDetail::class,
        'translationmanager_export_list' => \Eurotext\Translationmanager\Controller\Admin\ExportList::class,
        'translationmanager_import' => \Eurotext\Translationmanager\Controller\Admin\Import::class,
        'translationmanager_import_detail' => \Eurotext\Translationmanager\Controller\Admin\ImportDetail::class,
        'translationmanager_import_list' => \Eurotext\Translationmanager\Controller\Admin\ImportList::class,
        'translationmanager_settings' => \Eurotext\Translationmanager\Controller\Admin\Settings::class,
        'translationmanager_registration' => \Eurotext\Translationmanager\Controller\Admin\Registration::class,
        'translationmanager_help' => \Eurotext\Translationmanager\Controller\Admin\Help::class,
        'translationmanager_cms_selection' => \Eurotext\Translationmanager\Controller\Admin\Popup\CmsSelectionMain::class,
        'translationmanager_cms_selection_ajax' => \Eurotext\Translationmanager\Controller\Admin\Popup\CmsSelectionAjax::class,
        'translationmanager_articles_selection' => \Eurotext\Translationmanager\Controller\Admin\Popup\ArticlesSelectionMain::class,
        'translationmanager_articles_selection_ajax' => \Eurotext\Translationmanager\Controller\Admin\Popup\ArticlesSelectionAjax::class,
        'translationmanager_attributes_selection' => \Eurotext\Translationmanager\Controller\Admin\Popup\AttributesSelectionMain::class,
        'translationmanager_attributes_selection_ajax' => \Eurotext\Translationmanager\Controller\Admin\Popup\AttributesSelectionAjax::class,
        'translationmanager_categories_selection' => \Eurotext\Translationmanager\Controller\Admin\Popup\CategoriesSelectionMain::class,
        'translationmanager_categories_selection_ajax' => \Eurotext\Translationmanager\Controller\Admin\Popup\CategoriesSelectionAjax::class,
    ),
    'templates' => array(
        'translationmanager6_settings.tpl' => 'eurotext/translationmanager6/application/views/admin/tpl/translationmanager_settings.tpl',
        'translationmanager6_registration.tpl' => 'eurotext/translationmanager6/application/views/admin/tpl/translationmanager_registration.tpl',
        'translationmanager6_help.tpl' => 'eurotext/translationmanager6/application/views/admin/tpl/translationmanager_help.tpl',
        'translationmanager6_export.tpl' => 'eurotext/translationmanager6/application/views/admin/tpl/translationmanager_export.tpl',
        'translationmanager6_export_list.tpl' => 'eurotext/translationmanager6/application/views/admin/tpl/translationmanager_export_list.tpl',
        'translationmanager6_export_detail.tpl' => 'eurotext/translationmanager6/application/views/admin/tpl/translationmanager_export_detail.tpl',
        'translationmanager6_import.tpl' => 'eurotext/translationmanager6/application/views/admin/tpl/translationmanager_import.tpl',
        'translationmanager6_import_list.tpl' => 'eurotext/translationmanager6/application/views/admin/tpl/translationmanager_import_list.tpl',
        'translationmanager6_import_detail.tpl' => 'eurotext/translationmanager6/application/views/admin/tpl/translationmanager_import_detail.tpl',
        'translationmanager6_cms_selection_detail.tpl' => 'eurotext/translationmanager6/application/views/admin/tpl/popup/translationmanager_cms_selection_detail.tpl',
        'translationmanager6_articles_selection_detail.tpl' => 'eurotext/translationmanager6/application/views/admin/tpl/popup/translationmanager_articles_selection_detail.tpl',
        'translationmanager6_attributes_selection_detail.tpl' => 'eurotext/translationmanager6/application/views/admin/tpl/popup/translationmanager_attributes_selection_detail.tpl',
        'translationmanager6_categories_selection_detail.tpl' => 'eurotext/translationmanager6/application/views/admin/tpl/popup/translationmanager_categories_selection_detail.tpl',
    ),
    'events' => array(
        'onActivate' => '\Eurotext\Translationmanager\Core\Installer::onActivate',
        'onDeactivate' => '\Eurotext\Translationmanager\Core\Installer::onDeactivate',
    ),
    'blocks' => array(
        array(
            'template' => 'bottomnaviitem.tpl',
            'block'    => 'admin_bottomnavicustom',
            'file'     => '/application/views/blocks/bottomnavi.tpl'
        )
    ),
);
