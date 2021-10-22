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

use Xmf\Request;
use XoopsModules\Adslight\{
    Helper,
    Tree,
    Utility
};

/** @var Helper $helper */

require_once __DIR__ . '/header.php';

global $xoopsModule, $xoopsDB, $xoopsConfig, $xoTheme;

$myts      = \MyTextSanitizer::getInstance();
$moduleId = $xoopsModule->getVar('mid');
$groups    = $GLOBALS['xoopsUser'] instanceof \XoopsUser ? $GLOBALS['xoopsUser']->getGroups() : XOOPS_GROUP_ANONYMOUS;
/** @var \XoopsGroupPermHandler $grouppermHandler */
$grouppermHandler = xoops_getHandler('groupperm');
$perm_itemid      = Request::getInt('item_id', 0, 'POST');

//If no access
if (!$grouppermHandler->checkRight('adslight_submit', $perm_itemid, $groups, $moduleId)) {
    $helper->redirect('index.php', 3, _NOPERM);
}

/**
 * @param $lid
 * @param $ok
 */
function listingDel($lid, $ok): void
{
    global $xoopsDB;
    $helper = Helper::getInstance();
    $result = $xoopsDB->query(
        'SELECT usid FROM ' . $xoopsDB->prefix('adslight_listing') . ' WHERE lid=' . $xoopsDB->escape($lid)
    );
    [$usid] = $xoopsDB->fetchRow($result);
    $result1 = $xoopsDB->query(
        'SELECT url FROM ' . $xoopsDB->prefix('adslight_pictures') . ' WHERE lid=' . $xoopsDB->escape($lid)
    );
    if ($GLOBALS['xoopsUser']) {
        $currentid = $GLOBALS['xoopsUser']->getVar('uid', 'E');
        if ($usid === $currentid) {
            if (1 === $ok) {
                while ([$purl] = $xoopsDB->fetchRow($result1)) {
                    if ($purl) {
                        $destination = XOOPS_ROOT_PATH . '/uploads/adslight';
                        if (is_file("${destination}/${purl}")) {
                            unlink("${destination}/${purl}");
                        }
                        $destination2 = XOOPS_ROOT_PATH . '/uploads/adslight/thumbs';
                        if (is_file("${destination2}/thumb_${purl}")) {
                            unlink("${destination2}/thumb_${purl}");
                        }
                        $destination3 = XOOPS_ROOT_PATH . '/uploads/adslight/midsize';
                        if (is_file("${destination3}/resized_${purl}")) {
                            unlink("${destination3}/resized_${purl}");
                        }
                        $xoopsDB->queryF(
                            'DELETE FROM ' . $xoopsDB->prefix(
                                'adslight_pictures'
                            ) . ' WHERE lid=' . $xoopsDB->escape($lid)
                        );
                    }
                }
                $xoopsDB->queryF(
                    'DELETE FROM ' . $xoopsDB->prefix('adslight_listing') . ' WHERE lid=' . $xoopsDB->escape($lid)
                );
                $helper->redirect('index.php', 1, _ADSLIGHT_ANNDEL);
            } else {
                echo "<table width='100%' border='0' cellspacing='1' cellpadding='8'><tr class='bg4'><td valign='top'>\n";
                echo '<br><div style="text-align:center">';
                echo '<strong>' . _ADSLIGHT_SURDELANN . '</strong></div><br><br>';
            }
            echo '[ <a href="modify.php?op=ListingDel&amp;lid=' . $lid . '&amp;ok=1">' . _YES . '</a> | <a href="index.php">' . _NO . '</a> ]<br><br>';
            echo '</td></tr></table>';
        }
    }
}

/**
 * @param $r_lid
 * @param $ok
 */
