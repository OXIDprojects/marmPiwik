[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly }]
[{assign var="readonly" value="readonly disabled"}]
[{else}]
[{assign var="readonly" value=""}]
[{/if}]
<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="marm_piwik_setup">
    <input type="hidden" name="language" value="[{ $actlang }]">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="marm_piwik_setup">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="language" value="[{ $actlang }]">


    <table cellspacing="0" cellpadding="0" border="0" width="98%">
        <tr>
            <td valign="top" class="edittext">

                <table cellspacing="0" cellpadding="0" border="0">

                    [{foreach from=$oView->getConfigValues() item='aConfigValueOptions' key='sConfigKey'}]
                    <tr>
                        <td class="edittext">
                            [{ oxmultilang ident="MARM_PIWIK_CONFIG_"|cat:$sConfigKey }]
                        </td>
                        <td class="edittext">
                            [{if $aConfigValueOptions.input_type == 'text'}]
                            <input type="text" class="editinput" size="40" maxlength="255"
                                   name="editval[[{$sConfigKey}]]" value="[{$aConfigValueOptions.value}]" [{ $readonly }]>
                            [{elseif $aConfigValueOptions.input_type == 'select'}]
                            <select name="editval[[{$sConfigKey}]]">
                                [{foreach from=$aConfigValueOptions.options item='sConfigOption'}]
                                <option value="[{$sConfigOption}]"[{if $aConfigValueOptions.value ==$sConfigOption}] selected="selected"[{/if}]>[{$sConfigOption}]</option>
                                [{/foreach}]
                            </select>
                            [{/if}]
                        </td>
                    </tr>
                    [{/foreach}]
                    <tr>
                        <td class="edittext"><br><br>
                        </td>
                        <td class="edittext"><br><br>
                            <input type="submit" class="edittext" id="oLockButton"
                                   value="[{ oxmultilang ident="GENERAL_SAVE" }]"
                                   onclick="Javascript:document.myedit.fnc.value='save'"" [{ $readonly }]><br>
                        </td>
                    </tr>
                </table>
            </td>

        </tr>
    </table>

</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
