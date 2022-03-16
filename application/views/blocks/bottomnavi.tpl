[{$smarty.block.parent}]

[{if $bottom_buttons->ETTM_PROJECT_NEW}]
    [{* NEW PROJEKT *}]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" style="text-decoration: underline;" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{ oxmultilang ident="TOOLTIPS_NEW_EUROTEXT_EXPORT_PROJECT" }]</a> |</li>
[{/if}]

[{if $bottom_buttons->ETTM_HELP}]
    <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.help" style="text-decoration: underline;" href="[{$oViewConf->getSelfLink()}]&cl=translationmanager_help" target="_parent">[{oxmultilang ident="TOOLTIPS_SUPPORT_EUROTEXT_EXPORT_BOTTOM"}]</a> |</li>
    [{assign var="sHelpURL" value=false}]
[{/if}]

[{if $bottom_buttons->ETTM_VERSION}]
    [{* MODULE VERSION *}]
    <li style="padding-left: 0.5em; padding-right: 0.2em;">[{oxmultilang ident="BOTTOM_VERSION_INFO"}]</li>
[{/if}]