function delReply($r_lid, $ok): void
{
    global $xoopsDB;
    $helper = Helper::getInstance();
    $sql    = 'SELECT l.usid, r.r_lid, r.lid, r.title, r.date_created, r.submitter, r.message, r.tele, r.email, r.r_usid FROM ' . $xoopsDB->prefix(
            'adslight_listing'
        ) . ' l LEFT JOIN ' . $xoopsDB->prefix(
            'adslight_replies'
        ) . ' r ON l.lid=r.lid  WHERE r.r_lid=' . $xoopsDB->escape($r_lid);
    $result = $xoopsDB->query($sql);
    [$usid, $r_lid, $rlid, $title, $date_created, $submitter, $message, $tele, $email, $r_usid] = $xoopsDB->fetchRow(
        $result
    );
    if ($GLOBALS['xoopsUser']) {
        $currentid = $GLOBALS['xoopsUser']->getVar('uid', 'E');
        if ($usid === $currentid) {
            if (1 === $ok) {
                $xoopsDB->queryF(
                    'DELETE FROM ' . $xoopsDB->prefix('adslight_replies') . ' WHERE r_lid=' . $xoopsDB->escape($r_lid)
                );
                $helper->redirect('members.php?usid=' . addslashes($usid) . '', 1, _ADSLIGHT_ANNDEL);
            } else {
                echo "<table width='100%' border='0' cellspacing='1' cellpadding='8'><tr class='bg4'><td valign='top'>\n";
                echo '<br><div style="text-align:center">';
                echo '<strong>' . _ADSLIGHT_SURDELANN . '</strong></div><br><br>';
            }
            echo '[ <a href="modify.php?op=DelReply&amp;r_lid=' . addslashes(
                    $r_lid
                ) . '&amp;ok=1">' . _YES . '</a> | <a href="members.php?usid=' . addslashes(
                     $usid
                 ) . '">' . _NO . '</a> ]<br><br>';
            echo '</td></tr></table>';
        }
    }
}

/**
 * @param $lid
 */
