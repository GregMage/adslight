<?php

declare(strict_types=1);

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author       XOOPS Development Team
 * @author       Pascal Le Boustouller: original author (pascal.e-xoops@perso-search.com)
 * @author       Luc Bizet (www.frxoops.org)
 * @author       jlm69 (www.jlmzone.com)
 * @author       mamba (www.xoops.org)
 */

use Xmf\Module\Admin;
use Xmf\Request;
use XoopsModules\Adslight\{
    Helper,
    Tree,
    Utility
};

require_once __DIR__ . '/header.php';

global $xoopsModule, $xoopsDB, $xoopsConfig, $xoTheme;

$myts = \MyTextSanitizer::getInstance(); // MyTextSanitizer object
global $xoopsModule;
$pathIcon16 = Admin::iconUrl('', '16');
xoops_load('XoopsLocal');
$moduleDirName = \basename(__DIR__);

$helper                                  = Helper::getInstance();
$mytree                                  = new Tree($xoopsDB->prefix('adslight_categories'), 'cid', 'pid');
$GLOBALS['xoopsOption']['template_main'] = 'adslight_members.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/include/comment_view.php';

$lid       = Request::getInt('lid', 0, 'GET');
$usid      = Request::getInt('usid', 0, 'GET');
$moduleId = $xoopsModule->getVar('mid');
if (is_object($GLOBALS['xoopsUser'])) {
    $groups = $GLOBALS['xoopsUser']->getGroups();
} else {
    $groups = XOOPS_GROUP_ANONYMOUS;
}
/** @var \XoopsGroupPermHandler $grouppermHandler */
$grouppermHandler = xoops_getHandler('groupperm');
$perm_itemid      = Request::getInt('item_id', 0, 'POST');

//If no access
$permit = !$grouppermHandler->checkRight('adslight_premium', $perm_itemid, $groups, $moduleId) ? '0' : '1';

$GLOBALS['xoopsTpl']->assign('permit', $permit);
$isadmin = ($GLOBALS['xoopsUser'] instanceof \XoopsUser)
           && $GLOBALS['xoopsUser']->isAdmin($xoopsModule->mid());

$GLOBALS['xoopsTpl']->assign('add_from', _ADSLIGHT_ADDFROM . ' ' . $xoopsConfig['sitename']);
$GLOBALS['xoopsTpl']->assign('add_from_title', _ADSLIGHT_ADDFROM);
$GLOBALS['xoopsTpl']->assign('add_from_sitename', $xoopsConfig['sitename']);
$GLOBALS['xoopsTpl']->assign('mydirname', $moduleDirName);
$GLOBALS['xoopsTpl']->assign('comments_head', _ADSLIGHT_COMMENTS_HEAD);
$GLOBALS['xoopsTpl']->assign('lang_user_rating', _ADSLIGHT_USER_RATING);
$GLOBALS['xoopsTpl']->assign('lang_ratethisuser', _ADSLIGHT_RATETHISUSER);
$GLOBALS['xoopsTpl']->assign('title_head', _ADSLIGHT_TITLE);
$GLOBALS['xoopsTpl']->assign('date_head', _ADSLIGHT_ADDED_ON);
$GLOBALS['xoopsTpl']->assign('views_head', _ADSLIGHT_VIEW2);
$GLOBALS['xoopsTpl']->assign('replies_head', _ADSLIGHT_REPLIES);
$GLOBALS['xoopsTpl']->assign('expires_head', _ADSLIGHT_EXPIRES_ON);
$GLOBALS['xoopsTpl']->assign('all_user_listings', _ADSLIGHT_ALL_USER_LISTINGS);
$GLOBALS['xoopsTpl']->assign('nav_main', '<a href="index.php">' . _ADSLIGHT_MAIN . '</a>');
$GLOBALS['xoopsTpl']->assign('mydirname', $moduleDirName);
$GLOBALS['xoopsTpl']->assign('xoops_module_header', '<link rel="stylesheet" href="' . XOOPS_URL . '/modules/adslight/assets/css/adslight.css" type="text/css" media="all" >');

$GLOBALS['xoopsTpl']->assign('adslight_active_menu', $helper->getConfig('adslight_active_menu'));
$GLOBALS['xoopsTpl']->assign('adslight_active_rss', $helper->getConfig('adslight_active_rss'));
$GLOBALS['xoTheme']->addMeta('meta', 'robots', 'noindex, nofollow');

