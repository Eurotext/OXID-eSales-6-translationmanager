[{include file="headitem.tpl" title="Eurotext - Import"}]
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

</script>
<script type="text/javascript" src="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/js/ettm_import.js")}]"></script>
<script type="text/javascript" src="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/js/uikit-core.min.js")}]"></script>
<link rel="stylesheet" href="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/css/ettm_styles.css")}]">
<div>
    <form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink }]" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="cl" value="translationmanager_import_detail">
        <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
    </form>

    <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" enctype="multipart/form-data" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="translationmanager_import_detail">
        <input type="hidden" name="fnc" value="">
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="voxid" value="[{ $oxid }]">
        <input type="hidden" name="editval[ettm_project__oxid]" value="[{ $oxid }]">
        <input type="hidden" class="edittext" name="editval[ettm_project__status]" value="[{ $edit->ettm_project__status->value}]">
        <input type="hidden" class="edittext" name="editval[ettm_project__external_id]" value="[{$edit->ettm_project__external_id->value}]">

        <div class="uk-grid uk-grid-small" uk-grid>

            [{if 30 <= $edit->ettm_project__status->value}]
            <div class="uk-width-1-1">
                <fieldset>
                    <legend><strong>[{oxmultilang ident="ETTM_IMPORT_GENERAL"}]</strong></legend>
                    <table class="uk-margin">
                        <tr>
                            <td class="edittext">[{oxmultilang ident="ETTM_IMPORT_GENERAL_PROJECT_NAME"}]</td>
                            <td>
                                [{$edit->ettm_project__name->value}]
                            </td>
                        </tr>
                        <tr>
                            <td class="edittext">[{oxmultilang ident="ETTM_IMPORT_GENERAL_STARTLANG"}]</td>

                            <td>
                                [{assign var="editcurlang" value=$edit->ettm_project__lang_origin->rawValue }]
                                [{$ettmoriglaguages.$editcurlang}]
                            </td>
                        </tr>
                        <tr>
                            <td class="edittext">[{oxmultilang ident="ETTM_IMPORT_GENERAL_TARGETLANG"}]</td>
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

            [{if 40 == $edit->ettm_project__status->value}]
            <div class="uk-width-1-1">
                <fieldset>
                    <legend><strong>[{oxmultilang ident="ETTM_IMPORT_STATUS"}]</strong></legend>
                    [{oxmultilang ident="ETTM_EXPORT_STATUS_TEXTBLOCK2"}]
                    <input type="submit" class="edittext ettm-button-start" name="startTranslation" value="[{oxmultilang ident="ETTM_EXPORT_TRANSLATION_START"}]" onclick="javascript:document.myedit.fnc.value='startTranslation';">
                </fieldset>
            </div>
            [{/if}]

            [{if 50 == $edit->ettm_project__status->value}]
            <div class="uk-width-1-1">
                <fieldset>
                    <legend><strong>[{oxmultilang ident="ETTM_IMPORT_STATUS"}]</strong></legend>
                    [{oxmultilang ident="ETTM_IMPORT_STATUS_TRANSLATION"}]
                </fieldset>
            </div>
            [{/if}]

            [{if 60 == $edit->ettm_project__status->value}]
            <div class="uk-width-1-1">
                <fieldset>
                    <legend><strong>[{oxmultilang ident="ETTM_IMPORT_IMPORT"}]</strong></legend>
                    [{oxmultilang ident="ETTM_IMPORT_IMPORT_TEXTBLOCK"}]
                    <input type="submit" class="edittext ettm-button-start" name="startImport" value="[{oxmultilang ident="ETTM_IMPORT_START"}]" onClick="Javascript:document.myedit.fnc.value='startImport'">
                </fieldset>
            </div>
            [{/if}]

            [{if 70 == $edit->ettm_project__status->value}]
            <div class="uk-width-1-1">
                <fieldset>
                    <legend><strong>[{oxmultilang ident="ETTM_IMPORT_STATUS"}]</strong></legend>
                    [{oxmultilang ident="ETTM_IMPORT_RUNNING"}]
                </fieldset>
            </div>
            [{/if}]

            [{if 80 == $edit->ettm_project__status->value}]
            <div class="uk-width-1-1">
                <fieldset>
                    <legend><strong>[{oxmultilang ident="ETTM_IMPORT_STATUS"}]</strong></legend>
                    [{oxmultilang ident="ETTM_IMPORT_FINISHED"}]
                </fieldset>
            </div>
            [{/if}]

        </div>
    </form>
</div>

[{include file="bottomitem.tpl"}]
