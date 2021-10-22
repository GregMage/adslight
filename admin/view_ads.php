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
/** @var Admin $adminObject */
/** @var Helper $helper */

require_once __DIR__ . '/admin_header.php';

$op = Request::getString('op', 'list');

#  function index
#####################################################
function index(): void
{
    global $xoopsDB, $myts, $desctext, $admin_lang;
    $helper = Helper::getInstance();

    //    $mytree = new Tree($xoopsDB->prefix('adslight_categories'), 'cid', 'pid');
    $photo3 = $photo4 = '';
    xoops_cp_header();
    //    loadModuleAdminMenu(0, '');

    // photo dir setting checker
    $photo_dir         = $helper->getConfig('adslight_path_upload', '');
    $photo_thumb_dir   = $helper->getConfig('adslight_path_upload') . '/thumbs';
    $photo_resized_dir = $helper->getConfig('adslight_path_upload') . '/midsize';
    if (!is_dir($photo_dir) && (!mkdir($photo_dir) && !is_dir($photo_dir))) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $photo_dir));
    }
    if (!is_dir($photo_thumb_dir) && (!mkdir($photo_thumb_dir) && !is_dir($photo_thumb_dir))) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $photo_thumb_dir));
    }
    if (!is_dir($photo_resized_dir) && (!mkdir($photo_resized_dir) && !is_dir($photo_resized_dir))) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $photo_resized_dir));
    }
    if (!is_writable($photo_dir) || !is_readable($photo_dir)) {
        echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_ADSLIGHT_CHECKER . '</legend><br>';
        echo "<span style='color: #FF0000;'><b>" . _AM_ADSLIGHT_DIRPERMS . '' . $photo_dir . "</b></span><br><br>\n";
        echo '</fieldset><br>';
    }

    if (!is_writable($photo_thumb_dir) || !is_readable($photo_thumb_dir)) {
        echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_ADSLIGHT_CHECKER . '</legend><br>';
        echo "<span style='color: #FF0000;'><b>" . _AM_ADSLIGHT_DIRPERMS . '' . $photo_thumb_dir . "</b></span><br><br>\n";
        echo '</fieldset><br>';
    }

    if (!is_writable($photo_resized_dir) || !is_readable($photo_resized_dir)) {
        echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_ADSLIGHT_CHECKER . '</legend><br>';
        echo "<span style='color: #FF0000;'><b>" . _AM_ADSLIGHT_DIRPERMS . '' . $photo_resized_dir . "</b></span><br><br>\n";
        echo '</fieldset><br>';
    }

    $sql     = 'SELECT lid, cid, title, status, expire, type, desctext, tel, price, typeprice, typecondition, date_created, email, submitter, town, country, contactby, premium, photo, usid FROM ' . $xoopsDB->prefix('adslight_listing') . " WHERE valid='Yes' ORDER BY lid";
    $result  = $xoopsDB->query($sql);
    $numrows = $xoopsDB->getRowsNum($result);
    if ($numrows > 0) {
        // Il y a [..] Annonces en attente d'être approuvées //////
        echo "<table class='outer bnone' cellspacing=5 cellpadding=0><tr><td width=40>";
        echo "<img src='../assets/images/admin/error_button.png' border=0 ></td><td>";
        echo "<span style='color:#00B4C4;'><b>" . _AM_ADSLIGHT_THEREIS . "</b></span> <b>${numrows}</b> <b><span style='color:#00B4C4;'>" . _AM_ADSLIGHT_ADSVALIDE . '</b></span>';
        echo '</td></tr></table><br>';

        // Liste des ID  ///// Soumis par /////  Titre   /////  Description  /////  Date d'ajout
        echo "<table class='outer width100 bnone'>";
        $rank = 1;

        while (false !== [$lid, $cid, $title, $status, $expire, $type, $desctext, $tel, $price, $typeprice, $typecondition, $date_created, $email, $submitter, $town, $country, $contactby, $premium, $photo, $usid] = $xoopsDB->fetchRow($result)) {
            $title    = \htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);
            $desctext = $myts->displayTarea($desctext, 1, 0, 1, 1, 1);

            if (mb_strlen($desctext) >= 200) {
                $desctext = mb_substr($desctext, 0, 199) . '...';
            } else {
                $desctext = $myts->displayTarea($desctext, 1, 1, 1);
            }
            $date2  = formatTimestamp($date_created, 's');
            $color  = is_int($rank / 2) ? '#ffffff' : 'head';
            $status = \htmlspecialchars($status, ENT_QUOTES | ENT_HTML5);
            $expire = \htmlspecialchars($expire, ENT_QUOTES | ENT_HTML5);
            $type   = \htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);
            $tel    = \htmlspecialchars($tel, ENT_QUOTES | ENT_HTML5);
            //            $price = number_format($price, 2, ',', ' ');
            $typeprice     = \htmlspecialchars($typeprice, ENT_QUOTES | ENT_HTML5);
            $typecondition = \htmlspecialchars($typecondition, ENT_QUOTES | ENT_HTML5);
            $submitter     = \htmlspecialchars($submitter, ENT_QUOTES | ENT_HTML5);
            $town          = \htmlspecialchars($town, ENT_QUOTES | ENT_HTML5);
            $country       = \htmlspecialchars($country, ENT_QUOTES | ENT_HTML5);
            $contactby     = \htmlspecialchars($contactby, ENT_QUOTES | ENT_HTML5);
            $premium       = \htmlspecialchars($premium, ENT_QUOTES | ENT_HTML5);

            $updir   = $helper->getConfig('adslight_link_upload');
            $sql     = 'SELECT cod_img, lid, uid_owner, url FROM ' . $xoopsDB->prefix('adslight_pictures') . " WHERE  uid_owner={$usid} AND lid={$lid} ORDER BY date_created ASC LIMIT 1";
            $resultp = $xoopsDB->query($sql);
            while ([$cod_img, $pic_lid, $uid_owner, $url] = $xoopsDB->fetchRow($resultp)) {
                if ($photo) {
                    $photo3 = "<a href='" . XOOPS_URL . "/modules/adslight/viewads.php?lid={$lid}'><img class=\"thumb\" src=\"{$updir}/thumbs/thumb_{$url}\" align=\"left\" width=\"100px\" alt=\"{$title}\"></a>";
                }
            }
            if ($photo <= 0) {
                $photo3 = "<a href=\"index.php?op=indexView&lid=${lid}\"><img class=\"thumb\" src=\"" . XOOPS_URL . "/modules/adslight/assets/images/nophoto.jpg\" align=\"left\" width=\"100px\" alt=\"${title}\"></a>";
            } else {
                $photo4 = (string)$photo;
            }

            $sql     = 'SELECT nom_type FROM ' . $xoopsDB->prefix('adslight_type') . ' WHERE id_type=' . (int)$type;
            $result7 = $xoopsDB->query($sql);
            [$nom_type] = $xoopsDB->fetchRow($result7);

            $sql     = 'SELECT nom_price FROM ' . $xoopsDB->prefix('adslight_price') . ' WHERE id_price=' . (int)$typeprice;
            $result8 = $xoopsDB->query($sql);
            [$nom_price] = $xoopsDB->fetchRow($result8);

            echo '<form action="view_ads.php" method="post">';
            echo $GLOBALS['xoopsSecurity']->getTokenHTML();
            echo "<tr><th class='left'>" . _AM_ADSLIGHT_LID . ": {$lid}</th><th class='left'>{$photo4} " . _AM_ADSLIGHT_NBR_PHOTO . "</th><th class='left'>" . _AM_ADSLIGHT_TITLE . ":</th><th class='left'>" . _AM_ADSLIGHT_DESC . "</th><th class='left'><!--" . _AM_ADSLIGHT_ACTIONS . '--></th></tr>';

            echo "<tr><td class='even width3'></td>";
            echo "<td class='odd width10' >{$photo3}</td>";
            echo "<td class='even width20'><b>{$title}</b><br><br>{$nom_type}<br>{$price} " . $helper->getConfig('adslight_currency_symbol') . " ${nom_price}<br>";
            echo "${town} - ${country}<br>";
            echo '<b>' . _AM_ADSLIGHT_SUBMITTER . ":</b> {$submitter}<br>";
            echo '<b>' . _AM_ADSLIGHT_DATE . ":</b> {$date2}</td>";
            echo "<td class='even width='35%'>{$desctext}</td><td class='even right width2'></td>";
            echo "</tr><tr><td width='5%'></td><td>";

            echo '<select name="op">
        <option value="modifyAds"> ' . _AM_ADSLIGHT_MODIF . '
        <option value="listingDel"> ' . _AM_ADSLIGHT_DEL . '
               </select><input type="submit" value="' . _AM_ADSLIGHT_GO . '">';

            echo '<input type="hidden" name="valid" value="Yes">';
            echo "<input type=\"hidden\" name=\"lid\" value=\"{$lid}\">";
            echo "<input type=\"hidden\" name=\"cid\" value=\"{$cid}\">";
            echo "<input type=\"hidden\" name=\"title\" value=\"{$title}\">";
            echo "<input type=\"hidden\" name=\"status\" value=\"{$status}\">";
            echo "<input type=\"hidden\" name=\"expire\" value=\"{$expire}\">";
            echo "<input type=\"hidden\" name=\"type\" value=\"{$type}\">";
            echo "<input type=\"hidden\" name=\"desctext\" value=\"{$desctext}\">";
            echo "<input type=\"hidden\" name=\"tel\" value=\"{$tel}\">";
            echo "<input type=\"hidden\" name=\"price\" value=\"{$price}\">";
            echo "<input type=\"hidden\" name=\"typeprice\" value=\"{$typeprice}\">";
            echo "<input type=\"hidden\" name=\"typecondition\" value=\"{$typecondition}\">";
            echo "<input type=\"hidden\" name=\"date_created\" value=\"{$date_created}\">";
            echo "<input type=\"hidden\" name=\"email\" value=\"{$email}\">";
            echo "<input type=\"hidden\" name=\"submitter\" value=\"{$submitter}\">";
            echo "<input type=\"hidden\" name=\"town\" value=\"{$town}\">";
            echo "<input type=\"hidden\" name=\"country\" value=\"{$country}\">";
            echo "<input type=\"hidden\" name=\"contactby\" value=\"{$contactby}\">";
            echo "<input type=\"hidden\" name=\"premium\" value=\"{$premium}\">";
            echo "<input type=\"hidden\" name=\"photo\" value=\"{$photo}\">";
            echo '</form><br></td></tr>';
            ++$rank;
        }

        echo '</td></tr></table>
              <br><br>';
    } else {
        echo "<table class='outer width50 bnone'><tr><td class='width40'>";
        echo "<img src='../assets/images/admin/search_button_green_32.png' border=0 ></td><td>";
        echo "<span style='color: #00B4C4;'><b>" . _AM_ADSLIGHT_NOANNVALADS . '</b></span>';
        echo '</td></tr></table><br>';
    }

    xoops_cp_footer();
}