$show = 4;
$min  = Request::getInt('min', 0, 'GET');
if (!isset($max)) {
    $max = $min + $show;
}
$orderby = 'date_created ASC';
$rate    = '1' === $helper->getConfig('adslight_rate_user') ? '1' : '0';
$GLOBALS['xoopsTpl']->assign('rate', $rate);

if ($GLOBALS['xoopsUser']) {
    $member_usid = $GLOBALS['xoopsUser']->getVar('uid', 'E');
    $istheirs    = $usid === $member_usid ? 1 : '';
}

$cat_perms  = '';
$categories = Utility::getMyItemIds('adslight_view');
if (is_array($categories) && count($categories) > 0) {
    $cat_perms .= ' AND cid IN (' . implode(',', $categories) . ') ';
}

if (1 === $istheirs) {
    $sql         = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('adslight_listing') . ' WHERE usid=' . $xoopsDB->escape($usid) . " AND valid='Yes' ${cat_perms}";
    $countresult = $xoopsDB->query($sql);
    [$trow] = $xoopsDB->fetchRow($countresult);

    $sql    = 'SELECT lid, cid, title, status, expire, type, desctext, tel, price, typeprice, date_created, email, submitter, usid, town, country, contactby, premium, valid, photo, hits, item_rating, item_votes, user_rating, user_votes, comments FROM '
              . $xoopsDB->prefix('adslight_listing')
              . ' WHERE usid = '
              . $xoopsDB->escape($usid)
              . " AND valid='Yes' ${cat_perms} ORDER BY ${orderby}";
    $result = $xoopsDB->query($sql, $show, $min);
} else {
    $sql         = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('adslight_listing') . ' WHERE usid=' . $xoopsDB->escape($usid) . " AND valid='Yes' AND status!='1' ${cat_perms}";
    $countresult = $xoopsDB->query($sql);
    [$trow] = $xoopsDB->fetchRow($countresult);

    $sql    = 'SELECT lid, cid, title, status, expire, type, desctext, tel, price, typeprice, date_created, email, submitter, usid, town, country, contactby, premium, valid, photo, hits, item_rating, item_votes, user_rating, user_votes, comments FROM '
              . $xoopsDB->prefix('adslight_listing')
              . ' WHERE usid = '
              . $xoopsDB->escape($usid)
              . " AND valid='Yes' AND status!='1' ${cat_perms} ORDER BY ${orderby}";
    $result = $xoopsDB->query($sql, $show, $min);
}

