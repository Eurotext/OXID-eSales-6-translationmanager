[{include file="headitem.tpl" title="Eurotext - Export"}]
[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{ else }]
    [{assign var="readonly" value=""}]
[{ /if }]

<script type="text/javascript">

window.onload = function ()
{
    [{if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
    [{/if}]
    top.reloadEditFrame();
}
function editThis( sID )
{
    var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
    oTransfer.oxid.value = sID;
    oTransfer.cl.value = top.basefrm.list.sDefClass;

    //forcing edit frame to reload after submit
    top.forceReloadingEditFrame();

}

function customOpenModal(url) {
    var popupWindow = window.open('[{$oViewConf->getSelfLink()|replace:"&amp;":"&"}]'+url, 'ajaxpopup', 'width=800,height=680,scrollbars=yes,resizable=yes');
    popupWindow.onbeforeunload = function(){
        document.myedit.fnc.value='checkIfReady';
        document.myedit.submit();
    };
}

</script>
<script type="text/javascript" src="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/js/ettm_js.js")}]"></script>
<script type="text/javascript" src="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/js/uikit-core.min.js")}]"></script>
<link rel="stylesheet" href="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/css/ettm_styles.css")}]">
<div>
    <form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink }]" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="cl" value="translationmanager_export_detail">
        <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
    </form>

    <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" enctype="multipart/form-data" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="translationmanager_export_detail">
        <input type="hidden" name="fnc" value="">
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="voxid" value="[{ $oxid }]">
        <input type="hidden" name="editval[ettm_project__oxid]" value="[{ $oxid }]">
        <input type="hidden" name="editval[ettm_project__status]" value="[{ $edit->ettm_project__status->value}]">
        <input type="hidden" name="editval[ettm_project__external_id]" value="[{$edit->ettm_project__external_id->value}]">

        <div class="uk-grid uk-grid-small" uk-grid>

            [{if 0 <= $edit->ettm_project__status->value && 30 > $edit->ettm_project__status->value}]
            <div class="uk-width-1-1">
                <fieldset>
                    <legend><strong>[{oxmultilang ident="ETTM_EXPORT_GENERAL"}]</strong></legend>
                    <table class="uk-margin uk-width-1-1">
                        <tr>
                            <td class="edittext">[{oxmultilang ident="ETTM_EXPORT_GENERAL_PROJECT_NAME"}]</td>
                            <td>
                                <input
                                    type="text"
                                    class="editinput"
                                    size="50"
                                    maxlength="255"
                                    name="editval[ettm_project__name]"
                                    value="[{$edit->ettm_project__name->value}]"
                                >
                                [{ oxinputhelp ident="ETTM_EXPORT_GENERAL_PROJECT_NAMEHELP" }]
                            </td>
                        </tr>
                        <tr>
                            <td class="edittext">[{oxmultilang ident="ETTM_EXPORT_GENERAL_STARTLANG"}]</td>

                            <td>
                                [{if $oxid != '-1' }]
                                    [{assign var="editcurlang" value=$edit->ettm_project__lang_origin->rawValue }]
                                    <input type="hidden" name="editval[ettm_project__lang_origin]" value="[{$editcurlang}]">
                                    [{$ettmoriglaguages.$editcurlang}]
                                [{else}]
                                    <input type="hidden" name="editval[ettm_project__lang_origin]" value="[{$languages[$editlanguage]->abbr}]">
                                    [{$languages[$editlanguage]->name}]
                                [{/if}]
                            </td>
                        </tr>
                        <tr>
                            <td class="edittext">[{oxmultilang ident="ETTM_EXPORT_GENERAL_TARGETLANG"}]</td>
                            <td>
                                [{if $editlangs|@count eq 0}]
                                    <input type="hidden" id="ettm-translate-more-counter" value="1">
                                    <input type="hidden" id="ettm-translate-selection" value="['[{$languages[$editlanguage]->abbr}]']">
                                    <div id="ettm-lang-selector-container" class="ettm-lang-selector-container">
                                        <div class="ettm__target-lang">
                                            <select name="editlangs[0]" class="ettm__target-lang-selector" [{ $readonly }]>
                                                [{if $languages}]
                                                    [{ foreach from=$languages item=lang }]
                                                        [{if 1 != $lang->selected}]
                                                            <option value="[{$lang->abbr}]">[{ $lang->name }]</option>
                                                        [{/if}]
                                                    [{ /foreach }]
                                                [{/if}]
                                            </select>
                                            <a href="#" class="delete" onclick="deleteThis(this);" onmouseout="popDown('item_delete')" onmouseover="popUp(event,'item_delete');return true;"></a>
                                        </div>
                                    </div>
                                [{else}]
                                    <input type="hidden" id="ettm-translate-more-counter" value="[{$editlangs|@count}]">
                                    <input type="hidden" id="ettm-translate-selection" value="['[{$languages[$editlanguage]->abbr}]']">
                                    <div id="ettm-lang-selector-container" class="ettm-lang-selector-container">

                                        [{foreach from=$editlangs key=iKey item=aListItem}]
                                            <div class="ettm__target-lang">
                                                <select name="editlangs[[{$iKey}]]" class="ettm__target-lang-selector" [{ $readonly }]>
                                                    [{if $languages}]
                                                        [{ foreach from=$languages item=lang }]
                                                            [{if 1 != $lang->selected}]
                                                                <option value="[{$lang->abbr}]" [{if $aListItem->scalar == $lang->abbr}] selected="selected"[{/if}]>[{ $lang->name }]</option>
                                                            [{/if}]
                                                        [{ /foreach }]
                                                    [{/if}]
                                                </select>
                                                <a href="#" class="delete" onclick="deleteThis(this);" onmouseout="popDown('item_delete')" onmouseover="popUp(event,'item_delete');return true;"></a>
                                            </div>
                                        [{/foreach}]
                                    </div>
                                [{/if}]

                                <button id="ettm-translate-more" type="button" class="be-cursor">&#43;</button>

                                <div id="ettm-lang-selector-template">
                                    <div class="ettm__target-lang">
                                        <select class="ettm__target-lang-selector" [{ $readonly }]>
                                            [{if $languages}]
                                                [{ foreach from=$languages item=lang }]
                                                    [{if 1 != $lang->selected}]
                                                        <option value="[{$lang->abbr}]">[{ $lang->name }]</option>
                                                    [{/if}]
                                                [{ /foreach }]
                                            [{/if}]
                                        </select>
                                        <a href="#" class="delete" onclick="deleteThis(this);" onmouseout="popDown('item_delete')" onmouseover="popUp(event,'item_delete');return true;"></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>
            [{/if}]

            [{if 30 <= $edit->ettm_project__status->value}]
            <div class="uk-width-1-1">
                <fieldset>
                    <legend><strong>[{oxmultilang ident="ETTM_EXPORT_GENERAL"}]</strong></legend>
                    <table class="uk-margin">
                        <tr>
                            <td class="edittext">[{oxmultilang ident="ETTM_EXPORT_GENERAL_PROJECT_NAME"}]</td>
                            <td>
                                [{$edit->ettm_project__name->value}]
                            </td>
                        </tr>
                        <tr>
                            <td class="edittext">[{oxmultilang ident="ETTM_EXPORT_GENERAL_STARTLANG"}]</td>

                            <td>
                                [{assign var="editcurlang" value=$edit->ettm_project__lang_origin->rawValue }]
                                [{$ettmoriglaguages.$editcurlang}]
                            </td>
                        </tr>
                        <tr>
                            <td class="edittext">[{oxmultilang ident="ETTM_EXPORT_GENERAL_TARGETLANG"}]</td>
                            <td>
                                [{foreach from=$editlangs key=iKey item=aListItem}]
                                    [{assign var="edittarglang" value=$aListItem->scalar }]
                                    [{$ettmoriglaguages.$edittarglang}]&nbsp;
                                [{/foreach}]
                            </td>
                        </tr>
                    </table>
                </fieldset>

            </div>
            [{/if}]


            [{if 0 < $edit->ettm_project__status->value && 30 > $edit->ettm_project__status->value}]
            <div class="uk-width-1-1">
                <fieldset>
                    <legend><strong>[{oxmultilang ident="ETTM_EXPORT_ELEMENTS"}]</strong></legend>
                    [{oxmultilang ident="ETTM_EXPORT_ELEMENTS_TEXTBLOCK"}]

                    <div class="uk-grid uk-grid-small uk-margin" uk-grid>
                        <div class="uk-width-1-1 uk-width-1-4@s">
                            <h3>[{oxmultilang ident="ETTM_SETTINGS_FIELDS_CMS"}]</h3>
                            [{if $cmscount > 0 }]
                            <div class="uk-margin">
                                [{$cmscount}] [{oxmultilang ident="ETTM_EXPORT_CMS_SELECTED"}]
                            </div>
                            [{/if}]

                            [{if '' == $readonly}]
                                <div class="uk-margin">
                                    <a id="ettn-select-cms" class="edittext be-cursor" onclick="javascript:customOpenModal('&cl=translationmanager_cms_selection&oxid=[{ $oxid }]');"> [{oxmultilang ident="ETTM_EXPORT_CMS_SELECT"}]</a>
                                </div>
                            [{/if}]
                        </div>
                        <div class="uk-width-1-1 uk-width-1-4@s">
                            <h3>[{oxmultilang ident="ETTM_SETTINGS_FIELDS_ARTICLES"}]</h3>
                            [{if $articlescount > 0 }]
                            <div class="uk-margin">
                                [{$articlescount}]  [{oxmultilang ident="ETTM_EXPORT_ARTICLE_SELECTED"}]
                            </div>
                            [{/if}]
                            [{if '' == $readonly}]
                                <div class="uk-margin">
                                    <a id="ettm-select-articles" class="edittext be-cursor" onclick="javascript:customOpenModal('&cl=translationmanager_articles_selection&oxid=[{ $oxid }]');">[{oxmultilang ident="ETTM_EXPORT_ARTICLE_SELECT"}]</a>
                                </div>
                            [{/if}]
                        </div>
                        <div class="uk-width-1-1 uk-width-1-4@s">
                            <h3>[{oxmultilang ident="ETTM_SETTINGS_FIELDS_ATTRIBUTES"}]</h3>
                            [{if $attributescount > 0 }]
                            <div class="uk-margin">
                                [{$attributescount}] [{oxmultilang ident="ETTM_EXPORT_ATTRIBUTE_SELECTED"}]
                            </div>
                            [{/if}]
                            [{if '' == $readonly}]
                                <div class="uk-margin">
                                    <a id="ettm-select-attributes" class="edittext be-cursor" onclick="javascript:customOpenModal('&cl=translationmanager_attributes_selection&oxid=[{ $oxid }]');">[{oxmultilang ident="ETTM_EXPORT_ATTRIBUTE_SELECT"}]</a>
                                </div>
                            [{/if}]
                        </div>
                        <div class="uk-width-1-1 uk-width-1-4@s">
                            <h3>[{oxmultilang ident="ETTM_SETTINGS_FIELDS_CAT"}]</h3>
                            [{if $categoriescount > 0 }]
                            <div class="uk-margin">
                                [{$categoriescount}] [{oxmultilang ident="ETTM_EXPORT_CAT_SELECTED"}]
                            </div>
                            [{/if}]
                            [{if '' == $readonly}]
                                <div class="uk-margin">
                                    <a id="ettm-select-categories" class="edittext be-cursor" onclick="javascript:customOpenModal('&cl=translationmanager_categories_selection&oxid=[{ $oxid }]');">[{oxmultilang ident="ETTM_EXPORT_CAT_SELECT"}]</a>
                                </div>
                            [{/if}]
                        </div>
                    </div>
                </fieldset>
            </div>
            [{/if}]

            [{if 20 >= $edit->ettm_project__status->value}]
            <div class="uk-width-1-1">
                <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onclick="Javascript:document.myedit.fnc.value='save'">
            </div>
            [{/if}]

            [{if 20 == $edit->ettm_project__status->value }]
            <div class="uk-width-1-1">
                <fieldset>
                    <legend><strong>[{oxmultilang ident="ETTM_EXPORT_TRANSMIT"}]</strong></legend>
                    [{oxmultilang ident="ETTM_EXPORT_TRANSMIT_TEXTBLOCK"}]

                    <label class="uk-margin">
                        <input type="checkbox" name="editval[ettm_project__only_untranslated]" value="1">
                        <span><strong>[{oxmultilang ident="ETTM_EXPORT_TRANSMIT_SKIP"}]</strong></span>
                    </label>

                </fieldset>
            </div>
            <div class="uk-width-1-1">
                <input type="submit" class="edittext" name="exportToRemote" value="[{oxmultilang ident="ETTM_EXPORT_TRANSMIT_START"}]" onclick="javascript:document.myedit.fnc.value='startExport';">
            </div>
            [{/if}]

            [{if 30 == $edit->ettm_project__status->value}]
            <div class="uk-width-1-1">
                <fieldset>
                    <legend><strong>[{oxmultilang ident="ETTM_EXPORT_STATUS"}]</strong></legend>
                    [{oxmultilang ident="ETTM_EXPORT_STATUS_TEXTBLOCK1"}]
                    <table>
                        <tr>
                            <td><strong>[{oxmultilang ident="ETTM_EXPORT_STATUS_TRANSMITTED"}]</strong></td>
                            <td>[{$edit->ettm_project__transmitted->value}] [{oxmultilang ident="ETTM_EXPORT_STATUS_ITEM"}]</td>
                        </tr>
                        <tr>
                            <td><strong>[{oxmultilang ident="ETTM_EXPORT_STATUS_SKIPPED"}]</strong></td>
                            <td>[{$edit->ettm_project__skipped->value}] [{oxmultilang ident="ETTM_EXPORT_STATUS_ITEM"}]</td>
                        </tr>
                        <tr>
                            <td><strong>[{oxmultilang ident="ETTM_EXPORT_STATUS_ERRORS"}]</strong></td>
                            <td>[{$edit->ettm_project__failed->value}] [{oxmultilang ident="ETTM_EXPORT_STATUS_ITEM"}]</td>
                        </tr>
                    </table>
                </fieldset>
            </div>
            [{/if}]

            [{if 40 == $edit->ettm_project__status->value}]
            <div class="uk-width-1-1">
                <fieldset>
                    <legend><strong>[{oxmultilang ident="ETTM_EXPORT_STATUS"}]</strong></legend>
                    [{oxmultilang ident="ETTM_EXPORT_STATUS_TEXTBLOCK2"}]
                    <table>
                        <tr>
                            <td><strong>[{oxmultilang ident="ETTM_EXPORT_STATUS_TRANSMITTED"}]</strong></td>
                            <td>[{$edit->ettm_project__transmitted->value}] [{oxmultilang ident="ETTM_EXPORT_STATUS_ITEM"}]</td>
                        </tr>
                        <tr>
                            <td><strong>[{oxmultilang ident="ETTM_EXPORT_STATUS_SKIPPED"}]</strong></td>
                            <td>[{$edit->ettm_project__skipped->value}] [{oxmultilang ident="ETTM_EXPORT_STATUS_ITEM"}]</td>
                        </tr>
                        <tr>
                            <td><strong>[{oxmultilang ident="ETTM_EXPORT_STATUS_ERRORS"}]</strong></td>
                            <td>[{$edit->ettm_project__failed->value}] [{oxmultilang ident="ETTM_EXPORT_STATUS_ITEM"}]</td>
                        </tr>
                    </table>
                    <input type="submit" class="edittext" name="startTranslation" value="[{oxmultilang ident="ETTM_EXPORT_TRANSLATION_START"}]" onclick="javascript:document.myedit.fnc.value='startTranslation';">
                </fieldset>
            </div>
            [{/if}]
        </div>
    </form>
</div>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