#  function indexView
#####################################################
/**
 * @param null $lid
 */
function indexView($lid = null): void
{
    global $xoopsDB, $myts, $desctext, $admin_lang;
    $helper = Helper::getInstance();
    $mytree = new Tree($xoopsDB->prefix('adslight_categories'), 'cid', 'pid');

    require_once __DIR__ . '/admin_header.php';
    xoops_cp_header();
    //    loadModuleAdminMenu(0, '');

    $sql     = 'SELECT lid, cid, title, status, expire, type, desctext, tel, price, typeprice, typecondition, date_created, email, submitter, town, country, contactby, premium, photo FROM ' . $xoopsDB->prefix('adslight_listing') . " WHERE valid='No' AND lid='{$lid}'";
    $result  = $xoopsDB->query($sql);
    $numrows = $xoopsDB->getRowsNum($result);
    if ($numrows > 0) {
        echo "<table class='width100' cellspacing='1' cellpadding='8' style='border: 2px solid #DFE0E0;'><tr class='bg4'><td class='top'>";
        echo '<b>' . _AM_ADSLIGHT_WAIT . '</b><br><br>';

        [$lid, $cid, $title, $status, $expire, $type, $desctext, $tel, $price, $typeprice, $typecondition, $date_created, $email, $submitter, $town, $country, $contactby, $premium, $photo] = $xoopsDB->fetchRow($result);

        $lid      = (int)$lid;
        $cid      = (int)$cid;
        $date2    = formatTimestamp($date_created, 's');
        $title    = \htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);
        $status   = \htmlspecialchars($status, ENT_QUOTES | ENT_HTML5);
        $expire   = \htmlspecialchars($expire, ENT_QUOTES | ENT_HTML5);
        $type     = \htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);
        $desctext = $myts->displayTarea($desctext, 1, 1, 1);
        $tel      = \htmlspecialchars($tel, ENT_QUOTES | ENT_HTML5);
        //        $price = number_format($price, 2, ',', ' ');
        $typeprice     = \htmlspecialchars($typeprice, ENT_QUOTES | ENT_HTML5);
        $typecondition = \htmlspecialchars($typecondition, ENT_QUOTES | ENT_HTML5);
        $submitter     = \htmlspecialchars($submitter, ENT_QUOTES | ENT_HTML5);
        $town          = \htmlspecialchars($town, ENT_QUOTES | ENT_HTML5);
        $country       = \htmlspecialchars($country, ENT_QUOTES | ENT_HTML5);
        $contactby     = \htmlspecialchars($contactby, ENT_QUOTES | ENT_HTML5);
        $premium       = \htmlspecialchars($premium, ENT_QUOTES | ENT_HTML5);

        echo '<form action="index.php" method="post">';
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
        echo "<table><tr class='head' border='1'>
            <td>" . _AM_ADSLIGHT_NUMANN . " </td><td>{$lid} &nbsp;&nbsp;&nbsp;&nbsp;   " . _AM_ADSLIGHT_ADDED_ON . " &nbsp;&nbsp;&nbsp;&nbsp; {$date2}</td>
            </tr><tr class='head' border='1'>
        <td>" . _AM_ADSLIGHT_SENDBY . " </td><td>{$submitter}</td>
            </tr><tr class='head' border='1'>
        <td>" . _AM_ADSLIGHT_EMAIL . " </td><td><input type=\"text\" name=\"email\" size=\"40\" value=\"{$email}\"></td>
            </tr><tr class='head' border='1'>
        <td>" . _AM_ADSLIGHT_TEL . " </td><td><input type=\"text\" name=\"tel\" size=\"50\" value=\"{$tel}\"></td>
            </tr><tr class='head' border='1'>
        <td>" . _AM_ADSLIGHT_TOWN . " </td><td><input type=\"text\" name=\"town\" size=\"40\" value=\"{$town}\"></td>
            </tr><tr class='head' border='1'>
        <td>" . _AM_ADSLIGHT_COUNTRY . " </td><td><input type=\"text\" name=\"country\" size=\"40\" value=\"{$country}\"></td>
            </tr><tr class='head' border='1'>
        <td>" . _AM_ADSLIGHT_CONTACTBY . " </td><td><input type=\"text\" name=\"contactby\" size=\"40\" value=\"{$contactby}\"></td>
            </tr>";

        echo "<tr>
            <td class='head'>" . _AM_ADSLIGHT_STATUS . "</td><td class='head'><input type=\"radio\" name=\"status\" value=\"0\"";
        if (0 === (int)$status) {
            echo 'checked';
        }
        echo '>' . _AM_ADSLIGHT_ACTIVE . '&nbsp;&nbsp; <input type="radio" name="status" value="1"';
        if (1 === (int)$status) {
            echo 'checked';
        }
        echo '>' . _AM_ADSLIGHT_INACTIVE . '&nbsp;&nbsp; <input type="radio" name="status" value="2"';
        if (2 === (int)$status) {
            echo 'checked';
        }
        echo '>' . _AM_ADSLIGHT_SOLD . '</td></tr>';

        echo "<tr class='head' border='1'>
           <td>" . _AM_ADSLIGHT_TITLE2 . " </td><td><input type=\"text\" name=\"title\" size=\"40\" value=\"{$title}\"></td>
            </tr><tr class='head' border='1'>
           <td>" . _AM_ADSLIGHT_PREMIUM . " </td><td><input type=\"text\" name=\"premium\" size=\"3\" value=\"{$premium}\"></td>
            </tr><tr class='head' border='1'>
           <td>" . _AM_ADSLIGHT_EXPIRE . " </td><td><input type=\"text\" name=\"expire\" size=\"40\" value=\"{$expire}\"></td>
            </tr><tr class='head' border='1'>
            <td>" . _AM_ADSLIGHT_TYPE . ' </td><td><select name="type">';

        $sql     = 'SELECT nom_type FROM ' . $xoopsDB->prefix('adslight_type') . ' ORDER BY nom_type';
        $result5 = $xoopsDB->query($sql);
        while ([$nom_type] = $xoopsDB->fetchRow($result5)) {
            $sel = '';
            if ($nom_type === $type) {
                $sel = 'selected';
            }
            echo "<option value=\"{$nom_type}\"{$sel}>{$nom_type}</option>";
        }

        echo '</select></td></tr>';

        ////// Condition
        echo "<tr class='head' border='1'>
            <td>" . _AM_ADSLIGHT_TYPE_CONDITION . ' </td><td><select name="typecondition">';

        $sql     = 'SELECT nom_condition FROM ' . $xoopsDB->prefix('adslight_condition') . ' ORDER BY nom_condition';
        $result6 = $xoopsDB->query($sql);
        while ([$nom_condition] = $xoopsDB->fetchRow($result6)) {
            $sel = '';
            if ($nom_condition === $typecondition) {
                $sel = 'selected';
            }
            echo "<option value=\"{$nom_condition}\"{$sel}>{$nom_condition}</option>";
        }
        echo '</select></td></tr>';

        echo "<tr class='head' border='1'><td>" . _AM_ADSLIGHT_PRICE2 . " </td><td><input type=\"text\" name=\"price\" size=\"20\" value=\"${price}\"> " . $helper->getConfig('adslight_currency_symbol') . '';
        $sql     = 'SELECT nom_price FROM ' . $xoopsDB->prefix('adslight_price') . ' ORDER BY id_price';
        $result3 = $xoopsDB->query($sql);
        echo " <select name=\"typeprice\"><option value=\"{$typeprice}\">{$typeprice}</option>";
        while ([$nom_price] = $xoopsDB->fetchRow($result3)) {
            echo "<option value=\"${nom_price}\">${nom_price}</option>";
        }
        echo '</select></td></tr>';

        echo "<tr class='head' border='1'>
           <td>" . _AM_ADSLIGHT_PHOTO1 . " </td><td><input type=\"text\" name=\"photo\" size=\"40\" value=\"{$photo}\"></td>
            </tr>";
        echo "<tr class='head' border='1'><td>" . _AM_ADSLIGHT_DESC . '</td><td>';
        $wysiwyg_text_area = Utility::getEditor(_AM_ADSLIGHT_DESC);
        echo $wysiwyg_text_area->render();
        echo '</td></tr>';
        echo "<tr class='head' border='1'><td>" . _AM_ADSLIGHT_CAT . ' </td><td>';
        $mytree->makeMySelBox('title', 'title', $cid);
        echo "</td>
        </tr><tr class='head' border='1'>
        <td>&nbsp;</td><td><select name=\"op\">
        <option value=\"listingValid\"> " . _AM_ADSLIGHT_OK . '
        <option value="listingDel"> ' . _AM_ADSLIGHT_DEL . '
        </select><input type="submit" value="' . _AM_ADSLIGHT_GO . '"></td>
        </tr></table>';
        echo '<input type="hidden" name="valid" value="Yes">';
        echo "<input type=\"hidden\" name=\"lid\" value=\"{$lid}\">";
        echo "<input type=\"hidden\" name=\"date_created\" value=\"{$date_created}\">";
        echo "<input type=\"hidden\" name=\"submitter\" value=\"{$submitter}\">
            </form>";

        echo '</td></tr></table>';
        echo '<br>';
    }

    require_once __DIR__ . '/admin_footer.php';
}

