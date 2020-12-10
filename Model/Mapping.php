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
class Mapping
{

    /**
     * Array of Eurotext languages
     * short abbreviation => Full name
     * (e.g. de-de => Deutsch (DE))
     *
     * @var array
     */
    protected $_aDefaultLangArray = [
        "de-de" => 'GERMAN_DE',
        "en-gb" => 'ENGLISH_GB',
        "en-us" => 'ENGLISH_US',
        "fr-fr" => 'FRENCH_FR',
        "it-it" => 'ITALIAN',
        "nl-nl" => 'DUTCH_NL',
        "es-es" => 'SPANISH_ES',
        "af"    => 'AFRICAANS',
        "alb"   => 'ALBANIAN',
        "ar-dz" => 'ARABIC_ALGERIA',
        "ar-eg" => 'ARABIC_EGYPT',
        "ar-kw" => 'ARABIC_KUWAIT',
        "ar-ma" => 'ARABIC_MOROCCO',
        "ar-sa" => 'ARABIC_SAUDIARABIA',
        "az"    => 'AZERBAIJANI_LATIN',
        "bos"   => 'BOSNIAN',
        "bg"    => 'BULGARIAN',
        "my"    => 'BURMESE',
        "zh-cn" => 'CHINESE_PRC',
        "zh-hk" => 'CHINESE_HK',
        "zh-tw" => 'CHINESE_TAIWAN',
        "da"    => 'DANISH',
        "de-at" => 'GERMAN_AT',
        "de-ch" => 'GERMAN_CH',
        "en-au" => 'ENGLISH_AU',
        "en-ca" => 'ENGLISH_CA',
        "en-ie" => 'ENGLISH_IE',
        "en-nz" => 'ENGLISH_NZ',
        "et"    => 'ESTONIAN',
        "fi-fi" => 'FINNISH',
        "fr-be" => 'FRENCH_BE',
        "fr-ca" => 'FRENCH_CA',
        "fr-ch" => 'FRENCH_CH',
        "fr-lu" => 'FRENCH_LU',
        "glg"   => 'GALICIAN',
        "gur"   => 'GURAJATI',
        "el"    => 'GREEK',
        "he"    => 'HEBREW',
        "hi"    => 'HINDI',
        "hmn"   => 'HMONG',
        "ind"   => 'INDONESIAN',
        "ice"   => 'ICELANDIC',
        "it-ch" => 'ITALY_CH',
        "ja"    => 'JAPANESE',
        "kn"    => 'KANNADA',
        "cat"   => 'CATALAN',
        "kk"    => 'KAZAKH',
        "ko-kr" => 'KOREAN',
        "hr"    => 'CROATIAN',
        "lv"    => 'LATVIAN',
        "lt-lt" => 'LITHUANIAN',
        "mk"    => 'MACEDONIAN',
        "msa"   => 'MALAY',
        "nl-be" => 'DUTCH_BE',
        "no-no" => 'NORWEGIAN',
        "no-bo" => 'NORWEGIAN_BOKMAL',
        "no-nn" => 'NORWEGIAN_NYNORSK',
        "pl"    => 'POLISH',
        "pt-br" => 'PORTUGUESE_BR',
        "pt-pt" => 'PORTUGUESE_PT',
        "ro-ro" => 'ROMANIAN',
        "ru-ru" => 'RUSSIAN',
        "sv-se" => 'SWEDISH',
        "sh-sr" => 'SERBIAN',
        "rs-sr" => 'SERBIAN_CYRILLIC',
        "sr"    => 'SERBIAN_LATIN',
        "sk"    => 'SLOVAK',
        "sl"    => 'SLOVENIAN',
        "es-ar" => 'SPANISH_AR',
        "es-cl" => 'SPANISH_CL',
        "es-co" => 'SPANISH_CO',
        "es-cr" => 'SPANISH_CR',
        "es-mx" => 'SPANISH_MX',
        "es-pa" => 'SPANISH_PA',
        "es-pe" => 'SPANISH_PE',
        "es-ve" => 'SPANISH_VE',
        "th"    => 'THAI',
        "cz-cz" => 'CZECH',
        "tr"    => 'TURKISH',
        "uk"    => 'UKRANIAN',
        "hu"    => 'HUNGARIAN',
        "vn"    => 'VIETNAMESE',
        "wel"   => 'WELSH',
        "be"    => 'BYELORUSSIAN',
    ];

    /**
     * Return selected mapping.
     *
     * @return array
     */
    public function getMapping()
    {
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sShopId = $oConfig->getShopId();
        $languages = $oLang->getLanguageArray();
        $mapping = [];

        foreach ($languages as $language) {
            // $language->abbr -- gives us short name of language, like "de"
            $sVarName = 'sEttmLang_' . $language->abbr;
            $mapping[$language->abbr] = $oConfig->getShopConfVar($sVarName, $sShopId, 'module:translationmanager6');
        }

        return $mapping;
    }

    /**
     * Return selected mapping.
     *
     * @return array
     */
    public function getReverseMapping()
    {
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sShopId = $oConfig->getShopId();
        $languages = $oLang->getLanguageArray();
        $mapping = [];

        foreach ($languages as $language) {
            // $language->abbr -- gives us short name of language, like "de"
            $sVarName = 'sEttmLang_' . $language->abbr;
            $mapping[$oConfig->getShopConfVar($sVarName, $sShopId, 'module:translationmanager6')] = $language->abbr;
        }

        return $mapping;
    }

    /**
     * Return language array.
     *
     * @return array
     */
    public function getDefaultLangArray()
    {
        return $this->_aDefaultLangArray;
    }
}
