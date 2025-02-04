<{if $adslight_active_menu|default:0 == 1 }>
    <{include file='db:adslight_menu.tpl'}>
<{/if}>
<{include file='db:adslight_search.tpl'}>
<br>
<table border="0" cellspacing="0" cellpadding="0" align="center">
    <td colspan="2" valign="top"></td>
    <tr>
        <{foreach item=category from=$categories|default:null}>
        <td valign="top">
            <{if $category.image != ""}>
                <{$category.image}>
            <{/if}>
            <br><br>
        </td>
        <td valign="top">
            <div align="left">
                <a class="catlist"
                   href="<{$xoops_url}>/modules/adslight/viewcats.php?cid=<{$category.id}>"><strong><{$category.title}></strong></a>&nbsp;-&nbsp;[<{$category.totallisting}>
                ]<br>
                <{$category.subcategories}>
            </div>
        </td>
        <{if $category.count % 2 == 0}>
    </tr>
    <tr>
        <{/if}>
        <{/foreach}>
    </tr>
</table><br>
<table class="even" cellspacing='5' cellpadding='0' align="center">
    </td></tr>
</table>
<table border="0" cellspacing="1" class="outer" style="width:100%;">
    <tr>
        <th>
            <{$last_head|default:''}>
        </th>
    </tr>
    <tr>
        <td style="padding:0;">
            <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                <tr>
                    <td class="head"></td>
                    <td class="head">
                        <{$last_head_title|default:''}>
                    </td>
                    <td width="80" align="left" class="head">
                        <{$last_head_local|default:''}>
                    </td>
                </tr>
            </table>
            <{if $use_extra_code|default:0 == 1}>
                <{foreach from=$items item=item name=items}>
                    <{if $smarty.foreach.items.iteration eq $index_code_place}>
                        <{if $adslight_use_banner == 1}>
                            <table>
                                <tr>
                                    <td align="center"><{$banner}></td>
                                </tr>
                            </table>
                        <{else}>
                            <table>
                                <tr>
                                    <td align="center"><{$index_extra_code}></td>
                                </tr>
                            </table>
                        <{/if}>
                    <{/if}>
                    <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                        <tr class='<{cycle values="odd,even"}>'>
                            <{if $xoops_isadmin}>
                                <td width="20">
                                    <{$item.admin}>
                                </td>
                            <{/if}>
                            <td width="24" align="right">
                                <{if $item.photo|default:''}>
                                    <{$item.photo}>
                                <{else}>
                                    <{$item.no_photo}>
                                <{/if}>
                            </td>
                            <td><strong>
                                    <{$item.title}>&nbsp;
                                </strong><br>
                                <{$item.type}><br>
                                <{if $item.price|default:'' !=""}>
                                <strong><{$item.price}></strong>&nbsp;
                                <{else}>&nbsp;
                                <{/if}><br>
                                <{if $item.sold|default:''}><{$item.sold}><{/if}>
                            </td>
                            <td width="80" align="left">
                                <{$item.local}>
                            </td>
                        </tr>
                    </table>
                <{/foreach}><{else}>
                <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                    <{foreach item=item from=$items|default:null}>
                        <tr class='<{cycle values="odd,even"}>'>
                            <{if $xoops_isadmin}>
                                <td width="20">
                                    <{$item.admin}>
                                </td>
                            <{/if}>
                            <td width="24" align="right">
                                <{if $item.photo|default:''}>
                                    <{$item.photo}>
                                <{else}>
                                    <{$item.no_photo}>
                                <{/if}>
                            </td>
                            <td><strong>
                                    <{$item.title}>&nbsp;
                                </strong><br>
                                <{$item.type}><br>
                                <{if $item.price|default:'' !=""}>
                                <strong><{$item.price}></strong>&nbsp;-&nbsp;<{$item.price_typeprice}>
                                <{else}>&nbsp;
                                <{/if}><br>
                                <{if $item.sold|default:false}><{$item.sold}><{/if}>
                            </td>
                            <td align="center">
                                <{$item.local}>
                            </td>
                        </tr>
                    <{/foreach}>
                </table>
            <{/if}>
        </td>
    </tr>
</table>
<br><br>
<{include file='db:system_notification_select.tpl'}>
<br>
<br>