#  function modifyAds
#####################################################
/**
 * @param $lid
 */
function modifyAds($lid): void
{
    global $xoopsDB, $myts, $desctext, $admin_lang;
    $helper = Helper::getInstance();
    $mytree = new Tree($xoopsDB->prefix('adslight_categories'), 'cid', 'pid');

    //    require_once __DIR__ . '/admin_header.php';
    xoops_cp_header();
    //    loadModuleAdminMenu(0, '');

    $lid           = (int)$lid;
    $id_price      = '';
    $nom_price     = '';
    $contactselect = '';

    echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_ADSLIGHT_MODANN . '</legend>';

    $sql    = 'SELECT lid, cid, title, status, expire, type, desctext, tel, price, typeprice, typecondition, date_created, email, submitter, town, country, contactby, premium, valid, photo FROM ' . $xoopsDB->prefix('adslight_listing') . " WHERE lid={$lid}";
    $result = $xoopsDB->query($sql);
    while ([$lid, $cid, $title, $status, $expire, $type, $desctext, $tel, $price, $typeprice, $typecondition, $date_created, $email, $submitter, $town, $country, $contactby, $premium, $valid, $photo] = $xoopsDB->fetchRow(
        $result
    )) {
        $title = \htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);
        //        $status    = \htmlspecialchars($status);
        $status   = (int)$status;
        $expire   = \htmlspecialchars($expire, ENT_QUOTES | ENT_HTML5);
        $type     = \htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);
        $desctext = $myts->displayTarea($desctext, 1, 1, 1);
        $tel      = \htmlspecialchars($tel, ENT_QUOTES | ENT_HTML5);
        //        $price     = number_format($price, 2, ',', ' ');
        $typeprice     = \htmlspecialchars($typeprice, ENT_QUOTES | ENT_HTML5);
        $typecondition = \htmlspecialchars($typecondition, ENT_QUOTES | ENT_HTML5);
        $submitter     = \htmlspecialchars($submitter, ENT_QUOTES | ENT_HTML5);
        $town          = \htmlspecialchars($town, ENT_QUOTES | ENT_HTML5);
        $country       = \htmlspecialchars($country, ENT_QUOTES | ENT_HTML5);
        $contactby     = \htmlspecialchars($contactby, ENT_QUOTES | ENT_HTML5);
        $premium       = \htmlspecialchars($premium, ENT_QUOTES | ENT_HTML5);

        $date2 = formatTimestamp($date_created, 's');

        echo '<form action="view_ads.php" method="post">';
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
        echo "<table border=0><tr class='head' border='1'>
           <td>" . _AM_ADSLIGHT_NUMANN . " </td><td>{$lid} &nbsp;" . _AM_ADSLIGHT_ADDED_ON . "&nbsp; {$date2}</td>
            </tr><tr class='head' border='1'>
           <td>" . _AM_ADSLIGHT_SENDBY . " </td><td>{$submitter}</td>
            </tr><tr class='head' border='1'>
           <td>" . _AM_ADSLIGHT_EMAIL . " </td><td><input type=\"text\" name=\"email\" size=\"40\" value=\"{$email}\"></td>
            </tr><tr class='head' border='1'>
           <td>" . _AM_ADSLIGHT_TEL . " </td><td><input type=\"text\" name=\"tel\" size=\"50\" value=\"{$tel}\"></td>
            </tr><tr class='head' border='1'>
           <td>" . _AM_ADSLIGHT_TOWN . " </td><td><input type=\"text\" name=\"town\" size=\"40\" value=\"{$town}\"></td>
            </tr><tr class='head' border='1'>
           <td>" . _AM_ADSLIGHT_COUNTRY . " </td><td><input type=\"text\" name=\"country\" size=\"40\" value=\"{$country}\"></td>
            </tr>
            <tr class='head' border='1'>";

        $contactSel1 = $contactSel2 = $contactSel3 = $contactSel4 = '';
        $selected    = 'contactSel' . (int)$contactby;
        ${$selected} = ' selected';

        echo " <td class='head'>"
             . _AM_ADSLIGHT_CONTACTBY
             . " </td><td class='head'><select name=\"contactby\">\n"
             //           . "  <option value=\"{$contactby}\">{$contactselect}</option>\n"
             . "  <option value=\"1\"{$contactSel1}>"
             . _AM_ADSLIGHT_CONTACT_BY_EMAIL
             . "</option>\n"
             . "  <option value=\"2\"{$contactSel2}>"
             . _AM_ADSLIGHT_CONTACT_BY_PM
             . "</option>\n"
             . "  <option value=\"3\"{$contactSel3}>"
             . _AM_ADSLIGHT_CONTACT_BY_BOTH
             . "</option>\n"
             . "  <option value=\"4\"{$contactSel4}>"
             . _AM_ADSLIGHT_CONTACT_BY_PHONE
             . "</option></select>\n"
             . " </td>\n"
             . '</tr>';

        echo "<tr><td class='head'>" . _AM_ADSLIGHT_STATUS . "</td><td class='head'><input type=\"radio\" name=\"status\" value=\"0\"";
        if (0 === $status) {
            echo 'checked';
        }
        echo '>' . _AM_ADSLIGHT_ACTIVE . '&nbsp;&nbsp; <input type="radio" name="status" value="1"';
        if (1 === $status) {
            echo 'checked';
        }
        echo '>' . _AM_ADSLIGHT_INACTIVE . '&nbsp;&nbsp; <input type="radio" name="status" value="2"';
        if (2 === $status) {
            echo 'checked';
        }
        echo '>' . _AM_ADSLIGHT_SOLD . '</td></tr>';

        echo "<tr class='head' border='1'>
           <td>" . _AM_ADSLIGHT_TITLE2 . " </td><td><input type=\"text\" name=\"title\" size=\"40\" value=\"{$title}\"></td>
            </tr><tr class='head' border='1'>
           <td>" . _AM_ADSLIGHT_PREMIUM . " </td><td><input type=\"text\" name=\"premium\" size=\"3\" value=\"{$premium}\"></td>
            </tr><tr class='head' border='1'>
           <td>" . _AM_ADSLIGHT_EXPIRE . " </td><td><input type=\"text\" name=\"expire\" size=\"40\" value=\"{$expire}\"></td>
            </tr>";
        // Type d'annonce
        echo "<tr class='head' border='1'>
            <td>" . _AM_ADSLIGHT_TYPE . ' </td><td><select name="type">';

        $sql     = 'SELECT nom_type, id_type FROM ' . $xoopsDB->prefix('adslight_type') . ' ORDER BY nom_type';
        $result5 = $xoopsDB->query($sql);
        while ([$nom_type, $id_type] = $xoopsDB->fetchRow($result5)) {
            $sel = '';
            if ($id_type === $type) {
                $sel = 'selected';
            }
            echo "<option value=\"{$id_type}\"{$sel}>{$nom_type}</option>";
        }
        echo '</select></td></tr>';

        // Condition
        echo "<tr class='head' border='1'>
            <td>" . _AM_ADSLIGHT_TYPE_CONDITION . ' </td><td><select name="typecondition">';

        $sql     = 'SELECT nom_condition, id_condition FROM ' . $xoopsDB->prefix('adslight_condition') . ' ORDER BY nom_condition';
        $result6 = $xoopsDB->query($sql);
        while ([$nom_condition, $id_condition] = $xoopsDB->fetchRow($result6)) {
            $sel = '';
            if ($id_condition === $typecondition) {
                $sel = 'selected';
            }
            echo "<option value=\"{$id_condition}\"{$sel}>{$nom_condition}</option>";
        }
        echo '</select></td></tr>';

        /////// Price
        echo "<tr class='head' border='1'><td>" . _AM_ADSLIGHT_PRICE2 . " </td><td><input type=\"text\" name=\"price\" size=\"20\" value=\"{$price}\"> {$helper->getConfig('adslight_currency_symbol')}";

        // Price type
        $sql     = 'SELECT nom_price, id_price FROM ' . $xoopsDB->prefix('adslight_price') . ' ORDER BY nom_price';
        $resultx = $xoopsDB->query($sql);

        echo " <select name=\"typeprice\"><option value=\"{$id_price}\">{$nom_price}</option>";
        while ([$nom_price, $id_price] = $xoopsDB->fetchRow($resultx)) {
            $sel = '';
            if ($id_price === $typeprice) {
                $sel = 'selected';
            }

            echo "<option value=\"{$id_price}\"{$sel}>{$nom_price}</option>";
        }
        echo '</select></td>';

        // Category
        echo "<tr class='head' border='1'>
            <td>" . _AM_ADSLIGHT_CAT2 . ' </td><td>';
        $mytree->makeMySelBox('title', 'title', $cid);
        echo "</td>
            </tr><tr class='head' border='1'>
            <td>" . _AM_ADSLIGHT_DESC . ' </td><td>';

        $wysiwyg_text_area = Utility::getEditor();
        echo $wysiwyg_text_area->render();

        echo '</td></tr>';

        echo "<tr class='head' border='1'>
            <td>" . _AM_ADSLIGHT_PHOTO1 . " </td><td><input type=\"text\" name=\"photo\" size=\"50\" value=\"{$photo}\"></td>
            </tr><tr>";
        $time = time();
        echo "</tr><tr class='head' border='1'>
            <td>&nbsp;</td><td><select name=\"op\">
            <option value=\"modifyAdsS\"> " . _AM_ADSLIGHT_MODIF . '
            <option value="listingDel"> ' . _AM_ADSLIGHT_DEL . '
            </select><input type="submit" value="' . _AM_ADSLIGHT_GO . '"></td>
            </tr></table>';
        echo '<input type="hidden" name="valid" value="Yes">';
        echo "<input type=\"hidden\" name=\"lid\" value=\"{$lid}\">";
        echo "<input type=\"hidden\" name=\"date_created\" value=\"{$time}\">";
        echo "<input type=\"hidden\" name=\"submitter\" value=\"{$submitter}\">
              </form><br>";
        echo '</fieldset><br>';
        xoops_cp_footer();
    }
}