function modifyAd($lid): void
{
    global $xoopsDB, $xoopsModule, $xoopsConfig, $myts;
    $contactselect = '';
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    $helper            = Helper::getInstance();
    $options           = [];
    $options['name']   = 'Editor';
    $options['value']  = _ADSLIGHT_DESC;
    $options['rows']   = 10;
    $options['cols']   = '100%';
    $options['width']  = '100%';
    $options['height'] = '200px';
    echo "<script language=\"javascript\">\nfunction CLA(CLA) { var MainWindow = window.open (CLA, \"_blank\",\"width=500,height=300,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no\");}\n</script>";

    $mytree = new Tree($xoopsDB->prefix('adslight_categories'), 'cid', 'pid');
    $sql    = 'SELECT lid, cid, title, status, expire, type, desctext, tel, price, typeprice, typecondition, date_created, email, submitter, usid, town, country, contactby, premium, valid FROM ' . $xoopsDB->prefix(
            'adslight_listing'
        ) . ' WHERE lid=' . $xoopsDB->escape(
            $lid
        );
    $result = $xoopsDB->query($sql);
    [$lid, $cide, $title, $status, $expire, $type, $desctext, $tel, $price, $typeprice, $typecondition, $date_created, $email, $submitter, $usid, $town, $country, $contactby, $premium, $valid] = $xoopsDB->fetchRow(
        $result
    );
    $categories = Utility::getMyItemIds('adslight_submit');
    if (is_array($categories) && count($categories) > 0) {
        if (!\in_array((int)$cide, $categories, true)) {
            $helper->redirect('index.php', 3, _NOPERM);
        }
    } else {    // User can't see any category
        redirect_header(XOOPS_URL . '/index.php', 3, _NOPERM);
    }

    if ($GLOBALS['xoopsUser']) {
        $calusern = $GLOBALS['xoopsUser']->uid();
        if ((int)$usid === $calusern) {
            echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _ADSLIGHT_MODIFANN . '</legend><br><br>';
            $title    = \htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);
            $status   = \htmlspecialchars($status, ENT_QUOTES | ENT_HTML5);
            $expire   = \htmlspecialchars($expire, ENT_QUOTES | ENT_HTML5);
            $type     = \htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);
            $desctext = $myts->displayTarea($desctext, 1);
            $tel      = \htmlspecialchars($tel, ENT_QUOTES | ENT_HTML5);

            //            $price      = number_format($price, 2, ',', ' ');

            xoops_load('XoopsLocal');
            $tempXoopsLocal = new \XoopsLocal();
            //  For US currency with 2 numbers after the decimal comment out if you don't want 2 numbers after decimal
            $price = $tempXoopsLocal->number_format($price);
            //  For other countries uncomment the below line and comment out the above line
            //      $price = $tempXoopsLocal->number_format($price);

            $typeprice     = \htmlspecialchars($typeprice, ENT_QUOTES | ENT_HTML5);
            $typecondition = \htmlspecialchars($typecondition, ENT_QUOTES | ENT_HTML5);
            $submitter     = \htmlspecialchars($submitter, ENT_QUOTES | ENT_HTML5);
            $town          = \htmlspecialchars($town, ENT_QUOTES | ENT_HTML5);
            $country       = \htmlspecialchars($country, ENT_QUOTES | ENT_HTML5);
            $contactby     = \htmlspecialchars($contactby, ENT_QUOTES | ENT_HTML5);
            $premium       = \htmlspecialchars($premium, ENT_QUOTES | ENT_HTML5);
            $useroffset    = '';
            if ($GLOBALS['xoopsUser']) {
                $timezone   = $GLOBALS['xoopsUser']->timezone();
                $useroffset = empty($timezone) ? $xoopsConfig['default_TZ'] : $GLOBALS['xoopsUser']->timezone();
            }
            $dates = formatTimestamp($date_created, 's');

            echo '<form action="modify.php" method=post enctype="multipart/form-data">';
            echo $GLOBALS['xoopsSecurity']->getTokenHTML();
            echo '<table><tr class="head" border="2">
    <td class="head">' . _ADSLIGHT_NUMANNN . " </td><td class=\"head\" border=\"1\">${lid} " . _ADSLIGHT_DU . " ${dates}</td>
    </tr><tr>";
            if ('1' === $helper->getConfig('adslight_diff_name')) {
                echo '<td class="head">' . _ADSLIGHT_SENDBY . " </td><td class=\"head\"><input type=\"text\" name=\"submitter\" size=\"50\" value=\"${submitter}\" ></td>";
            } else {
                echo '<td class="head">' . _ADSLIGHT_SENDBY . " </td><td class=\"head\"><input type=\"hidden\" name=\"submitter\" value=\"${submitter}\">${submitter}</td>";
            }
            echo '</tr><tr>';
            if (1 === $contactby) {
                $contactselect = _ADSLIGHT_CONTACT_BY_EMAIL;
            }
            if (2 === $contactby) {
                $contactselect = _ADSLIGHT_CONTACT_BY_PM;
            }
            if (3 === $contactby) {
                $contactselect = _ADSLIGHT_CONTACT_BY_BOTH;
            }
            if (4 === $contactby) {
                $contactselect = _ADSLIGHT_CONTACT_BY_PHONE;
            }

            echo " <td class='head'>" . _ADSLIGHT_CONTACTBY . " </td><td class='head'><select name=\"contactby\">
    <option value=\"" . $contactby . '">' . $contactselect . '</option>
    <option value="1">' . _ADSLIGHT_CONTACT_BY_EMAIL . '</option>
    <option value="2">' . _ADSLIGHT_CONTACT_BY_PM . '</option>
    <option value="3">' . _ADSLIGHT_CONTACT_BY_BOTH . '</option>
    <option value="4">' . _ADSLIGHT_CONTACT_BY_PHONE . '</option></select></td></tr>';
            if ('1' === $helper->getConfig('adslight_diff_email')) {
                echo '<tr><td class="head">' . _ADSLIGHT_EMAIL . " </td><td class=\"head\"><input type=\"text\" name=\"email\" size=\"50\" value=\"${email}\" ></td>";
            } else {
                echo '<tr><td class="head">' . _ADSLIGHT_EMAIL . " </td><td class=\"head\">${email}<input type=\"hidden\" name=\"email\" value=\"${email}\" ></td>";
            }
            echo '</tr><tr>
    <td class="head">' . _ADSLIGHT_TEL . " </td><td class=\"head\"><input type=\"text\" name=\"tel\" size=\"50\" value=\"${tel}\" ></td>
    </tr>";
            echo '<tr>
    <td class="head">' . _ADSLIGHT_TOWN . " </td><td class=\"head\"><input type=\"text\" name=\"town\" size=\"50\" value=\"${town}\" ></td>
    </tr>";
            if ('1' === $helper->getConfig('adslight_use_country')) {
                echo '<tr>
    <td class="head">' . _ADSLIGHT_COUNTRY . " </td><td class=\"head\"><input type=\"text\" name=\"country\" size=\"50\" value=\"${country}\" ></td>
    </tr>";
            } else {
                echo '<input type="hidden" name="country" value="">';
            }

            echo "<tr><td class='head'>" . _ADSLIGHT_STATUS . "</td><td class='head'><input type=\"radio\" name=\"status\" value=\"0\"";
            if (0 === (int)$status) {
                echo 'checked';
            }
            echo '>' . _ADSLIGHT_ACTIVE . '&nbsp;&nbsp; <input type="radio" name="status" value="1"';
            if (1 === (int)$status) {
                echo 'checked';
            }
            echo '>' . _ADSLIGHT_INACTIVE . '&nbsp;&nbsp; <input type="radio" name="status" value="2"';
            if (2 === (int)$status) {
                echo 'checked';
            }
            echo '>' . _ADSLIGHT_SOLD . '</td></tr>';
            echo '<tr>
    <td class="head">' . _ADSLIGHT_TITLE2 . " </td><td class=\"head\"><input type=\"text\" name=\"title\" size=\"50\" value=\"${title}\" ></td>
    </tr>";
            echo '<tr><td class="head">' . _ADSLIGHT_PRICE2 . " </td><td class=\"head\"><input type=\"text\" name=\"price\" size=\"20\" value=\"${price}\" > " . $helper->getConfig('adslight_currency_symbol');

            $sql     = 'SELECT nom_price, id_price FROM ' . $xoopsDB->prefix('adslight_price') . ' ORDER BY id_price';
            $result3 = $xoopsDB->query($sql);
            echo ' <select name="typeprice">';
            while ([$nom_price, $id_price] = $xoopsDB->fetchRow($result3)) {
                $sel = '';
                if ($id_price === $typeprice) {
                    $sel = 'selected';
                }
                echo "<option value=\"${id_price}\" ${sel}>${nom_price}</option>";
            }
            echo '</select></td></tr>';
            $moduleId = $xoopsModule->getVar('mid');
            $groups    = $GLOBALS['xoopsUser'] instanceof \XoopsUser ? $GLOBALS['xoopsUser']->getGroups() : XOOPS_GROUP_ANONYMOUS;
            /** @var \XoopsGroupPermHandler $grouppermHandler */
            $grouppermHandler = xoops_getHandler('groupperm');
            $perm_itemid      = Request::getInt('item_id', 0, 'GET');

            //If no access
            if ($grouppermHandler->checkRight('adslight_premium', $perm_itemid, $groups, $moduleId)) {
                echo "<tr>
    <td width='30%' class='head'>" . _ADSLIGHT_HOW_LONG . " </td><td class='head'><input type=\"text\" name=\"expire\" size=\"3\" maxlength=\"3\" value=\"${expire}\" >  " . _ADSLIGHT_DAY . '</td>
    </tr>';
            } else {
                echo "<tr>
    <td width='30%' class='head'>" . _ADSLIGHT_WILL_LAST . " </td><td class='head'>${expire}  " . _ADSLIGHT_DAY . '</td>
    </tr>';
                echo "<input type=\"hidden\" name=\"expire\" value=\"${expire}\" >";
            }

            /// Type d'annonce
            echo '<tr>
    <td class="head">' . _ADSLIGHT_TYPE . ' </td><td class="head"><select name="type">';

            $sql     = 'SELECT nom_type, id_type FROM ' . $xoopsDB->prefix('adslight_type') . ' ORDER BY nom_type';
            $result5 = $xoopsDB->query($sql);
            while ([$nom_type, $id_type] = $xoopsDB->fetchRow($result5)) {
                $sel = '';
                if ($id_type === $type) {
                    $sel = 'selected';
                }
                echo "<option value=\"${id_type}\" ${sel}>${nom_type}</option>";
            }
            echo '</select></td></tr>';

            /// Etat de l'objet
            echo '<tr>
    <td class="head">' . _ADSLIGHT_TYPE_CONDITION . ' </td><td class="head"><select name="typecondition">';

            $sql     = 'SELECT nom_condition, id_condition FROM ' . $xoopsDB->prefix('adslight_condition') . ' ORDER BY nom_condition';
            $result6 = $xoopsDB->query($sql);
            while ([$nom_condition, $id_condition] = $xoopsDB->fetchRow($result6)) {
                $sel = '';
                if ($id_condition === $typecondition) {
                    $sel = 'selected';
                }
                echo "<option value=\"${id_condition}\" ${sel}>${nom_condition}</option>";
            }
            echo '</select></td></tr>';

            echo '<tr>
    <td class="head">' . _ADSLIGHT_CAT . ' </td><td class="head">';
            $mytree->makeMySelBox('title', 'title', $cide, 0, 'cid');
            echo '</td>
    </tr><tr>
    <td class="head">' . _ADSLIGHT_DESC . ' </td><td class="head">';
            //            $wysiwyg_text_area = Utility::getEditor(_ADSLIGHT_DESC, 'desctext', $desctext, '100%', '200px');

//            $desctext = $myts->displayTarea($desctext, 1);

            $options           = [];
            $options['name']   = _ADSLIGHT_DESC;
            $options['value']  = $desctext;
            $options['rows']   = 10;
            $options['cols']   = '100%';
            $options['width']  = '100%';
            $options['height'] = '400px';

            $wysiwyg_text_area = Utility::getEditor($helper, $options);
            echo $wysiwyg_text_area->render();
            echo '</td></tr>
    <td colspan=2><br><input type="submit" value="' . _ADSLIGHT_MODIFANN . '" ></td>
    </tr></table>';
            echo '<input type="hidden" name="op" value="modads" >';

            $moduleId = $xoopsModule->getVar('mid');
            if (is_object($GLOBALS['xoopsUser'])) {
                $groups = &$GLOBALS['xoopsUser']->getGroups();
            } else {
                $groups = XOOPS_GROUP_ANONYMOUS;
            }
            /** @var \XoopsGroupPermHandler $grouppermHandler */
            $grouppermHandler = xoops_getHandler('groupperm');
            $perm_itemid      = Request::getInt('item_id', 0, 'POST');
            //If no access
            if ($grouppermHandler->checkRight('adslight_premium', $perm_itemid, $groups, $moduleId)) {
                echo '<input type="hidden" name="valid" value="Yes" >';
            } elseif ('1' === $helper->getConfig('adslight_moderated')) {
                echo '<input type="hidden" name="valid" value="No" >';
                echo '<br>' . _ADSLIGHT_MODIFBEFORE . '<br>';
            } else {
                echo '<input type="hidden" name="valid" value="Yes" >';
            }
            echo "<input type=\"hidden\" name=\"lid\" value=\"${lid}\" >";
            echo "<input type=\"hidden\" name=\"premium\" value=\"${premium}\" >";
            echo "<input type=\"hidden\" name=\"date_created\" value=\"${date_created}\" >
    " . $GLOBALS['xoopsSecurity']->getTokenHTML() . '';
            echo '</form><br></fieldset><br>';
        }
    }
}

