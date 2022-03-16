[{include file="headitem.tpl" title="[ Eurotext ]"}]
[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{ else }]
    [{assign var="readonly" value=""}]
[{ /if }]

<link rel="stylesheet" href="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/css/ettm_styles.css")}]">
<script type="text/javascript" src="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/js/uikit-core.min.js")}]"></script>
<div class="ettm">

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="translationmanager_settings" />
    <input type="hidden" name="fnc" value="" />
    <input type="hidden" name="oxid" value="[{$oxid}]" />

    <h1>[{oxmultilang ident="ETTM_REGISTRATION"}]</h1>
    <div class="uk-grid uk-grid-small" uk-grid>
        <div class="uk-width-1-1">
            <fieldset>
                [{oxmultilang ident="ETTM_REGISTRATION_HTMLBLOCK1"}]
                [{oxmultilang ident="ETTM_REGISTRATION_HTMLBLOCK2"}]
            </fieldset>
        </div>
    </div>
</form>

</div>

[{include file="bottomitem.tpl"}]