$trows   = $trow;
$pagenav = '';
if ($trows > '0') {
    $GLOBALS['xoopsTpl']->assign('min', $min);
    $rank = 1;

    if ($trows > '1') {
        $GLOBALS['xoopsTpl']->assign('show_nav', true);
        $GLOBALS['xoopsTpl']->assign('lang_sortby', _ADSLIGHT_SORTBY);
        $GLOBALS['xoopsTpl']->assign('lang_title', _ADSLIGHT_TITLE);
        $GLOBALS['xoopsTpl']->assign('lang_titleatoz', _ADSLIGHT_TITLEATOZ);
        $GLOBALS['xoopsTpl']->assign('lang_titleztoa', _ADSLIGHT_TITLEZTOA);
        $GLOBALS['xoopsTpl']->assign('lang_date', _ADSLIGHT_DATE);
        $GLOBALS['xoopsTpl']->assign('lang_dateold', _ADSLIGHT_DATEOLD);
        $GLOBALS['xoopsTpl']->assign('lang_datenew', _ADSLIGHT_DATENEW);
        $GLOBALS['xoopsTpl']->assign('lang_popularity', _ADSLIGHT_POPULARITY);
        $GLOBALS['xoopsTpl']->assign('lang_popularityleast', _ADSLIGHT_POPULARITYLTOM);
        $GLOBALS['xoopsTpl']->assign('lang_popularitymost', _ADSLIGHT_POPULARITYMTOL);
    }
    while (false !== [$lid, $cid, $title, $status, $expire, $type, $desctext, $tel, $price, $typeprice, $date_created, $email, $submitter, $usid, $town, $country, $contactby, $premium, $valid, $photo, $hits, $item_rating, $item_votes, $user_rating, $user_votes, $comments] = $xoopsDB->fetchRow(
            $result
        )) {
        $newitem   = '';
        $newcount  = $helper->getConfig('adslight_countday');
        $startdate = time() - (86400 * $newcount);
        if ($startdate < $date_created) {
            //@todo move "New" alt text to language file
            $newitem = '<img src="' . XOOPS_URL . '/modules/adslight/assets/images/newred.gif" alt="New" >';
        }
        if (0 === (int)$status) {
            $status_is = _ADSLIGHT_ACTIVE;
        }
        if (1 === (int)$status) {
            $status_is = _ADSLIGHT_INACTIVE;
        }
        if (2 === (int)$status) {
            $status_is = _ADSLIGHT_SOLD;
        }
        $sql         = 'SELECT COUNT(*) FROM ' . $xoopsDB->prefix('adslight_replies') . " WHERE lid='" . $xoopsDB->escape($lid) . "'";
        $countresult = $xoopsDB->query($sql);
        [$rrow] = $xoopsDB->fetchRow($countresult);
        $rrows = $rrow;
        $GLOBALS['xoopsTpl']->assign('reply_count', $rrows);

        $sql     = 'SELECT r_lid, lid, date_created, submitter, message, email, r_usid FROM ' . $xoopsDB->prefix('adslight_replies') . ' WHERE lid =' . $xoopsDB->escape($lid);
        $result2 = $xoopsDB->query($sql);
        [$r_lid, $rlid, $rdate, $rsubmitter, $message, $remail, $r_usid] = $xoopsDB->fetchRow($result2);

        //Fix bug for type and typeprice
        $sql     = 'SELECT nom_type FROM ' . $xoopsDB->prefix('adslight_type') . ' WHERE id_type=' . (int)$type;
        $result7 = $xoopsDB->query($sql);
        [$nom_type] = $xoopsDB->fetchRow($result7);

        // $sql = 'SELECT nom_price FROM ' . $xoopsDB->prefix('adslight_price') . " WHERE id_price='" . $xoopsDB->escape($typeprice) . "'";
        //        $result8 = $xoopsDB->query($sql);
        //        [$nom_price] = $xoopsDB->fetchRow($result8);

        $sql     = 'SELECT nom_price FROM ' . $xoopsDB->prefix('adslight_price') . ' WHERE id_price=' . (int)$typeprice;
        $result8 = $xoopsDB->query($sql);
        [$nom_price] = $xoopsDB->fetchRow($result8);

        if ($isadmin) {
            $adminlink = "<a href='" . XOOPS_URL . '/modules/adslight/admin/validate_ads.php?op=modifyAds&amp;lid=' . $lid . "'><img src='" . $pathIcon16 . "/edit.png' border=0 alt=\"" . _ADSLIGHT_MODADMIN . '" ></a>';
            $GLOBALS['xoopsTpl']->assign('isadmin', $isadmin);
        } else {
            $adminlink = '';
        }
        $modify_link = '';
        if ($GLOBALS['xoopsUser'] instanceof \XoopsUser) {
            $member_usid = $GLOBALS['xoopsUser']->getVar('uid', 'E');
            if ($usid === $member_usid) {
                $istheirs = true;
                $GLOBALS['xoopsTpl']->assign('istheirs', $istheirs);
                $modify_link = "<a href='modify.php?op=modad&amp;lid=" . $lid . "'><img src='" . $pathIcon16 . "/edit.png'  border=0 alt=\"" . _ADSLIGHT_MODADMIN . '" ></a>';
            } else {
                $istheirs = false;
                $GLOBALS['xoopsTpl']->assign('istheirs', '');
            }
        }

        $GLOBALS['xoopsTpl']->assign('submitter', $submitter);
        $GLOBALS['xoopsTpl']->assign('usid', $usid);
        $GLOBALS['xoopsTpl']->assign('read', "${hits} " . _ADSLIGHT_VIEW2);
        $GLOBALS['xoopsTpl']->assign('rating', number_format((float)$user_rating, 2));
        $GLOBALS['xoopsTpl']->assign('status_head', _ADSLIGHT_STATUS);
        $tempXoopsLocal = new \XoopsLocal();
        //  For US currency with 2 numbers after the decimal comment out if you don't want 2 numbers after decimal
        $priceFormatted = $tempXoopsLocal->number_format($price);
        //  For other countries uncomment the below line and comment out the above line
        //      $priceFormatted = $tempXoopsLocal->number_format($price);

        //        $GLOBALS['xoopsTpl']->assign('price', '<strong>' . _ADSLIGHT_PRICE . "</strong>$price" . $helper->getConfig('adslight_currency_symbol') . " - $typeprice");

        //        $currencyCode                 = $helper->getConfig('adslight_currency_code');
        //        $currencySymbol               = $helper->getConfig('adslight_currency_symbol');
        //        $currencyPosition             = $helper->getConfig('currency_position');
        //        $formattedCurrencyUtilityTemp = Utility::formatCurrencyTemp($price, $currencyCode, $currencySymbol, $currencyPosition);
        //        $priceHtml                    = '<strong>' . _ADSLIGHT_PRICE2 . '</strong>' . $formattedCurrencyUtilityTemp . ' - ' . $typeprice;

        //        $GLOBALS['xoopsTpl']->assign('price', $priceHtml);

        $GLOBALS['xoopsTpl']->assign('price_head', _ADSLIGHT_PRICE);
        $GLOBALS['xoopsTpl']->assign('money_sign', '' . $helper->getConfig('adslight_currency_symbol'));
        $GLOBALS['xoopsTpl']->assign('price_typeprice', $typeprice);

        $GLOBALS['xoopsTpl']->assign('type', htmlspecialchars($nom_type, ENT_QUOTES | ENT_HTML5));

        $priceTypeprice = \htmlspecialchars($nom_price, ENT_QUOTES | ENT_HTML5);
        $priceCurrency  = $helper->getConfig('adslight_currency_code');

        $currencyCode                 = $helper->getConfig('adslight_currency_code');
        $currencySymbol               = $helper->getConfig('adslight_currency_symbol');
        $currencyPosition             = $helper->getConfig('currency_position');
        $formattedCurrencyUtilityTemp = Utility::formatCurrencyTemp($price, $currencyCode, $currencySymbol, $currencyPosition);
        $priceHtml                    = '<strong>' . _ADSLIGHT_PRICE2 . '</strong>' . $formattedCurrencyUtilityTemp . ' - ' . $priceTypeprice;

        $GLOBALS['xoopsTpl']->assign('price_head', _ADSLIGHT_PRICE2);
        $GLOBALS['xoopsTpl']->assign('price_price', $priceFormatted);
        $GLOBALS['xoopsTpl']->assign('price_typeprice', $priceTypeprice);
        $GLOBALS['xoopsTpl']->assign('price_currency', $priceCurrency);
        $GLOBALS['xoopsTpl']->assign('price', $priceHtml);
        $GLOBALS['xoopsTpl']->assign('priceHtml', $priceHtml);

        $GLOBALS['xoopsTpl']->assign('local_town', (string)$town);
        $GLOBALS['xoopsTpl']->assign('local_country', (string)$country);
        $GLOBALS['xoopsTpl']->assign('local_head', _ADSLIGHT_LOCAL2);
        $GLOBALS['xoopsTpl']->assign('edit_ad', _ADSLIGHT_EDIT);

        $usid       = addslashes($usid);
        $votestring = 1 === $user_votes ? _ADSLIGHT_ONEVOTE : sprintf(_ADSLIGHT_NUMVOTES, $user_votes);

        $GLOBALS['xoopsTpl']->assign('user_votes', $votestring);
        $date2        = $date_created + ($expire * 86400);
        $date_created = formatTimestamp($date_created, 's');
        $date2        = formatTimestamp($date2, 's');
        $path         = $mytree->getPathFromId($cid, 'title');
        $path         = mb_substr($path, 1);
        $path         = str_replace('/', ' - ', $path);
        if ($rrows >= 1) {
            $view_now = "<a href='replies.php?lid=" . $lid . "'>" . _ADSLIGHT_VIEWNOW . '</a>';
        } else {
            $view_now = '';
        }
        $sold = '';
        if (2 === (int)$status) {
            $sold = _ADSLIGHT_RESERVEDMEMBER;
        }

        $GLOBALS['xoopsTpl']->assign('xoops_pagetitle', '' . _ADSLIGHT_ALL_USER_LISTINGS . ' ' . $submitter);
        $updir   = $helper->getConfig('adslight_link_upload');
        $sql     = 'SELECT cod_img, lid, uid_owner, url FROM ' . $xoopsDB->prefix('adslight_pictures') . ' WHERE  uid_owner=' . $xoopsDB->escape($usid) . ' AND lid=' . $xoopsDB->escape($lid) . ' ORDER BY date_created ASC LIMIT 1';
        $resultp = $xoopsDB->query($sql);
        while ([$cod_img, $pic_lid, $uid_owner, $url] = $xoopsDB->fetchRow($resultp)) {
            if ($photo) {
                $photo = "<a href='viewads.php?lid=" . $lid . "'><img class=\"thumb\" src=\"${updir}/thumbs/thumb_${url}\" align=\"left\" width=\"100px\" alt=\"${title}\" ></a>";
            }
        }
        $no_photo = "<a href='viewads.php?lid=" . $lid . "'><img class=\"thumb\" src=\"assets/images/nophoto.jpg\" align=\"left\" width=\"100px\" alt=\"${title}\" ></a>";

        $GLOBALS['xoopsTpl']->append('items', [
            'id'           => $lid,
            'cid'          => $cid,
            'title'        => \htmlspecialchars($title, ENT_QUOTES | ENT_HTML5),
            'status'       => \htmlspecialchars($status_is, ENT_QUOTES | ENT_HTML5),
            'expire'       => \htmlspecialchars($expire, ENT_QUOTES | ENT_HTML5),
            'type'         => \htmlspecialchars($type, ENT_QUOTES | ENT_HTML5),
            'desctext'     => $myts->displayTarea($desctext),
            'tel'          => \htmlspecialchars($tel, ENT_QUOTES | ENT_HTML5),
            //            'price'        => \htmlspecialchars($price, ENT_QUOTES | ENT_HTML5),
            'price'        => \htmlspecialchars($formattedCurrencyUtilityTemp, ENT_QUOTES | ENT_HTML5),
            'typeprice'    => \htmlspecialchars($typeprice, ENT_QUOTES | ENT_HTML5),
            'date_created' => \htmlspecialchars($date_created ?? '', ENT_QUOTES | ENT_HTML5), //TODO check date
            'email'        => \htmlspecialchars($email, ENT_QUOTES | ENT_HTML5),
            'submitter'    => \htmlspecialchars($submitter, ENT_QUOTES | ENT_HTML5),
            'usid'         => \htmlspecialchars($usid, ENT_QUOTES | ENT_HTML5),
            'town'         => \htmlspecialchars($town, ENT_QUOTES | ENT_HTML5),
            'country'      => \htmlspecialchars($country, ENT_QUOTES | ENT_HTML5),
            'contactby'    => \htmlspecialchars($contactby, ENT_QUOTES | ENT_HTML5),
            'premium'      => \htmlspecialchars($premium, ENT_QUOTES | ENT_HTML5),
            'valid'        => \htmlspecialchars($valid, ENT_QUOTES | ENT_HTML5),
            'hits'         => $hits,
            'rlid'         => \htmlspecialchars($rlid ?? '', ENT_QUOTES | ENT_HTML5),
            'rdate'        => \htmlspecialchars($rdate ?? '', ENT_QUOTES | ENT_HTML5),
            'rsubmitter'   => \htmlspecialchars($rsubmitter ?? '', ENT_QUOTES | ENT_HTML5),
            'message'      => \htmlspecialchars($message ?? '', ENT_QUOTES | ENT_HTML5),
            'remail'       => \htmlspecialchars($remail ?? '', ENT_QUOTES | ENT_HTML5),
            'rrows'        => $rrows,
            'expires'      => \htmlspecialchars($date2, ENT_QUOTES | ENT_HTML5),
            'view_now'     => $view_now,
            'modify_link'  => $modify_link,
            'photo'        => $photo,
            'no_photo'     => $no_photo,
            'adminlink'    => $adminlink,
            'new'          => $newitem,
            'sold'         => $sold,
        ]);
    }
    $usid = Request::getInt('usid', 0, 'GET');

    //Calculates how many pages exist.  Which page one should be on, etc...
    $linkpages = ceil($trows / $show);
    //Page Numbering
    if (1 !== (int)$linkpages && 0 !== (int)$linkpages) {
        $prev = $min - $show;
        if ($prev >= 0) {
            $pagenav .= "<a href='members.php?usid=${usid}&min=${prev}&show=${show}'><strong><u>&laquo;</u></strong></a> ";
        }
        $counter     = 1;
        $currentpage = $max / $show;
        while ($counter <= $linkpages) {
            $mintemp = ($show * $counter) - $show;
            if ($counter === $currentpage) {
                $pagenav .= "<strong>(${counter})</strong> ";
            } else {
                $pagenav .= "<a href='members.php?usid=${usid}&min=${mintemp}&show=${show}'>${counter}</a> ";
            }
            ++$counter;
        }
        if ($trows > $max) {
            $pagenav .= "<a href='members.php?usid=${usid}&min=${max}&show=${show}'>";
            $pagenav .= '<strong><u>&raquo;</u></strong></a>';
        }
        $GLOBALS['xoopsTpl']->assign('nav_page', '<strong>' . _ADSLIGHT_PAGES . "</strong>&nbsp;&nbsp; ${pagenav}");
    }
}

require_once XOOPS_ROOT_PATH . '/footer.php';