/**
 * @param $lid
 * @param $cat
 * @param $title
 * @param $status
 * @param $expire
 * @param $type
 * @param $desctext
 * @param $tel
 * @param $price
 * @param $typeprice
 * @param $typecondition
 * @param $date_created
 * @param $email
 * @param $submitter
 * @param $town
 * @param $country
 * @param $contactby
 * @param $premium
 * @param $valid
 */
function modifyAds(
    $lid,
    $cat,
    $title,
    $status,
    $expire,
    $type,
    $desctext,
    $tel,
    $price,
    $typeprice,
    $typecondition,
    $date_created,
    $email,
    $submitter,
    $town,
    $country,
    $contactby,
    $premium,
    $valid
) {
    global $xoopsDB, $myts;
    $helper = Helper::getInstance();
    if (!$GLOBALS['xoopsSecurity']->check()) {
        $helper->redirect('index.php', 3, $GLOBALS['xoopsSecurity']->getErrors());
    }

    $sql = 'UPDATE '
           . $xoopsDB->prefix('adslight_listing')
           . " SET cid='${cat}', title='${title}', status='${status}',  expire='${expire}', type='${type}', desctext='${desctext}', tel='${tel}', price='${price}', typeprice='${typeprice}', typecondition='${typecondition}', email='${email}', submitter='${submitter}', town='${town}', country='${country}', contactby='${contactby}', premium='${premium}', valid='${valid}' WHERE lid=${lid}";
    $result = $xoopsDB->query($sql);

    $helper->redirect('index.php', 1, _ADSLIGHT_ANNMOD2);
}