#  function modifyAdsS
#####################################################

/**
 * @param string $lid
 * @param string $cat
 * @param string $title
 * @param string $status
 * @param string $expire
 * @param string $type
 * @param string $desctext
 * @param string $tel
 * @param string $price
 * @param string $typeprice
 * @param string $typecondition
 * @param string $date_created
 * @param string $email
 * @param string $submitter
 * @param string $town
 * @param string $country
 * @param string $contactby
 * @param string $premium
 * @param string $valid
 * @param string $photo
 */
function modifyAdsS(
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
    $valid,
    $photo
): void {
    global $xoopsDB, $myts, $admin_lang;

    $helper = Helper::getInstance();
    $lid    = (int)$lid;
    $cat    = (int)$cat;
    $title  = \htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);
    //    $status    = \htmlspecialchars($status);
    $status        = (int)$status;
    $expire        = \htmlspecialchars($expire, ENT_QUOTES | ENT_HTML5);
    $type          = \htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);
    $desctext      = $myts->displayTarea($desctext, 1, 1, 1);
    $tel           = \htmlspecialchars($tel, ENT_QUOTES | ENT_HTML5);
    $price         = str_replace([' '], '', $price);
    $typeprice     = \htmlspecialchars($typeprice, ENT_QUOTES | ENT_HTML5);
    $typecondition = \htmlspecialchars($typecondition, ENT_QUOTES | ENT_HTML5);
    $submitter     = \htmlspecialchars($submitter, ENT_QUOTES | ENT_HTML5);
    $town          = \htmlspecialchars($town, ENT_QUOTES | ENT_HTML5);
    $country       = \htmlspecialchars($country, ENT_QUOTES | ENT_HTML5);
    $contactby     = \htmlspecialchars($contactby, ENT_QUOTES | ENT_HTML5);
    $premium       = \htmlspecialchars($premium, ENT_QUOTES | ENT_HTML5);

    $xoopsDB->query(
        'UPDATE '
        . $xoopsDB->prefix('adslight_listing')
        . " SET cid='{$cat}', title='{$title}', status='{$status}', expire='{$expire}', type='{$type}', desctext='{$desctext}', tel='{$tel}', price='{$price}', typeprice='{$typeprice}', typecondition='{$typecondition}', date_created='{$date_created}', email='{$email}', submitter='{$submitter}', town='{$town}', country='{$country}', contactby='{$contactby}', premium='{$premium}', valid='{$valid}', photo='{$photo}' WHERE lid={$lid}"
    );

    $helper->redirect('admin/view_ads.php', 1, _AM_ADSLIGHT_ANNMOD);
}

