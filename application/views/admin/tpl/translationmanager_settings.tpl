[{include file="headitem.tpl" title="[ Eurotext ]"}]
[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{ else }]
    [{assign var="readonly" value=""}]
[{ /if }]

<link rel="stylesheet" href="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/css/ettm_styles.css")}]">
<script type="text/javascript" src="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/js/uikit-core.min.js")}]"></script>
<script type="text/javascript" src="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/js/ettm_settings.js")}]"></script>
<div class="ettm">

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="translationmanager_settings" />
    <input type="hidden" name="fnc" value="" />
    <input type="hidden" name="oxid" value="[{$oxid}]" />
    <h1>[{oxmultilang ident="ETTM_SETTINGS"}]</h1>
    <div class="uk-grid uk-grid-small" uk-grid>
        <div class="uk-width-1-1">
            <fieldset>
                <legend><strong>[{oxmultilang ident="ETTM_SETTINGS_GENERAL"}]</strong></legend>
                <table class="uk-margin">
                    <tr>
                        <td>
                            [{oxmultilang ident="ETTM_SETTINGS_APIKEY"}]
                        </td>
                        <td>
                            <input type="text" class="editinput" size="60" maxlength="255" name="confstrs[sAPIKEY]" value="[{$confstrs.sAPIKEY}]">
                            &nbsp;[{ oxinputhelp ident="ETTM_SETTINGS_APIKEYHELP" }]
                        </td>
                    </tr>
                    <tr>
                        <td>
                            [{oxmultilang ident="ETTM_SETTINGS_STATUS"}]
                        </td>
                        <td>
                            [{if $confstrs.sCONNSTATUS == 1}]
                                <span class="ettm-green-status">
                                    <strong>
                                        [{oxmultilang ident="ETTM_SETTINGS_STATUS_OK"}]
                                    </strong>
                                    [{oxmultilang ident="ETTM_SETTINGS_STATUS_OK_LONG"}]
                                    &nbsp;[{ oxinputhelp ident="ETTM_SETTINGS_STATUS_OK_HELP" }]
                                </span>
                            [{else}]
                                <span class="ettm-red-status">
                                    <strong>
                                        [{oxmultilang ident="ETTM_SETTINGS_STATUS_FAIL"}]
                                    </strong>
                                    [{oxmultilang ident="ETTM_SETTINGS_STATUS_FAIL_LONG"}]
                                    &nbsp;[{ oxinputhelp ident="ETTM_SETTINGS_STATUS_FAIL_HELP" }]
                                </span>
                            [{/if}]
                        </td>
                    </tr>
                    <tr>
                        <td>
                            [{oxmultilang ident="ETTM_SETTINGS_ENDPOINT"}]
                        </td>
                        <td>
                            <select type="text" class="editinput" name="confstrs[sSERVICEURL]">
                                <option value="https://api.eurotext.de" [{if $confstrs.sSERVICEURL == 'https://api.eurotext.de'}]selected="selected"[{/if}]>https://api.eurotext.de ([{oxmultilang ident="ETTM_SETTINGS_ENDPOINT_LIVE"}])</option>
                                <option value="https://sandbox.api.eurotext.de" [{if $confstrs.sSERVICEURL == 'https://sandbox.api.eurotext.de'}]selected="selected"[{/if}]>https://sandbox.api.eurotext.de ([{oxmultilang ident="ETTM_SETTINGS_ENDPOINT_SANDBOX"}])</option>
                                <option value="https://stage.api.eurotext.de" [{if $confstrs.sSERVICEURL == 'https://stage.api.eurotext.de'}]selected="selected"[{/if}]>https://stage.api.eurotext.de ([{oxmultilang ident="ETTM_SETTINGS_ENDPOINT_STAGE"}])</option>
                            </select>
                            &nbsp;[{ oxinputhelp ident="ETTM_HELP_SOURCE" }]
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>

        <div class="uk-width-1-1">
            <fieldset>
                <legend><strong>[{oxmultilang ident="ETTM_SETTINGS_LANGUAGES"}]</strong></legend>

                <table class="uk-margin">
                    <tr>
                        <td>[{oxmultilang ident="ETTM_SETTINGS_SHOP_LANGUAGE"}]&nbsp;</td>
                        <td>&#8646;</td>
                        <td>[{oxmultilang ident="ETTM_SETTINGS_EUROTEXT_LANGUAGE"}]</td>
                    </tr>

                    [{if $languages}]
                        [{ foreach from=$languages item=lang }]
                            <tr>
                                <td>[{ $lang->name }]</td>
                                <td>&#8646;</td>
                                <td>
                                    <select name="confstrs[sEttmLang_[{$lang->abbr}]]" class="editinput" [{ $readonly}]>
                                        [{ assign var="sEttmLang" value="sEttmLang_"|cat:$lang->abbr}]
                                        [{foreach key=key item=item from=$ettmlanguages}]
                                            <option value="[{$key}]" [{if $confstrs.$sEttmLang == $key}] selected="selected"[{/if}]>[{$item|oxmultilangassign}]</option>
                                        [{/foreach}]
                                    </select>
                                </td>
                            </tr>
                        [{ /foreach }]
                    [{/if}]
                </table>
            </fieldset>
        </div>

        <div class="uk-width-1-1">
            <fieldset>
                <legend><strong>[{oxmultilang ident="ETTM_SETTINGS_CRON"}]</strong></legend>

                <table class="uk-margin">
                    <tr>
                        <td>[{oxmultilang ident="ETTM_SETTINGS_CRON_NAME"}]</td>
                        <td>[{oxmultilang ident="ETTM_SETTINGS_CRON_ITM"}]</td>
                    </tr>

                    <tr>
                        <td><strong>[{oxmultilang ident="ETTM_SETTINGS_CRON_EXPORT"}]</strong></td>
                        <td><input type="number" class="editinput" name="confstrs[sEXPORTJOBIPJ]" value="[{$confstrs.sEXPORTJOBIPJ}]">
                        &nbsp;[{ oxinputhelp ident="ETTM_HELP_EXPORTJOBIPJ" }]</td>
                    </tr>
                    <tr>
                        <td><strong>[{oxmultilang ident="ETTM_SETTINGS_CRON_IMPORT"}]</strong></td>
                        <td><input type="number" class="editinput" name="confstrs[sIMPORTJOBIPJ]" value="[{$confstrs.sIMPORTJOBIPJ}]">
                        &nbsp;[{ oxinputhelp ident="ETTM_HELP_IMPORTJOBIPJ" }]</td>
                    </tr>
                </table>
            </fieldset>
        </div>

        <div class="uk-width-1-1">
            <fieldset>
                <legend><strong>[{oxmultilang ident="ETTM_SETTINGS_FIELDS"}]</strong></legend>

                <div class="uk-grid uk-grid-small uk-margin" uk-grid>
                    <div class="uk-width-1-1 uk-width-1-4@s">
                        <h3>[{oxmultilang ident="ETTM_SETTINGS_FIELDS_CMS"}]</h3>
                        <div class="uk-margin-small">[{oxmultilang ident="ETTM_SETTINGS_FIELDS_VIEW"}]<a href="#" class="ettm-view-activator active activate-simple-view">[{oxmultilang ident="ETTM_SETTINGS_FIELDS_VIEW_SIMPLE"}]</a> <a href="#" class="ettm-view-activator activate-ext-view">[{oxmultilang ident="ETTM_SETTINGS_FIELDS_VIEW_EXT"}]</a></div>
                        <div class="ettm-view ettm-view--simple">
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" class="double-binding" data-target="cb_oxcontents_oxtitle"> [{oxmultilang ident="ETTM_SETTINGS_OXCONTENTS_OXTITLE"}]
                                </label>
                            </div>
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" class="double-binding" data-target="cb_oxcontents_oxcontent"> [{oxmultilang ident="ETTM_SETTINGS_OXCONTENTS_OXCONTENT"}]
                                </label>
                            </div>
                        </div>

                        <div class="ettm-view ettm-view--ext" style="display: none">
                            <div class="uk-width-1-1 uk-margin-small">
                                <h4>oxcontents</h4>
                            </div>
                            [{ foreach from=$cms_fields item=field }]
                                <div class="uk-width-1-1 uk-margin-small">
                                    <label>
                                        <input type="checkbox" id="cb_oxcontents_[{$field.name|lower}]" name="cmsfields[]" value="[{$field.name}]" [{if $field.selected}]checked="checked"[{/if}]> [{$field.name}]
                                    </label>
                                </div>
                            [{ /foreach }]
                            <div class="uk-width-1-1 uk-margin-small">
                                <h4>oxobject2seodata</h4>
                            </div>
                            [{ foreach from=$cmsseo_fields item=field }]
                                <div class="uk-width-1-1 uk-margin-small">
                                    <label>
                                        <input type="checkbox" id="cb_cms_oxobject2seodata_[{$field.name|lower}]" name="cmsseofields[]" value="[{$field.name}]" [{if $field.selected}]checked="checked"[{/if}]> [{$field.name}]
                                    </label>
                                </div>
                            [{ /foreach }]

                        </div>
                    </div>

                    <div class="uk-width-1-1 uk-width-1-4@s">
                        <h3>[{oxmultilang ident="ETTM_SETTINGS_FIELDS_ARTICLES"}]</h3>
                        <div class="uk-margin-small">[{oxmultilang ident="ETTM_SETTINGS_FIELDS_VIEW"}]<a href="#" class="ettm-view-activator active activate-simple-view">[{oxmultilang ident="ETTM_SETTINGS_FIELDS_VIEW_SIMPLE"}]</a> <a href="#" class="ettm-view-activator activate-ext-view">[{oxmultilang ident="ETTM_SETTINGS_FIELDS_VIEW_EXT"}]</a></div>
                        <div class="ettm-view ettm-view--simple">
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" class="double-binding" data-target="cb_oxarticles_oxtitle" value="OXTITLE"> [{oxmultilang ident="ETTM_SETTINGS_OXARTICLES_OXTITLE"}]
                                </label>
                            </div>
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" class="double-binding" data-target="cb_oxarticles_oxshortdesc" value="OXTITLE"> [{oxmultilang ident="ETTM_SETTINGS_OXARTICLES_OXSHORTDESC"}]
                                </label>
                            </div>
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" class="double-binding" data-target="cb_oxartextends_oxlongdesc" value="OXTITLE"> [{oxmultilang ident="ETTM_SETTINGS_OXARTEXTENDS_OXLONGDESC"}]
                                </label>
                            </div>

                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" class="double-binding" data-target="cb_oxarticles_oxvarname" value="OXVARNAME"> [{oxmultilang ident="ETTM_SETTINGS_OXARTICLES_OXVARNAME"}]
                                </label>
                            </div>
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" class="double-binding" data-target="cb_oxarticles_oxvarselect" value="OXVARSELECT"> [{oxmultilang ident="ETTM_SETTINGS_OXARTICLES_OXVARSELECT"}]
                                </label>
                            </div>
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" class="double-binding" data-target="cb_oxobject2attribute_oxvalue" value="OXTITLE"> [{oxmultilang ident="ETTM_SETTINGS_OXOBJECT2ATTRIBUTE_OXVALUE"}]
                                </label>
                            </div>
                        </div>

                        <div class="ettm-view ettm-view--ext" style="display: none">
                            <div class="uk-width-1-1 uk-margin-small">
                                <h4>oxarticles</h4>
                            </div>
                            [{ foreach from=$articles_fields item=field }]
                                <div class="uk-width-1-1 uk-margin-small">
                                    <label>
                                        <input type="checkbox" id="cb_oxarticles_[{$field.name|lower}]" name="articlesfields[]" value="[{$field.name}]" [{if $field.selected}]checked="checked"[{/if}]> [{$field.name}]
                                    </label>
                                </div>
                            [{ /foreach }]
                            <div class="uk-width-1-1 uk-margin-small">
                                <h4>oxartextends</h4>
                            </div>
                            [{ foreach from=$artextends_fields item=field }]
                                <div class="uk-width-1-1 uk-margin-small">
                                    <label>
                                        <input type="checkbox" id="cb_oxartextends_[{$field.name|lower}]" name="artextendsfields[]" value="[{$field.name}]" [{if $field.selected}]checked="checked"[{/if}]> [{$field.name}]
                                    </label>
                                </div>
                            [{ /foreach }]
                            <div class="uk-width-1-1 uk-margin-small">
                                <h4>oxobject2seodata</h4>
                            </div>
                            [{ foreach from=$articleseo_fields item=field }]
                                <div class="uk-width-1-1 uk-margin-small">
                                    <label>
                                        <input type="checkbox" id="cb_article_oxobject2seodata_[{$field.name|lower}]" name="articleseofields[]" value="[{$field.name}]" [{if $field.selected}]checked="checked"[{/if}]> [{$field.name}]
                                    </label>
                                </div>
                            [{ /foreach }]
                            <div class="uk-width-1-1 uk-margin-small">
                                <h4>oxobject2attribute</h4>
                            </div>
                            [{ foreach from=$o2attributes_fields item=field }]
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" id="cb_oxobject2attribute_[{$field.name|lower}]" name="o2attributesfields[]" value="[{$field.name}]" [{if $field.selected}]checked="checked"[{/if}]> [{$field.name}]
                                </label>
                            </div>
                            [{ /foreach }]
                        </div>
                    </div>

                    <div class="uk-width-1-1 uk-width-1-4@s">
                        <h3>[{oxmultilang ident="ETTM_SETTINGS_FIELDS_ATTRIBUTES"}]</h3>
                        <div class="uk-margin-small">[{oxmultilang ident="ETTM_SETTINGS_FIELDS_VIEW"}]<a href="#" class="ettm-view-activator active activate-simple-view">[{oxmultilang ident="ETTM_SETTINGS_FIELDS_VIEW_SIMPLE"}]</a> <a href="#" class="ettm-view-activator activate-ext-view">[{oxmultilang ident="ETTM_SETTINGS_FIELDS_VIEW_EXT"}]</a></div>
                        <div class="ettm-view ettm-view--simple">
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" class="double-binding" data-target="cb_oxattribute_oxtitle" value="OXTITLE"> [{oxmultilang ident="ETTM_SETTINGS_OXATTRIBUTE_OXTITLE"}]
                                </label>
                            </div>
                        </div>

                        <div class="ettm-view ettm-view--ext" style="display: none">
                            <div class="uk-width-1-1 uk-margin-small">
                                <h4>oxattribute</h4>
                            </div>
                            [{ foreach from=$attributes_fields item=field }]
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" id="cb_oxattribute_[{$field.name|lower}]" name="attributesfields[]" value="[{$field.name}]" [{if $field.selected}]checked="checked"[{/if}]> [{$field.name}]
                                </label>
                            </div>
                            [{ /foreach }]
                        </div>
                    </div>

                    <div class="uk-width-1-1 uk-width-1-4@s">
                        <h3>[{oxmultilang ident="ETTM_SETTINGS_FIELDS_CAT"}]</h3>
                        <div class="uk-margin-small">[{oxmultilang ident="ETTM_SETTINGS_FIELDS_VIEW"}]<a href="#" class="ettm-view-activator active activate-simple-view">[{oxmultilang ident="ETTM_SETTINGS_FIELDS_VIEW_SIMPLE"}]</a> <a href="#" class="ettm-view-activator activate-ext-view">[{oxmultilang ident="ETTM_SETTINGS_FIELDS_VIEW_EXT"}]</a></div>
                        <div class="ettm-view ettm-view--simple">
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" class="double-binding" data-target="cb_oxcategories_oxtitle"> [{oxmultilang ident="ETTM_SETTINGS_OXCATEGORIES_OXTITLE"}]
                                </label>
                            </div>
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" class="double-binding" data-target="cb_oxcategories_oxdesc"> [{oxmultilang ident="ETTM_SETTINGS_OXCATEGORIES_OXDESC"}]
                                </label>
                            </div>
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" class="double-binding" data-target="cb_oxcategories_oxlongdesc"> [{oxmultilang ident="ETTM_SETTINGS_OXCATEGORIES_OXOXLONGDESC"}]
                                </label>
                            </div>
                        </div>

                        <div class="ettm-view ettm-view--ext" style="display: none">
                            <div class="uk-width-1-1 uk-margin-small">
                                <h4>oxcategories</h4>
                            </div>
                            [{ foreach from=$category_fields item=field }]
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" id="cb_oxcategories_[{$field.name|lower}]" name="categoryfields[]" value="[{$field.name}]" [{if $field.selected}]checked="checked"[{/if}]> [{$field.name}]
                                </label>
                            </div>
                            [{ /foreach }]
                            <div class="uk-width-1-1 uk-margin-small">
                                <h4>oxobject2seodata</h4>
                            </div>
                            [{ foreach from=$categoryseo_fields item=field }]
                            <div class="uk-width-1-1 uk-margin-small">
                                <label>
                                    <input type="checkbox" id="cb_category_oxobject2seodata_[{$field.name|lower}]" name="categoryseofields[]" value="[{$field.name}]" [{if $field.selected}]checked="checked"[{/if}]> [{$field.name}]
                                </label>
                            </div>
                            [{ /foreach }]
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="uk-width-1-1">
            <input type="submit" class="edittext" name="save" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[{oxmultilang ident="GENERAL_SAVE"}]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" onClick="Javascript:document.myedit.fnc.value='save'">
        </div>
    </div>
</form>

</div>

[{include file="bottomitem.tpl"}]