####################################################
//foreach ($_POST as $k => $v) {
//    ${$k} = $v;
//}

$cid           = Request::getInt('cid', 0, 'POST');
$contactby     = Request::getInt('contactby', 0, 'POST');
$country       = Request::getString('country', '', 'POST');
$date_created  = Request::getInt('date_created', time(), 'POST');
$desctext      = Request::getText('Description', '', 'POST');
$email         = Request::getString('email', '', 'POST');
$expire        = Request::getInt('expire', 14, 'POST');
$lid           = Request::getInt('lid', 0, 'POST');
$op            = Request::getCmd('op', '', 'POST');
$premium       = Request::getInt('premium', 0, 'POST');
$price         = Request::getFloat('price', 0.00, 'POST');
$status        = Request::getInt('status', 0, 'POST');
$submitter     = Request::getInt('submitter', 0, 'POST');
$tel           = Request::getString('tel', '', 'POST');
$title         = Request::getString('title', '', 'POST');
$town          = Request::getString('town', '', 'POST');
$type          = Request::getInt('type', 0, 'POST');
$typecondition = Request::getInt('typecondition', 0, 'POST');
$typeprice     = Request::getInt('typeprice', 0, 'POST');
$valid         = Request::getString('valid', '', 'POST');

$ok = Request::getString('ok', '', 'GET');

