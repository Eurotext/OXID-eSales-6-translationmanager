[{include file="popups/headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<script type="text/javascript" src="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/js/datepicker-full.min.js")}]"></script>
<link rel="stylesheet" href="[{$oViewConf->getModuleUrl("translationmanager6", "out/admin/css/datepicker.css")}]">
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
    <tr>
        <td colspan="2"><br><label><input type="checkbox" name="filterbytime" class="editinput" value="1"> [{ oxmultilang ident="EXPORT_MAIN_DATERANGE" }]</label></td>
    </tr>
    <tr id="rangecontainer" style="display: none;">
        <td colspan="2">
            <br>
            <div id="innercontainerrange">
                <select name="filtermode" class="editinput">
                    <option value="created" selected>[{ oxmultilang ident="EXPORT_MAIN_DATERANGE_CRTD" }]</option>
                    <option value="updated">[{ oxmultilang ident="EXPORT_MAIN_DATERANGE_UPDTD" }]</option>
                </select>
                <span>[{ oxmultilang ident="EXPORT_MAIN_FROM" }]</span>
                <input type="text" name="start">
                <span>[{ oxmultilang ident="EXPORT_MAIN_TO" }]</span>
                <input type="text" name="end">
                &nbsp;
                <button id="applydatefilter">[{ oxmultilang ident="EXPORT_MAIN_FILTER" }]</button>
            </div>
        </td>
    </tr>
    <tr class="edittext">
        <td colspan="2"><br><label><input type="checkbox" name="skiptranslated" class="editinput" value="[{$editlangs}]" checked="checked"> [{ oxmultilang ident="EXPORT_ART_SKIPTRANSLATES" }]</label></td>
    </tr>
    <tr class="edittext">
        <td colspan="2"><br>[{ oxmultilang ident="AJAX_DESCRIPTION" }]<br>[{ oxmultilang ident="GENERAL_FILTERING" }]<br /><br /></td>
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

            if (document.querySelector('input[name="filterbytime"]').checked) {
                sRequest += '&atrmode=' + document.querySelector('select[name="filtermode"]').value;
                sRequest += '&start=' + document.querySelector('input[name="start"]').value;
                sRequest += '&end='+ document.querySelector('input[name="end"]').value;
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

        document.querySelector('#applydatefilter').addEventListener('click', function(e) {
            YAHOO.oxid.container1.getPage( 0 );
        });
    } );
</script>

<script>
    $E.onDOMReady( function() {
        /**
         * German translation for bootstrap-datepicker
         * Sam Zurcher <sam@orelias.ch>
         */
        (function () {
            Datepicker.locales.de = {
                days: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"],
                daysShort: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
                daysMin: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
                months: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
                monthsShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
                today: "Heute",
                monthsTitle: "Monate",
                clear: "Löschen",
                weekStart: 1,
                format: "dd.mm.yyyy"
            };
        }());

        document.querySelector('input[name="filterbytime"]').addEventListener('change', function(e) {
            if (this.checked) {
                document.querySelector('#rangecontainer').style.display = 'table-row';
            } else {
                document.querySelector('#rangecontainer').style.display = 'none';
            }
        });

        const elem = document.getElementById('innercontainerrange');
        const rangepicker = new DateRangePicker(elem, {
            "format": "dd.mm.yyyy",
            "autohide": true,
            "language": "de"
        });
    });

</script>

</body>
</html>