#  function listingDel
#####################################################
/**
 * @param $lid
 * @param $photo
 */
function listingDel($lid, $photo): void
{
    global $xoopsDB, $admin_lang;
    $helper = Helper::getInstance();

    $lid     = (int)$lid;
    $sql     = 'SELECT p.url FROM ' . $xoopsDB->prefix('adslight_listing') . ' l LEFT JOIN ' . $xoopsDB->prefix('adslight_pictures') . " p  ON l.lid=p.lid WHERE l.lid={$lid}";
    $result2 = $xoopsDB->query($sql);

    while ([$purl] = $xoopsDB->fetchRow($result2)) {
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
            $sql = 'DELETE FROM ' . $xoopsDB->prefix('adslight_pictures') . " WHERE lid={$lid}";
            $xoopsDB->query($sql);
        }
    }

    $sql = 'DELETE FROM ' . $xoopsDB->prefix('adslight_listing') . " WHERE lid={$lid}";
    $xoopsDB->query($sql);
    $helper->redirect('admin/view_ads.php', 1, _AM_ADSLIGHT_ANNDEL);
}

#  function listingValid
#####################################################
/**
 * @param string $lid
 * @param string $cid
 * @param string $title
 * @param string $status
 * @param string $expire
 * @param string $type
 * @param string $desctext
 * @param string $tel
 * @param string $price
 * @param string $typeprice
 * @param string $typecondition
 * @param string $date_created
 * @param string $email
 * @param string $submitter
 * @param string $town
 * @param string $country
 * @param string $contactby
 * @param string $premium
 * @param string $valid
 * @param string $photo
 */
