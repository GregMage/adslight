<table cellspacing="1" class="outer width100">
    <tr>
        <th><{$add_from_title|default:''}> <{$add_from_sitename|default:''}></th>
    </tr>
    <{if $moderated|default:false}>
        <{if $xoops_isadmin}>
            <tr>
                <td class="center even">
                    <table class="outer" cellspacing="0">
                        <tr>
                            <td class="head center"><{$admin_block}></td>
                        </tr>
                        <tr>
                            <td class="odd center"><{$confirm_ads}></td>
                        </tr>
                    </table>
                </td>
            </tr>
        <{/if}>
    <{/if}>
    <tr>
        <td class="even center">
            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table class="bullinfo">
                            <tr>
                                <td class="bullinfotext">
                                    <{$bullinfotext|default:''}>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table width="122" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <form name='search' id='search' action='search.php' method='post'
                                      onsubmit='return xoopsFormValidate_search();'>
                                    <input type='hidden' name='mids[]' value='<{$xmid|default:''}>'>
                                    <td colspan="2"><strong><{$smarty.const._ADSLIGHT_SEARCH_LISTINGS}></strong><br><br>
                                    </td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type='text' name='query' id='query' size='30' maxlength='255'
                                                       value=''><br><br></td>
                            </tr>
                            <tr>
                                <td><select size='1' name='andor' id='andor'>
                                        <option value='AND'
                                                selected='selected'><{$smarty.const._ADSLIGHT_ALL_WORDS}></option>
                                        <option value='OR'><{$smarty.const._ADSLIGHT_ANY_WORDS}></option>
                                        <option value='exact'><{$smarty.const._ADSLIGHT_EXACT_MATCH}></option>
                                    </select></td>
                                <td><input type='submit' class='formButton' name='submit' id='submit'
                                           value='<{$smarty.const._ADSLIGHT_SEARCH}>'></td>
                            </tr>
                            <input type='hidden' name='action' id='action' value='results'>
                            </form>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<script type='text/javascript'>

    <{*    function xoopsFormValidate_search() {}*}>

</script>