if (!Request::hasVar('lid', 'POST') && Request::hasVar('lid', 'GET')) {
    $lid = Request::getInt('lid', 0, 'GET');
}
if (!Request::hasVar('r_lid', 'POST') && Request::hasVar('r_lid', 'GET')) {
    $r_lid = Request::getInt('r_lid', 0, 'GET');
}
if (!Request::hasVar('op', 'POST') && Request::hasVar('op', 'GET')) {
    $op = Request::getCmd('op', '', 'GET');
}
switch ($op) {
    case 'modad':
        require_once XOOPS_ROOT_PATH . '/header.php';
        modifyAd($lid);
        require_once XOOPS_ROOT_PATH . '/footer.php';
        break;
    case 'modads':
        modifyAds(
            $lid,
            $cid,
            $title,
            $status,
            $expire,
            $type,
            $desctext,
            $tel,
            $price,
            $typeprice,
            $typecondition,
            $date_created,
            $email,
            $submitter,
            $town,
            $country,
            $contactby,
            $premium,
            $valid
        );
        break;
    case 'ListingDel':
        require_once XOOPS_ROOT_PATH . '/header.php';
        listingDel($lid, $ok);
        require_once XOOPS_ROOT_PATH . '/footer.php';
        break;
    case 'DelReply':
        require_once XOOPS_ROOT_PATH . '/header.php';
        delReply($r_lid, $ok);
        require_once XOOPS_ROOT_PATH . '/footer.php';
        break;
    default:
        $helper->redirect('index.php', 1, '' . _RETURNANN);
        break;
}
