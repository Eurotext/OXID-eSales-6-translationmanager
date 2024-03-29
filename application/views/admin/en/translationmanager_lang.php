<?php
$sLangName  = "Englisch";
// -------------------------------
// RESOURCE IDENTITFIER = STRING
// -------------------------------
$aLang = [
    'charset'                                   => 'UTF-8',
    'TOOLTIPS_NEW_EUROTEXT_EXPORT_PROJECT' => 'New project',
    'TOOLTIPS_SUPPORT_EUROTEXT_EXPORT_BOTTOM' => 'Support',
    'BOTTOM_VERSION_INFO' => 'v2.0',
    'ETTM_TRANSLATION' => 'Eurotext',
    'ETTM_HOME' => 'Translation Manager',
    'ETTM_REGISTRATION' => 'Registration',
    'ETTM_SETTINGS' => 'Settings',
    'ETTM_EXPORT' => 'Export',
    'ETTM_IMPORT' => 'Import',
    'ETTM_HELP' => 'Help &amp; Support',
    'ETTM_EXPORT_MAIN' => 'Settings',
    'ETTM_IMPORT_MAIN'  => 'Settings',
    'ETTM_STATUS_10' => 'New',
    'ETTM_STATUS_20' => 'Ready for export',
    'ETTM_STATUS_30' => 'Export in progress',
    'ETTM_STATUS_40' => 'Export complete',
    'ETTM_STATUS_50' => 'Project work ongoing',
    'ETTM_STATUS_60' => 'Ready for import',
    'ETTM_STATUS_70' => 'Import in progress',
    'ETTM_STATUS_80' => 'Import complete',
    'ETTM_SETTINGS' => 'Settings',
    'ETTM_SETTINGS_GENERAL' => 'General settings',
    'ETTM_SETTINGS_APIKEY' => 'API key:',
    'ETTM_SETTINGS_APIKEYHELP' => 'Please save a working API key.',
    'ETTM_SETTINGS_STATUS' => 'Connection status:',
    'ETTM_SETTINGS_STATUS_OK' => 'Ok',
    'ETTM_SETTINGS_STATUS_OK_LONG' => 'Connected.',
    'ETTM_SETTINGS_STATUS_OK_HELP' => 'Make sure that the plugin is able to establish a connection with the translation portal.',
    'ETTM_SETTINGS_STATUS_FAIL' => 'Error',
    'ETTM_SETTINGS_STATUS_FAIL_LONG' => 'A connection could not be established.',
    'ETTM_SETTINGS_STATUS_FAIL_HELP' => 'Make sure that the plugin is able to establish a connection with the translation portal.',
    'ETTM_SETTINGS_ENDPOINT' => 'Service end point:',
    'ETTM_SETTINGS_ENDPOINT_LIVE' => 'Live',
    'ETTM_SETTINGS_ENDPOINT_STAGE' => 'Stage end point',
    'ETTM_SETTINGS_ENDPOINT_SANDBOX' => 'Sandbox',
    'ETTM_SETTINGS_LANGUAGES' => 'Languages:',
    'ETTM_SETTINGS_SHOP_LANGUAGE'                  => 'Your languages in the OXID eShop',
    'ETTM_SETTINGS_EUROTEXT_LANGUAGE'              => 'Languages in the translation portal',
    'ETTM_HELP_SOURCE' => 'Please select the service end point.',
    'ETTM_SETTINGS_CRON' => 'Cron jobs:',
    'ETTM_SETTINGS_CRON_NAME' => 'Cron job name',
    'ETTM_SETTINGS_CRON_ITM' => 'Items per call',
    'ETTM_SETTINGS_CRON_EXPORT' => 'Export job:',
    'ETTM_SETTINGS_CRON_IMPORT' => 'Import job:',
    'ETTM_HELP_EXPORTJOBIPJ' => 'Please specify the number of items per export cron job. For large data volumes, e.g. lots of product texts, we recommend a setting of 300-500.',
    'ETTM_HELP_IMPORTJOBIPJ' => 'This standard value is appropriate for nearly all kinds of projects',
    'ETTM_SETTINGS_FIELDS' => 'Translatable fields:',
    'ETTM_SETTINGS_FIELDS_VIEW' => 'View: ',
    'ETTM_SETTINGS_FIELDS_VIEW_SIMPLE' => 'simple',
    'ETTM_SETTINGS_FIELDS_VIEW_EXT' => 'extended',
    'ETTM_SETTINGS_FIELDS_CMS' => 'CMS pages',
    'ETTM_SETTINGS_OXCONTENTS_OXTITLE' => 'Title',
    'ETTM_SETTINGS_OXCONTENTS_OXCONTENT' => 'Content',
    'ETTM_SETTINGS_FIELDS_CAT' => 'Categories',
    'ETTM_SETTINGS_OXCATEGORIES_OXTITLE' => 'Title',
    'ETTM_SETTINGS_OXCATEGORIES_OXDESC' => 'Description',
    'ETTM_SETTINGS_OXCATEGORIES_OXOXLONGDESC' => 'Long description',
    'ETTM_SETTINGS_FIELDS_ATTRIBUTES' => 'Attributes',
    'ETTM_SETTINGS_OXATTRIBUTE_OXTITLE' => 'Title',
    'ETTM_SETTINGS_OXOBJECT2ATTRIBUTE_OXVALUE' => 'Value of attribute',
    'ETTM_SETTINGS_FIELDS_ARTICLES' => 'Item',
    'ETTM_SETTINGS_OXARTICLES_OXTITLE' => 'Title',
    'ETTM_SETTINGS_OXARTICLES_OXVARNAME' => 'Name of selection',
    'ETTM_SETTINGS_OXARTICLES_OXVARSELECT' => 'Value of selection',
    'ETTM_SETTINGS_OXARTICLES_OXSHORTDESC' => 'Short description',
    'ETTM_SETTINGS_OXARTEXTENDS_OXLONGDESC' => 'Description',

    'ETTM_REGISTRATION' => 'Registration',
    'ETTM_REGISTRATION_HTMLBLOCK1' => '<p>In order to use the plugin, you need an API key.</p><p>You can request the API key here:</p>',
    'ETTM_REGISTRATION_HTMLBLOCK2' => '<p><b><a target="_blank" href="https://eurotext.de/api-key-anfordern/">https://eurotext.de/api-key-anfordern/</a></b></p>',

    'ETTM_EXPORT_GENERAL' => 'General settings',
    'ETTM_EXPORT_GENERAL_PROJECT_NAME' => 'Project name:',
    'ETTM_EXPORT_GENERAL_PROJECT_NAMEHELP' => 'Please assign a unique project name.',
    'ETTM_EXPORT_GENERAL_STARTLANG' => 'Source language:',
    'ETTM_EXPORT_GENERAL_TARGETLANG' => 'Target language(s):',
    'ETTM_EXPORT_ELEMENTS' => 'Select elements for translation',
    'ETTM_EXPORT_ELEMENTS_TEXTBLOCK' => '<p>If you click on the button below, the selected texts will be submitted to the translation portal and you will receive a detailed quotation for the translation of your content within 24 hours (business days).</p><p>This process could take several minutes depending on your selection. Do not interrupt the process. Please wait until you receive a message confirming completion.</p>',
    'ETTM_EXPORT_CMS_SELECTED' => 'CMS pages selected',
    'ETTM_EXPORT_CMS_SELECT' => 'Select CMS pages ',
    'ETTM_EXPORT_CAT_SELECTED' => 'Categories selected',
    'ETTM_EXPORT_CAT_SELECT' => 'Select categories ',
    'ETTM_EXPORT_ATTRIBUTE_SELECTED' => 'Attributes selected',
    'ETTM_EXPORT_ATTRIBUTE_SELECT' => 'Select attributes ',
    'ETTM_EXPORT_ARTICLE_SELECTED' => 'Items selected',
    'ETTM_EXPORT_ARTICLE_SELECT' => 'Select items ',

    'ETTM_EXPORT_TRANSMIT' => 'Submit information',
    'ETTM_EXPORT_TRANSMIT_TEXTBLOCK' => '<p>If you click on the button below, the selected texts will be submitted to the translation portal and you will receive a detailed quotation for the translation of your content within 24 hours (business days).</p><p>This process could take several minutes depending on your selection. Do not interrupt the process. Please wait until you receive a message confirming completion.</p>',
    'ETTM_EXPORT_TRANSMIT_SKIP' => ' - Skip already translated elements.',
    'ETTM_EXPORT_START_AFTER_EXPORT'   => ' - Start the project immediately after export.',

    'ETTM_EXPORT_TRANSMIT_START' => 'Start export',

    'ETTM_EXPORT_STATUS' => 'Status',
    'ETTM_EXPORT_STATUS_TEXTBLOCK1' => '<p>The project is currently being exported to the Eurotext API. Once this is complete, you will receive a quotation from your project manager. Contract customers will receive detailed delivery times and an order confirmation.</p>',
    'ETTM_EXPORT_STATUS_TEXTBLOCK2' => '<p>Export successfully completed.</p>',
    'ETTM_EXPORT_STATUS_TRANSMITTED' => 'Submitted to Eurotext:&nbsp;',
    'ETTM_EXPORT_STATUS_SKIPPED' => 'Skipped:&nbsp;',
    'ETTM_EXPORT_STATUS_ERRORS' => 'Error during submission:&nbsp;',
    'ETTM_EXPORT_STATUS_ITEM' => 'Item(s)',

    'ETTM_EXPORT_TRANSLATION_START' => 'Start project',

    'ETTM_IMPORT_GENERAL' => 'General settings',
    'ETTM_IMPORT_GENERAL_PROJECT_NAME' => 'Project name:',
    'ETTM_IMPORT_GENERAL_STARTLANG' => 'Source language:',
    'ETTM_IMPORT_GENERAL_TARGETLANG' => 'Target language(s):',
    'ETTM_IMPORT_STATUS' => 'Status',
    'ETTM_IMPORT_IMPORT' => 'Import project',
    'ETTM_IMPORT_IMPORT_TEXTBLOCK' => '<p></p>',
    'ETTM_IMPORT_START' => 'Start import',
    'ETTM_IMPORT_RUNNING' => '<p>The project is currently being imported. Please wait.</p>',
    'ETTM_IMPORT_FINISHED' => '<p>Import completed.</p>',
    'ETTM_IMPORT_STATUS_TRANSLATION' => '<p>The project is currently being processed by Eurotext.</p>',


    'ETTM_LIST_EXTPROJECTID' => 'Eurotext API project ID',
    'ETTM_LIST_PROJECTNAME' => 'Project name',
    'ETTM_LIST_STARTLANG' => 'Source language',
    'ETTM_LIST_TARGETLANG' => 'Target language(s)',
    'ETTM_LIST_PROGRESS' => 'Progress',
    'ETTM_LIST_STATUS' => 'Status',
    'ETTM_LIST_LANSTCHANGE' => 'Last changed',
    'ETTM_LIST_NOTREGISTERED' => 'Not yet registered with Eurotext',

    'EXPORT_ART_SKIPTRANSLATES' => 'Hide already translated elements',

    'ETTM_SUPPORT' => 'Support and help',
    'ETTM_SUPPORT_SUB1' => 'Documentation and help:',
    'ETTM_SUPPORT_SUB2' => 'Support:',
    'ETTM_SUPPORT_SUB1_TEXTBLOCK' => '<p>You can find the documentation for the plugin at any time at <p><a target="_blank" href="https://eurotext.de/dokumentation/oxid/">https://eurotext.de/dokumentation/oxid/</a></p>',
    'ETTM_SUPPORT_SUB2_TEXTBLOCK' => '<p>If you have any questions or problems, please contact your Eurotext project team or write an e-mail to <a href="mailto:translationmanager@eurotext.de?subject=Rückfrage%20translationMANAGER%20für%20OXID 6"><b>translationmanager@eurotext.de</b></a>.</p>',

    'AJAX_DESCRIPTION' => 'Drag the elements from one list to another to assign them to the translation.',
    'EXPORT_MAIN_ALLCMS' => 'All CMS pages',
    'EXPORT_AJAX_CAT' => 'Category filters:',
    'EXPORT_MAIN_CMSINPROJECT' => 'Selected CMS pages',
    'EXPORT_MAIN_ALLATTRIBUTE' => 'All attributes',
    'EXPORT_MAIN_ATTRIBUTESINPROJECT' => 'Selected attributes',
    'EXPORT_MAIN_ALLCAT' => 'All categories',
    'EXPORT_MAIN_CATINPROJECT' => 'Selected categories',
    'EXPORT_MAIN_ALLARTICLES' => 'All items',
    'EXPORT_MAIN_ARTICLESINPROJECT' => 'Selected items',
    'EXPORT_MAIN_DATERANGE' => 'Select items that were created or updated in a specific time period.',
    'EXPORT_MAIN_DATERANGE_CRTD' => 'Created',
    'EXPORT_MAIN_DATERANGE_UPDTD' => 'Updated',
    'EXPORT_MAIN_FROM' => 'in the period from',
    'EXPORT_MAIN_TO' => 'until',
    'EXPORT_MAIN_FILTER' => 'Filter',
    'ETTM_AJAX_OXTITLE' => 'Title',
    'ETTM_AJAX_OXDESC' => 'Description',
    'ETTM_AJAX_OXARTNUM' => 'Item no.:',
    'ETTM_AJAX_OXEAN' => 'EAN',
    'ETTM_AJAX_OXLOADID' => 'Ident',
    'ETTM_AJAX_OXACTIVE' => 'Active',
    'ETTM_AJAX_OXSHOPID' => 'ShopID',
    'ALBANIAN'                         => 'Albanian',
    'ARABIC'                           => 'Arabic',
    'ARABIC_AE'                        => 'Arabic (AE)',
    'ARABIC_ALGERIA'                   => 'Arabic (Algeria)',
    'ARABIC_EGYPT'                     => 'Arabic (Egypt)',
    'ARABIC_KUWAIT'                    => 'Arabic (Kuwait)',
    'ARABIC_MOROCCO'                   => 'Arabic (Morocco)',
    'ARABIC_SAUDIARABIA'               => 'Arabic (Saudi Arabia)',
    'AZERBAIJANI_LATIN'                => 'Azerbaijani',
    'BYELORUSSIAN'                     => 'Belarusian',
    'BULGARIAN'                        => 'Bulgarian',
    'BURMESE'                          => 'Burmese',
    'BOSNIAN'                          => 'Bosnian',
    'CATALAN'                          => 'Catalan',
    'CHINESE_HK'                       => 'Chinese (HK)',
    'CHINESE_TAIWAN'                   => 'Chinese (Taiwan)',
    'CHINESE_PRC'                      => 'Chinese (PRC)',
    'DANISH'                           => 'Danish',
    'GERMAN_DE'                        => 'German (DE)',
    'GERMAN_AT'                        => 'German (AT)',
    'GERMAN_CH'                        => 'German (CH)',
    'ENGLISH_GB'                       => 'English (GB)',
    'ENGLISH_CA'                       => 'English (Canada)',
    'ENGLISH_IE'                       => 'English (Ireland)',
    'ENGLISH_NZ'                       => 'English (New Zealand)',
    'ENGLISH_AU'                       => 'English (Australia)',
    'ENGLISH_US'                       => 'English (US)',
    'ENGLISH'                          => 'English',
    'AFRICAANS'                        => 'Afrikaans',
    'ESTONIAN'                         => 'Estonian',
    'FINNISH'                          => 'Finnish',
    'FRENCH_BE'                        => 'French (BE)',
    'FRENCH_CA'                        => 'French (CA)',
    'FRENCH_CH'                        => 'French (CH)',
    'FRENCH_FR'                        => 'French (FR)',
    'FRENCH_LU'                        => 'French (LU)',
    'GALICIAN'                         => 'Galician',
    'GURAJATI'                         => 'Gujarati',
    'GREEK'                            => 'Greek',
    'HEBREW'                           => 'Hebrew',
    'HMONG'                            => 'Hmong',
    'HINDI'                            => 'Hindi',
    'INDONESIAN'                       => 'Indonesian',
    'ICELANDIC'                        => 'Icelandic',
    'ITALY_CH'                         => 'Italian (CH)',
    'ITALIAN'                          => 'Italian (IT)',
    'JAPANESE'                         => 'Japanese',
    'KANNADA'                          => 'Canada',
    'KAZAKH'                           => 'Kazakh',
    'KHMER'                            => 'Khmer',
    'KOREAN'                           => 'Korean',
    'CROATIAN'                         => 'Croatian',
    'LAO'                              => 'Lao',
    'LATVIAN'                          => 'Latvian',
    'LITHUANIAN'                       => 'Lithuanian',
    'MACEDONIAN'                       => 'Macedonian',
    'MALAY'                            => 'Malay',
    'NORWEGIAN_BOKMAL'                 => 'Norwegian (Bokm&aring;l)',
    'NORWEGIAN_NYNORSK'                => 'Norwegian (Nynorsk)',
    'NORWEGIAN'                        => 'Norwegian',
    'DUTCH_BE'                         => 'Dutch (BE)',
    'DUTCH_NL'                         => 'Dutch (NL)',
    'POLISH'                           => 'Polish',
    'PORTUGUESE_BR'                    => 'Portuguese (BR)',
    'PORTUGUESE_PT'                    => 'Portuguese (PT)',
    'ROMANIAN'                         => 'Romanian',
    'RUSSIAN'                          => 'Russian',
    'SWEDISH'                          => 'Swedish',
    'SERBIAN'                          => 'Serbian',
    'SERBIAN_CYRILLIC'                 => 'Serbian (Cyrillic)',
    'SERBIAN_LATIN'                    => 'Serbian (Latin)',
    'SLOVAK'                           => 'Slovak',
    'SLOVENIAN'                        => 'Slovenian',
    'SPANISH_ES'                       => 'Spanish (ES)',
    'SPANISH_INTERNATIONAL_SORT'       => 'Spanish (International Sort)',
    'SPANISH_AR'                       => 'Spanish (Argentina)',
    'SPANISH_CL'                       => 'Spanish (Chile)',
    'SPANISH_CO'                       => 'Spanish (Colombia)',
    'SPANISH_CR'                       => 'Spanish (Costa Rica)',
    'SPANISH_MX'                       => 'Spanish (Mexico)',
    'SPANISH_PA'                       => 'Spanish (Panama)',
    'SPANISH_PE'                       => 'Spanish (Peru)',
    'SPANISH_VE'                       => 'Spanish (Venezuela)',
    'THAI'                             => 'Thai',
    'CZECH'                            => 'Czech',
    'TURKISH'                          => 'Turkish',
    'UKRANIAN'                         => 'Ukrainian',
    'HUNGARIAN'                        => 'Hungarian',
    'VIETNAMESE'                       => 'Vietnamese',
    'WELSH'                            => 'Welsh',
    'Afghanistan'                      => 'Afghanistan',
    'Egypt'                            => 'Egypt',
    'Albania'                          => 'Albania',
    'Algeria'                          => 'Algeria',
    'Andorra'                          => 'Andorra',
    'Angola'                           => 'Angola',
    'Antigua_and_Barbuda'              => 'Antigua and Barbuda',
    'Equatorial_Guinea'                => 'Equatorial Guinea',
    'Argentina'                        => 'Argentina',
    'Armenia'                          => 'Armenia',
    'Azerbaijan'                       => 'Azerbaijan',
    'Ethiopia'                         => 'Ethiopia',
    'Australia'                        => 'Australia',
    'Bahamas'                          => 'Bahamas',
    'Bahrain'                          => 'Bahrain',
    'Bangladesh'                       => 'Bangladesh',
    'Barbados'                         => 'Barbados',
    'Belarus'                          => 'Belarus',
    'Belgium'                          => 'Belgium',
    'Belize'                           => 'Belize',
    'Benin'                            => 'Benin',
    'Bhutan'                           => 'Bhutan',
    'Bolivia'                          => 'Bolivia',
    'Bosnia_and_Herzegovina'           => 'Bosnia-Herzegovina',
    'Botswana'                         => 'Botswana',
    'Brazil'                           => 'Brazil',
    'Brunei_Darussalam'                => 'Brunei Darussalam',
    'Bulgaria'                         => 'Bulgaria',
    'Burkina_Faso'                     => 'Burkina Faso',
    'Burundi'                          => 'Burundi',
    'Cayman_Islands'                   => 'Cayman Islands',
    'Chile'                            => 'Chile',
    'China'                            => 'China',
    'Cook_Islands'                     => 'Cook Islands',
    'Costa_Rica'                       => 'Costa Rica',
    'Cote_d_Ivoire'                    => 'Ivory Coast',
    'Denmark'                          => 'Denmark',
    'Dominica'                         => 'Dominica',
    'Dominican_Republic'               => 'Dominican Republic',
    'Jamahiriya'                       => 'Jamahiriya',
    'Djibouti'                         => 'Djibouti',
    'Ecuador'                          => 'Ecuador',
    'El_Salvador'                      => 'El Salvador',
    'England'                          => 'England',
    'Eritrea'                          => 'Eritrea',
    'Estonia'                          => 'Estonia',
    'Fiji'                             => 'Fiji',
    'Finland'                          => 'Finland',
    'France'                           => 'France',
    'Gabon'                            => 'Gabon',
    'Gambia'                           => 'Gambia',
    'Georgia'                          => 'Georgia',
    'Germany'                          => 'Germany',
    'Ghana'                            => 'Ghana',
    'Grenada'                          => 'Grenada',
    'Greece'                           => 'Greece',
    'Guatemala'                        => 'Guatemala',
    'Guinea'                           => 'Guinea',
    'Guinea-Bissau'                    => 'Guinea-Bissau',
    'Guyana'                           => 'Guyana',
    'Haiti'                            => 'Haiti',
    'Holy See'                         => 'Holy See',
    'Honduras'                         => 'Honduras',
    'India'                            => 'India',
    'Indonesia'                        => 'Indonesia',
    'Iraq'                             => 'Iraq',
    'Iran'                             => 'Iran',
    'Ireland'                          => 'Ireland',
    'Iceland'                          => 'Iceland',
    'Israel'                           => 'Israel',
    'Italy'                            => 'Italy',
    'Jamaica'                          => 'Jamaica',
    'Japan'                            => 'Japan',
    'Yemen'                            => 'Yemen',
    'Jordan'                           => 'Jordan',
    'Yugoslavia'                       => 'Yugoslavia',
    'Cambodia'                         => 'Cambodia',
    'Cameroon'                         => 'Cameroon',
    'Canada'                           => 'Canada',
    'Cape_Verde'                       => 'Cape Verde',
    'Kazachstan'                       => 'Kazakhstan',
    'Katar'                            => 'Qatar',
    'Kenya'                            => 'Kenya',
    'Kyrgyzstan'                       => 'Kyrgyzstan',
    'Kiribati'                         => 'Kiribati',
    'Columbia'                         => 'Colombia',
    'Komor'                            => 'Comoros',
    'Congo'                            => 'Congo',
    'Congo_Dem_Republic_of'            => 'Congo, Dem. Republic',
    'Korea_Dem_People'                 => 'Korea, Dem. People&rsquo;s Republic',
    'Korea_Republic'                   => 'Korea, Republic',
    'Kosovo'                           => 'Kosovo',
    'Croatia'                          => 'Croatia',
    'Cuba'                             => 'Cuba',
    'Kuwait'                           => 'Kuwait',
    'Laos'                             => 'Laos',
    'Lesotho'                          => 'Lesotho',
    'Latvia'                           => 'Latvia',
    'Lebanon'                          => 'Lebanon',
    'Liberia'                          => 'Liberia',
    'Libyan_Arab'                      => 'Libyan Arab',
    'Liechtenstein'                    => 'Liechtenstein',
    'Lithuania'                        => 'Lithuania',
    'Luxembourg'                       => 'Luxembourg',
    'Madagascar'                       => 'Madagascar',
    'Malawi'                           => 'Malawi',
    'Malaysia'                         => 'Malaysia',
    'Maldives'                         => 'Maldives',
    'Mali'                             => 'Mali',
    'Malta'                            => 'Malta',
    'Morocco'                          => 'Morocco',
    'Marshall_Islands'                 => 'Marshall Islands',
    'Mauritania'                       => 'Mauritania',
    'Mauritius'                        => 'Mauritius',
    'Macedonia'                        => 'Macedonia',
    'Mexico'                           => 'Mexico',
    'Micronesia'                       => 'Micronesia',
    'Moldova'                          => 'Moldavia',
    'Monaco'                           => 'Monaco',
    'Mongolia'                         => 'Mongolia',
    'Mozambique'                       => 'Mozambique',
    'Myanmar'                          => 'Myanmar',
    'Namibia'                          => 'Namibia',
    'Nauru'                            => 'Nauru',
    'Nepal'                            => 'Nepal',
    'New_Zealand'                      => 'New Zealand',
    'Nicaragua'                        => 'Nicaragua',
    'Netherlands'                      => 'Netherlands',
    'Niger'                            => 'Niger',
    'Nigeria'                          => 'Nigeria',
    'Niue'                             => 'Niue',
    'Norway'                           => 'Norway',
    'Oman'                             => 'Oman',
    'Austria'                          => 'Austria',
    'East_Timor'                       => 'East Timor',
    'Pakistan'                         => 'Pakistan',
    'Palau'                            => 'Palau',
    'Palestine'                        => 'Palestine',
    'Panama'                           => 'Panama',
    'Papua_New_Guinea'                 => 'Papua New Guinea',
    'Paraguay'                         => 'Paraguay',
    'Peru'                             => 'Peru',
    'Philippines'                      => 'Philippines',
    'Poland'                           => 'Poland',
    'Portugal'                         => 'Portugal',
    'Rwanda'                           => 'Rwanda',
    'Romania'                          => 'Romania',
    'Russian_Federation'               => 'Russian Federation',
    'Solomon_Islands'                  => 'Solomon Islands',
    'Zambia'                           => 'Zambia',
    'Samoa'                            => 'Samoa',
    'San_Marino'                       => 'San Marino',
    'Sao_Tome_and_Principe'            => 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe',
    'Saudi_Arabia'                     => 'Saudi Arabia',
    'Sweden'                           => 'Sweden',
    'Switzerland'                      => 'Switzerland',
    'Senegal'                          => 'Senegal',
    'Serbia'                           => 'Serbia',
    'Seychelles'                       => 'Seychelles',
    'Sierra_Leone'                     => 'Sierra Leone',
    'Zimbabwe'                         => 'Zimbabwe',
    'Singapore'                        => 'Singapore',
    'Slovakia'                         => 'Slovakia',
    'Slovenia'                         => 'Slovenia',
    'Somalia'                          => 'Somalia',
    'Spain'                            => 'Spain',
    'Sri_Lanka'                        => 'Sri Lanka',
    'Saint_Kitts_and_Nevis'            => 'St. Kitts and Nevis',
    'Saint_Lucia'                      => 'St. Lucia',
    'Saint_Vincent_and_the_Grenadines' => 'St. Vincent / Grenadines',
    'South_Africa'                     => 'South Africa',
    'Sudan'                            => 'Sudan',
    'Suriname'                         => 'Suriname',
    'Swaziland'                        => 'Swaziland',
    'Syria'                            => 'Syria',
    'Tajikistan'                       => 'Tadzhikistan',
    'Taiwan'                           => 'Taiwan',
    'Tanzania'                         => 'Tanzania',
    'Thailand'                         => 'Thailand',
    'Togo'                             => 'Togo',
    'Tonga'                            => 'Tonga',
    'Trinidad_and_Tobago'              => 'Trinidad and Tobago',
    'Chad'                             => 'Chad',
    'Czech_Republic'                   => 'Czech Republic',
    'Tunisia'                          => 'Tunisia',
    'Turkey'                           => 'Turkey',
    'Turkmenistan'                     => 'Turkmenistan',
    'Tuvalu'                           => 'Tuvalu',
    'Uganda'                           => 'Uganda',
    'Ukraine'                          => 'Ukraine',
    'Hungary'                          => 'Hungary',
    'Uruguay'                          => 'Uruguay',
    'USA'                              => 'USA',
    'Uzbekistan'                       => 'Uzbekistan',
    'Vanuatu'                          => 'Vanuatu',
    'Vatican_City_State'               => 'Vatican City',
    'Venezuela'                        => 'Venezuela',
    'United_Arabian_Emirates'          => 'United Arab Emirates',
    'United_Kingdom'                   => 'United Kingdom',
    'Vietnam'                          => 'Vietnam',
    'Central_African_Republic'         => 'Central African Republic',
    'Cyprus'                           => 'Cyprus',
];
