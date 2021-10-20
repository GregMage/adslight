<{if $adslight_active_menu == 1 }>
    <{include file='db:adslight_menu.tpl'}>
<{/if}>
<br>
<table cellspacing="0" class="outer" style="width:100%;">
    <tr>
        <td class="head"><{$nav_main}></td>
    </tr>
    <tr>
        <td class="head"><{$all_user_listings}><{$submitter|default:''}>
        </td>
    </tr>
</table>
<br>
<{if $istheirs|default:'' == "1"}><{if $rate !='0'}>
    <strong><{$lang_user_rating}></strong>
    <{$rating}> (<{$user_votes}>)<{/if}><{else}>
    <{if $rate !='0'}><strong><{$lang_user_rating}></strong> <{$rating|default:''}> (<{$user_votes|default:''}>)
        <br>
        <a href="rate-user.php?usid=<{$usid|default:''}>" rel="nofollow"><{$lang_ratethisuser}></a><{/if}>
<{/if}>
<br><br><{$nav_page|default:false}><br><br>
<table cellspacing="0" class="outer" style="width:100%;">
    <tr>
        <td style="padding:0;">
            <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                <tr>
                    <{if $isadmin|default:''}>
                        <td class="head"></td>
                    <{/if}>
                    <td class="head"></td>
                    <{if $istheirs|default:''}><{if $permit}>
                        <td class="head" align="center"><{$status_head}></td><{/if}><{/if}>
                    <td class="head"><{$title_head}></td>
                    <{if $istheirs|default:''}>
                        <td class="head" align="center"><{$edit_ad}></td><{/if}>

                    <td class="head" align="left"><{$expires_head}></td>
                    <td class="head" align="left"><{$local_head|default:''}></td>
                    <{if $istheirs|default:''}><{if $permit}>
                        <td class="head"><{$replies_head}></td><{/if}><{/if}>
                    <td class="head" align="left"><{$views_head}></td>
                </tr>
                <{foreach item=item from=$items|default:null}>
                    <tr class=<{cycle values="odd,even"}>>
                        <{if $isadmin|default:''}>
                            <td><{$item.adminlink}></td><{/if}>
                        <td width="105"
                            align="left"><{if $item.photo}><{$item.photo}><{else}><{$item.no_photo}><{/if}></td>
                        <{if $istheirs}><{if $permit}>
                            <td align="left"><{$item.status}></td><{/if}><{/if}>
                        <td align="left"><a href="viewads.php?lid=<{$item.id}>"><strong><{$item.title}></strong></a>&nbsp;<{$item.new}>
                            <br>
                            <{*                            <{$item.type}><br>*}>
                            <{*                            <strong><{$item.price}></strong>&nbsp;<{$money_sign}>&nbsp;<{$item.typeprice}><br><br>*}>

                            <{*                        <td align="left"><{$type}></td>*}>
                            <{$type}> <br><{$item.price|default:''}>

                            <{if $item.sold}><{$item.sold}><{/if}><br>
                        </td>
                        <{if $istheirs}>
                            <td align="center"><{$item.modify_link}></td><{/if}>

                        <td align="left"><{$item.expires}></td>
                        <td align="left"><{$item.town}></td>
                        <{if $istheirs}><{if $permit}>
                            <td align="center"><{$item.rrows}>&nbsp;<{$item.view_now}></td><{/if}><{/if}>
                        <td align="center"><{$item.hits}></td>
                    </tr>
                <{/foreach}>
            </table>
        </td>
    </tr>
    <tr>

    </tr>
</table>
<br><br><{$nav_page|default:false}><br><br>
<div style="text-align: center; padding: 3px; margin:3px;">
    <{$comments_head}>
</div>
<div style="text-align: center; padding: 3px; margin:3px;">
    <{$commentsnav}>
    <{$lang_notice}>
</div>
<div style="margin:3px; padding: 3px;">
    <{if $comment_mode == "flat"}>
        <{include file="db:system_comments_flat.tpl"}>
    <{elseif $comment_mode == "thread"}>
        <{include file="db:system_comments_thread.tpl"}>
    <{elseif $comment_mode == "nest"}>
        <{include file="db:system_comments_nest.tpl"}>
    <{/if}>
</div>
