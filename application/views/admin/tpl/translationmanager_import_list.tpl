[{include file="headitem.tpl" title=""|oxmultilangassign box="list"}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<script type="text/javascript">
<!--

window.onload = function ()
{
    top.reloadEditFrame();
    [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
    [{ /if}]
}
//-->
</script>

<div id="liste">


    <script type="text/javascript" src="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/js/uikit-core.min.js")}]"></script>
    <link rel="stylesheet" href="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/css/ettm_styles.css")}]">
<form name="search" id="search" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{include file="_formparams.tpl" cl="translationmanager_import_list" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]
<table style="width: 100%">
    <colgroup>
        <col width="15%">
        <col width="20%">
        <col width="10%">
        <col width="10%">
        <col width="20%">
        <col width="13%">
        <col width="20%">
        <col width="2%">
    </colgroup>
    <!-- filter group -->
    <tr class="listitem">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <!-- /filter group -->

    <!-- table headers group -->
    <tr>
        <td class="listheader first" height="15">[{oxmultilang ident="ETTM_LIST_EXTPROJECTID"}]</td>
        <td class="listheader" height="15">[{oxmultilang ident="ETTM_LIST_PROJECTNAME"}]</td>
        <td class="listheader" height="15">[{oxmultilang ident="ETTM_LIST_STARTLANG"}]</td>
        <td class="listheader" height="15">[{oxmultilang ident="ETTM_LIST_TARGETLANG"}]</td>
        <td class="listheader" height="15">[{oxmultilang ident="ETTM_LIST_PROGRESS"}]</td>
        <td class="listheader" height="15">[{oxmultilang ident="ETTM_LIST_STATUS"}]</td>
        <td class="listheader" height="15">[{oxmultilang ident="ETTM_LIST_LANSTCHANGE"}]</td>
        <td class="listheader"></td>
    </tr>
    <!-- /table headers group -->

    <!-- listing -->
    [{assign var="_cnt" value=0}]
    [{foreach from=$mylist item=listitem}]
        [{assign var="_cnt" value=$_cnt+1}]
        <tr id="row.[{$_cnt}]">

            [{ if $listitem->ettm_project__oxid->value == $oxid }]
                [{assign var="listclass" value=listitem4 }]
            [{ else }]
                [{assign var="listclass" value=listitem }]
            [{ /if}]

            <!-- project external id -->
            <td valign="top" class="[{ $listclass}]" height="15">
                <div class="listitemfloating">
                    [{if $listitem->ettm_project__external_id->value > 0}]
                    <a href="Javascript:top.oxid.admin.editThis('[{ $listitem->ettm_project__oxid->value }]');" class="[{ $listclass }]">
                        [{ $listitem->ettm_project__external_id->value }]
                    </a>
                    [{else}]
                        [{oxmultilang ident="ETTM_LIST_NOTREGISTERED"}]
                    [{/if}]

                </div>
            </td>
            <!-- /project external id -->

            <!-- project name -->
            <td valign="top" class="[{ $listclass}]" height="15">
                <div class="listitemfloating">
                    <a href="Javascript:top.oxid.admin.editThis('[{ $listitem->ettm_project__oxid->value }]');" class="[{ $listclass }]">[{ $listitem->ettm_project__name->value }]</a>
                </div>
            </td>
            <!-- /project name -->

            <!-- project from languages -->
            <td valign="top" class="[{ $listclass}]" height="15">
                <div class="listitemfloating">
                    [{assign var="listcurlang" value=$listitem->ettm_project__lang_origin->rawValue }]
                    [{$ettmoriglaguages.$listcurlang}]
                </div>
            </td>
            <!-- /project from languages -->

            <!-- project to language -->
            <td valign="top" class="[{ $listclass}]" height="15">
                <div class="listitemfloating">
                    [{foreach from=$listitem->ettm_project__lang_target->rawValue|unserialize item=translationlang name=foo}]
                        [{$ettmoriglaguages.$translationlang}]&nbsp;
                    [{/foreach}]
                </div>
            </td>
            <!-- /project to language -->

            <!-- project progress -->
            <td valign="top" class="[{ $listclass}]" height="15">
                <div class="listitemfloating">
                    [{if $listitem->ettm_project__total_items->rawValue > 0}]
                        <div class="ettm-progress">
                            <div class="ettm-progress__inner" style="width: [{$listitem->ettm_project__percent_finished->rawValue}]%"></div>
                        </div>
                        [{$listitem->ettm_project__percent_finished->rawValue}]%
                    [{/if}]

                </div>
            </td>
            <!-- /project progress -->

            <!-- project status -->
            <td valign="top" class="[{ $listclass}]" height="15">
                <div class="listitemfloating">
                    <a href="Javascript:top.oxid.admin.editThis('[{ $listitem->ettm_project__oxid->value }]');" class="[{ $listclass }]">
                        [{ oxmultilang ident="ETTM_STATUS_`$listitem->ettm_project__status->value`" }]
                    </a>
                </div>
            </td>
            <!-- /project status -->

            <!-- updated at timestamp -->
            <td valign="top" class="[{ $listclass}]" height="15">
                <div class="listitemfloating">
                    <a href="Javascript:top.oxid.admin.editThis('[{ $listitem->ettm_project__oxid->value }]');" class="[{ $listclass }]">[{ $listitem->ettm_project__updated_at|oxformdate:'datetime':true }]</a>
                </div>
            </td>
            <!-- /updated at timestamp -->

            <!-- actions -->
            <td class="[{ $listclass}]">
                <a href="Javascript:top.oxid.admin.deleteThis('[{ $listitem->ettm_project__oxid->value }]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a>
            </td>
            <!-- /actions -->
        </tr>
    [{/foreach}]
    <!-- /listing -->
</table>
</form>
</div>

[{include file="pagetabsnippet.tpl"}]
</body>
</html>
