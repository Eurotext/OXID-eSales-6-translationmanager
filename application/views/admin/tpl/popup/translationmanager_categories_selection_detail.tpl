[{include file="popups/headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<table width="100%">
    <colgroup>
        <col span="2" width="50%" />
    </colgroup>
    <tr class="edittext">
        <td colspan="2">[{ oxmultilang ident="AJAX_DESCRIPTION" }]<br>[{ oxmultilang ident="GENERAL_FILTERING" }]<br /><br /></td>
    </tr>
    <tr class="edittext">
        <td align="center"><b>[{ oxmultilang ident="EXPORT_MAIN_ALLCAT" }]</b></td>
        <td align="center"><b>[{ oxmultilang ident="EXPORT_MAIN_CATINPROJECT" }]</b></td>
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
        YAHOO.oxid.container1 = new YAHOO.oxid.aoc(
            'container1',
            [{$traslHeaders.container1|@json_encode}],
            '[{ $oViewConf->getAjaxLink() }]cmpid=container1&container=translationmanager_categories_selection&projectid=[{ $oxid }]'
        );

        YAHOO.oxid.container2 = new YAHOO.oxid.aoc(
            'container2',
            [{$traslHeaders.container2|@json_encode}],
            '[{ $oViewConf->getAjaxLink() }]cmpid=container2&container=translationmanager_categories_selection&projectid=[{ $oxid }]'
        );

        YAHOO.oxid.container1.getDropAction = function() {
            return 'fnc=addItem';
        }

        YAHOO.oxid.container2.getDropAction = function() {
            return 'fnc=removeItem';
        }
    } );
</script>

</body>
</html>