function listingValid(
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
    $valid,
    $photo
): void {
    global $xoopsDB, $xoopsConfig, $xoopsModule, $myts, $meta, $admin_lang;
    $helper        = Helper::getInstance();
    $lid           = (int)$lid;
    $cid           = (int)$cid;
    $title         = \htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);
    $status        = (int)$status;
    $expire        = \htmlspecialchars($expire, ENT_QUOTES | ENT_HTML5);
    $type          = \htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);
    $desctext      = $myts->displayTarea($desctext, 1, 1, 1);
    $tel           = \htmlspecialchars($tel, ENT_QUOTES | ENT_HTML5);
    $price         = str_replace([' '], '', $price);
    $typeprice     = \htmlspecialchars($typeprice, ENT_QUOTES | ENT_HTML5);
    $typecondition = \htmlspecialchars($typecondition, ENT_QUOTES | ENT_HTML5);
    $submitter     = \htmlspecialchars($submitter, ENT_QUOTES | ENT_HTML5);
    $town          = \htmlspecialchars($town, ENT_QUOTES | ENT_HTML5);
    $country       = \htmlspecialchars($country, ENT_QUOTES | ENT_HTML5);
    $contactby     = \htmlspecialchars($contactby, ENT_QUOTES | ENT_HTML5);
    $premium       = \htmlspecialchars($premium, ENT_QUOTES | ENT_HTML5);
    $valid         = \htmlspecialchars($valid, ENT_QUOTES | ENT_HTML5);
    $photo         = \htmlspecialchars($photo, ENT_QUOTES | ENT_HTML5);
    $now           = time();
    $xoopsDB->query(
        'UPDATE '
        . $xoopsDB->prefix('adslight_listing')
        . " SET cid='{$cat}', title='{$title}', status='{$status}', expire='{$expire}', type='{$type}', desctext='{$desctext}', tel='{$tel}', price='{$price}', typeprice='{$typeprice}', typecondition='{$typecondition}', date_created='{$now}', email='{$email}', submitter='{$submitter}', town='{$town}', country='{$country}', contactby='{$contactby}', premium='{$premium}', valid='{$valid}', photo='{$photo}' WHERE lid={$lid}"
    );

    if ('' !== $email) {
        $tags               = [];
        $tags['TITLE']      = $title;
        $tags['TYPE']       = Utility::getNameType($type);
        $tags['SUBMITTER']  = $submitter;
        $tags['DESCTEXT']   = stripslashes($desctext);
        $tags['EMAIL']      = _AM_ADSLIGHT_EMAIL;
        $tags['TEL']        = _AM_ADSLIGHT_TEL;
        $tags['HELLO']      = _AM_ADSLIGHT_HELLO;
        $tags['VEDIT_AD']   = _AM_ADSLIGHT_VEDIT_AD;
        $tags['ANNACCEPT']  = _AM_ADSLIGHT_ANNACCEPT;
        $tags['CONSULTTO']  = _AM_ADSLIGHT_CONSULTTO;
        $tags['THANKS']     = _ADSLIGHT_THANKS;
        $tags['TEAMOF']     = _AM_ADSLIGHT_TEAMOF;
        $tags['META_TITLE'] = $meta['title'];
        $tags['LINK_URL']   = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . "/viewads.php?lid={$lid}";
        $tags['YOUR_AD']    = _AM_ADSLIGHT_YOUR_AD;
        $tags['WEBMASTER']  = _AM_ADSLIGHT_WEBMASTER;
        $tags['YOUR_AD_ON'] = _AM_ADSLIGHT_YOUR_AD_ON;
        $tags['APPROVED']   = _AM_ADSLIGHT_APPROVED;

        $subject = _AM_ADSLIGHT_ANNACCEPT;
        $mail    = getMailer();
        $mail->setTemplateDir(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . "/language/{$xoopsConfig['language']}/mail_template/");
        $mail->setTemplate('listing_approve.tpl');
        $mail->useMail();
        $mail->multimailer->isHTML(true);
        $mail->setFromName($meta['title']);
        $mail->setFromEmail($xoopsConfig['adminmail']);
        $mail->setToEmails($email);
        $mail->setSubject($subject);
        $mail->assign($tags);
        $mail->send();
        echo $mail->getErrors();
    }

    $tags                    = [];
    $tags['TITLE']           = $title;
    $tags['ADDED_TO_CAT']    = _AM_ADSLIGHT_ADDED_TO_CAT;
    $tags['RECIEVING_NOTIF'] = _AM_ADSLIGHT_RECIEVING_NOTIF;
    $tags['ERROR_NOTIF']     = _AM_ADSLIGHT_ERROR_NOTIF;
    $tags['WEBMASTER']       = _AM_ADSLIGHT_WEBMASTER;
    $tags['HELLO']           = _AM_ADSLIGHT_HELLO;
    $tags['FOLLOW_LINK']     = _AM_ADSLIGHT_FOLLOW_LINK;
    $tags['TYPE']            = Utility::getNameType($type);
    $tags['LINK_URL']        = XOOPS_URL . "/modules/adslight/viewads.php?&lid={$lid}";

    $sql                    = 'SELECT title FROM ' . $xoopsDB->prefix('adslight_categories') . " WHERE cid={$cat}";
    $result                 = $xoopsDB->query($sql);
    $row                    = $xoopsDB->fetchArray($result);
    $tags['CATEGORY_TITLE'] = $row['title'];
    $tags['CATEGORY_URL']   = XOOPS_URL . "/modules/adslight/viewcats.php?cid={$cat}";
    /** @var \XoopsNotificationHandler $notificationHandler */
    $notificationHandler = xoops_getHandler('notification');
    $notificationHandler->triggerEvent('global', 0, 'new_listing', $tags);
    $notificationHandler->triggerEvent('category', $cat, 'new_listing', $tags);
    $notificationHandler->triggerEvent('listing', $lid, 'new_listing', $tags);

    $helper->redirect('admin/view_ads.php', 3, _AM_ADSLIGHT_ANNVALID);
}

#####################################################
#####################################################

foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

//$pa  = Request::getString('pa', '', 'GET');
//$lid = Request::getInt('lid', 0);
//$op  = Request::getString('op', '');

$pa  = Request::getInt('pa', '', 'GET');
$lid = 0;
if (!Request::hasVar('lid', 'POST') && Request::hasVar('lid', 'GET')) {
    $lid = Request::getInt('lid', 0, 'GET');
}

//if (!Request::hasVar('op', 'POST') && Request::hasVar('op', 'GET')) {
//    $op = Request::getString('op', '', 'GET');
//}
$op = Request::getString('op', '');

if (!isset($op)) {
    $op = '';
}

switch ($op) {
    case 'indexView':
        indexView($lid);
        break;
    case 'listingDel':
        listingDel($lid, $photo);
        break;
    case 'listingValid':
        listingValid(
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
            $valid,
            $photo
        );
        break;
    case 'modifyAds':
        modifyAds($lid);
        break;
    case 'modifyAdsS':
        modifyAdsS(
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
            $valid,
            $photo
        );
        break;
    default:
        index();
        break;
}
