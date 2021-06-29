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
class Settings extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'translationmanager6_settings.tpl';

    /**
     * Executes parent method parent::render()
     *
     * @return string
     */
    public function render()
    {
        $oConfig = $this->getConfig();
        parent::render();

        $sOxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        if (!$sOxId) {
            $sOxId = $oConfig->getShopId();
        }

        $oMapping = oxNew('\Eurotext\Translationmanager\Model\Mapping');
        $this->_aViewData['ettmlanguages'] = $oMapping->getDefaultLangArray();

        $this->_aViewData['oxid'] =  $sOxId;

        $this->_aViewData['confstrs'] = [];

        $sql = "SELECT `OXVARNAME`, DECODE( `OXVARVALUE`, ? ) AS `OXVARVALUE` FROM `oxconfig` WHERE `OXSHOPID` = ? AND `OXMODULE` = 'module:translationmanager6'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sql,
            [$oConfig->getConfigParam('sConfigKey'), $sOxId]
        );

        foreach ($resultSet as $result) {
            $this->_aViewData['confstrs'][$result[0]] = $result[1];
        }

        // Get list of cms fields
        $sQuery = "SHOW COLUMNS FROM oxcontents WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            []
        );
        $selectedCMSFields = unserialize($this->_aViewData['confstrs']['cmsfields']);
        $this->_aViewData['cms_fields'] = [];
        foreach ($resultSet as $result) {
            $this->_aViewData['cms_fields'][] = [
                'name' => $result[0],
                'selected' => in_array($result[0], $selectedCMSFields),
            ];
        }

        // Get list of category fields
        $sQuery = "SHOW COLUMNS FROM oxcategories WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            []
        );
        $selectedCategoryFields = unserialize($this->_aViewData['confstrs']['categoryfields']);
        $this->_aViewData['category_fields'] = [];
        foreach ($resultSet as $result) {
            $this->_aViewData['category_fields'][] = [
                'name' => $result[0],
                'selected' => in_array($result[0], $selectedCategoryFields),
            ];
        }

        // Get list of attribute fields
        $sQuery = "SHOW COLUMNS FROM oxattribute WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            []
        );
        $selectedAttributesFields = unserialize($this->_aViewData['confstrs']['attributesfields']);
        $this->_aViewData['attributes_fields'] = [];
        foreach ($resultSet as $result) {
            $this->_aViewData['attributes_fields'][] = [
                'name' => $result[0],
                'selected' => in_array($result[0], $selectedAttributesFields),
            ];
        }

        // Get list of attribute fields
        $sQuery = "SHOW COLUMNS FROM oxobject2attribute WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            []
        );
        $selectedOAttributesFields = unserialize($this->_aViewData['confstrs']['o2attributesfields']);
        $this->_aViewData['o2attributes_fields'] = [];
        foreach ($resultSet as $result) {
            $this->_aViewData['o2attributes_fields'][] = [
                'name' => $result[0],
                'selected' => in_array($result[0], $selectedOAttributesFields),
            ];
        }

        // Get list of articles fields
        $sQuery = "SHOW COLUMNS FROM oxarticles WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            []
        );
        $selectedArticlesFields = unserialize($this->_aViewData['confstrs']['articlesfields']);
        $this->_aViewData['articles_fields'] = [];
        foreach ($resultSet as $result) {
            $this->_aViewData['articles_fields'][] = [
                'name' => $result[0],
                'selected' => in_array($result[0], $selectedArticlesFields),
            ];
        }

        // Get list of articles fields
        $sQuery = "SHOW COLUMNS FROM oxartextends WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            []
        );
        $selectedArticlesExtFields = unserialize($this->_aViewData['confstrs']['artextendsfields']);
        $this->_aViewData['artextends_fields'] = [];
        foreach ($resultSet as $result) {
            $this->_aViewData['artextends_fields'][] = [
                'name' => $result[0],
                'selected' => in_array($result[0], $selectedArticlesExtFields),
            ];
        }

        // Get list of cms fields
        $sQuery = "SHOW COLUMNS FROM oxobject2seodata WHERE (Type LIKE '%char%' OR Type LIKE '%text%') AND Field != 'OXOBJECTID'";
        $resultSet = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll(
            $sQuery,
            []
        );
        $selectedCMSSEOFields = unserialize($this->_aViewData['confstrs']['cmsseofields']);
        $selectedCategorySEOFields = unserialize($this->_aViewData['confstrs']['categoryseofields']);
        $selectedArticleSEOFields = unserialize($this->_aViewData['confstrs']['articleseofields']);
        $this->_aViewData['cmsseo_fields'] = [];
        $this->_aViewData['categoryseo_fields'] = [];
        $this->_aViewData['articleseo_fields'] = [];

        foreach ($resultSet as $result) {
            $this->_aViewData['cmsseo_fields'][] = [
                'name' => $result[0],
                'selected' => in_array($result[0], $selectedCMSSEOFields),
            ];

            $this->_aViewData['categoryseo_fields'][] = [
                'name' => $result[0],
                'selected' => in_array($result[0], $selectedCategorySEOFields),
            ];

            $this->_aViewData['articleseo_fields'][] = [
                'name' => $result[0],
                'selected' => in_array($result[0], $selectedArticleSEOFields),
            ];
        }

        return $this->_sThisTemplate;
    }

    /**
     * Saves changed modules configuration parameters.
     *
     * @return void
     */
    public function save()
    {
        $oConfig = $this->getConfig();

        $sOxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $aConfStrs = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('confstrs');

        // Check if API Key Works
        $uri = '/api/v1/info/whoami.json';
        $headers = [
            'Content-Type' => 'application/json',
            'apikey' => $aConfStrs['sAPIKEY'],
        ];
        $client = new \GuzzleHttp\Client([
            'base_uri' => $aConfStrs['sSERVICEURL'],
            'timeout'  => 6.0,
        ]);
        $status = 0;
        try {
            $response = $client->request(
                'GET',
                $uri,
                [
                    'headers' => $headers
                ]
            );
            if (200 === $response->getStatusCode()) {
                $status = 1;
            }
        } catch (\Exception $e) {
            // Do nothing
        }

        if (
            class_exists(\OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::class)
        ) {
            $moduleSettingBridge = \OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::getInstance()
                ->getContainer()
                ->get(\OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface::class);

            if (is_array($aConfStrs)) {
                foreach ($aConfStrs as $sVarName => $sVarVal) {
                    $moduleSettingBridge->save($sVarName, $sVarVal, 'translationmanager6');
                }
            }

            // Save cms fields
            $aCmsFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('cmsfields');
            $moduleSettingBridge->save('cmsfields', $aCmsFields, 'translationmanager6');

            // Save category fields
            $aCategoryFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('categoryfields');
            $moduleSettingBridge->save('categoryfields', $aCategoryFields, 'translationmanager6');

            // Save attributes fields
            $aAttributeFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('attributesfields');
            $moduleSettingBridge->save('attributesfields', $aAttributeFields, 'translationmanager6');

            // Save attributes fields
            $aOAttributeFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('o2attributesfields');
            $moduleSettingBridge->save('o2attributesfields', $aOAttributeFields, 'translationmanager6');

            // Save attributes fields
            $aArticlesFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('articlesfields');
            $moduleSettingBridge->save('articlesfields', $aArticlesFields, 'translationmanager6');

            // Save attributes fields
            $aArticlesExtFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('artextendsfields');
            $moduleSettingBridge->save('artextendsfields', $aArticlesExtFields, 'translationmanager6');

            // Save attributes fields
            $aArticlesExtFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('cmsseofields');
            $moduleSettingBridge->save('cmsseofields', $aArticlesExtFields, 'translationmanager6');

            // Save attributes fields
            $aArticlesExtFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('categoryseofields');
            $moduleSettingBridge->save('categoryseofields', $aArticlesExtFields, 'translationmanager6');

            // Save attributes fields
            $aArticlesExtFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('articleseofields');
            $moduleSettingBridge->save('articleseofields', $aArticlesExtFields, 'translationmanager6');

            $moduleSettingBridge->save('sCONNSTATUS', $status, 'translationmanager6');
        } else {
            if (is_array($aConfStrs)) {
                foreach ($aConfStrs as $sVarName => $sVarVal) {
                    $oConfig->saveShopConfVar('str', $sVarName, $sVarVal, $sOxId, 'module:translationmanager6');
                }
            }

            // Save cms fields
            $aCmsFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('cmsfields');
            $oConfig->saveShopConfVar('arr', 'cmsfields', $aCmsFields, $sOxId, 'module:translationmanager6');

            // Save category fields
            $aCategoryFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('categoryfields');
            $oConfig->saveShopConfVar('arr', 'categoryfields', $aCategoryFields, $sOxId, 'module:translationmanager6');

            // Save attributes fields
            $aAttributeFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('attributesfields');
            $oConfig->saveShopConfVar('arr', 'attributesfields', $aAttributeFields, $sOxId, 'module:translationmanager6');

            // Save attributes fields
            $aOAttributeFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('o2attributesfields');
            $oConfig->saveShopConfVar('arr', 'o2attributesfields', $aOAttributeFields, $sOxId, 'module:translationmanager6');

            // Save attributes fields
            $aArticlesFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('articlesfields');
            $oConfig->saveShopConfVar('arr', 'articlesfields', $aArticlesFields, $sOxId, 'module:translationmanager6');

            // Save attributes fields
            $aArticlesExtFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('artextendsfields');
            $oConfig->saveShopConfVar('arr', 'artextendsfields', $aArticlesExtFields, $sOxId, 'module:translationmanager6');

            // Save attributes fields
            $aArticlesExtFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('cmsseofields');
            $oConfig->saveShopConfVar('arr', 'cmsseofields', $aArticlesExtFields, $sOxId, 'module:translationmanager6');

            // Save attributes fields
            $aArticlesExtFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('categoryseofields');
            $oConfig->saveShopConfVar('arr', 'categoryseofields', $aArticlesExtFields, $sOxId, 'module:translationmanager6');

            // Save attributes fields
            $aArticlesExtFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('articleseofields');
            $oConfig->saveShopConfVar('arr', 'articleseofields', $aArticlesExtFields, $sOxId, 'module:translationmanager6');

            $oConfig->saveShopConfVar('str', 'sCONNSTATUS', $status, $sOxId, 'module:translationmanager6');
        }

        return;
    }
}
