[{include file="popups/headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<table width="100%">
    <colgroup>
        <col span="2" width="50%" />
    </colgroup>
    <tr>
        <td>
            <label>[{ oxmultilang ident="EXPORT_AJAX_CAT" }]
                <select name="artcat" id="artcat" class="editinput">
                    [{foreach from=$artcattree->aList item=pcat}]
                    <option value="[{ $pcat->oxcategories__oxid->value }]">[{ $pcat->oxcategories__oxtitle->value }]</option>
                    [{/foreach}]
                </select>
            </label>
        </td>
        <td></td>
    </tr>
    <tr class="edittext">
        <td colspan="2">[{ oxmultilang ident="AJAX_DESCRIPTION" }]<br>[{ oxmultilang ident="GENERAL_FILTERING" }]<br /><br /></td>
    </tr>
    <tr class="edittext">
        <td colspan="2"><input type="checkbox" name="skiptranslated" class="editinput" value="[{$editlangs}]" checked="checked"> [{ oxmultilang ident="EXPORT_ART_SKIPTRANSLATES" }]</td>
    </tr>
    <tr class="edittext">
        <td align="center"><b>[{ oxmultilang ident="EXPORT_MAIN_ALLARTICLES" }]</b></td>
        <td align="center"><b>[{ oxmultilang ident="EXPORT_MAIN_ARTICLESINPROJECT" }]</b></td>
    </tr>
    <tr>
        <td valign="top" id="container1"></td>
        <td valign="top" id="container2"></td>
    </tr>
    <tr>
        <td class="oxid-aoc-actions">
            <input type="button" value="[{ oxmultilang ident="GENERAL_AJAX_ASSIGNALL" }]" id="container1_btn">
        </td>
        <td class="oxid-aoc-actions">
            <input type="button" value="[{ oxmultilang ident="GENERAL_AJAX_UNASSIGNALL" }]" id="container2_btn">
        </td>
    </tr>
</table>

<script type="text/javascript">
    $E.onDOMReady( function() {
        var addFilter = '';
        if (document.querySelector('input[name="skiptranslated"]').checked) {
            addFilter += '&targetlangs='+document.querySelector('input[name="skiptranslated"]').value;
        }

        YAHOO.oxid.container1 = new YAHOO.oxid.aoc(
            'container1',
            [{$traslHeaders.container1|@json_encode}],
            '[{ $oViewConf->getAjaxLink() }]cmpid=container1&container=translationmanager_articles_selection&projectid=[{ $oxid }]' + addFilter
        );

        YAHOO.oxid.container2 = new YAHOO.oxid.aoc(
            'container2',
            [{$traslHeaders.container2|@json_encode}],
            '[{ $oViewConf->getAjaxLink() }]cmpid=container2&container=translationmanager_articles_selection&projectid=[{ $oxid }]'
        );

        YAHOO.oxid.container1.getDropAction = function() {
            return 'fnc=addItem';
        }

        YAHOO.oxid.container2.getDropAction = function() {
            return 'fnc=removeItem';
        }

        YAHOO.oxid.container1.modRequest = function( sRequest ) {
            var oSelect = document.getElementById('artcat');
            if ( 0 < oSelect.selectedIndex ) {
                sRequest += '&catid='+oSelect.options[oSelect.selectedIndex].value;
            }

            if (!document.querySelector('input[name="skiptranslated"]').checked) {
                sRequest += '&nofilter=1';
            }

            return sRequest;
        }

        oSelect = document.getElementById('artcat');
        oSelect.addEventListener('change', function(e) {
            YAHOO.oxid.container1.getPage( 0 );
        });

        document.querySelector('input[name="skiptranslated"]').addEventListener('change', function(e) {
            YAHOO.oxid.container1.getPage( 0 );
        });
    } );
</script>

</body>
</html>
