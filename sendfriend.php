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
    Utility
};

/** @var Helper $helper */

require __DIR__ . '/header.php';
/**
 * @param $lid
 */
function sendFriend($lid): void
{
    global $xoopsDB, $xoopsTheme, $xoopsLogger;
    $helper = Helper::getInstance();
    $idd = $idde = $iddee = '';
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    require_once XOOPS_ROOT_PATH . '/header.php';
    $GLOBALS['xoTheme']->addMeta('meta', 'robots', 'noindex, nofollow');

    $sql    = 'SELECT lid, title, type FROM ' . $xoopsDB->prefix('adslight_listing') . " WHERE lid={$lid}";
    $result = $xoopsDB->query($sql);
    [$lid, $title, $type] = $xoopsDB->fetchRow($result);

    echo "<table width='100%' border='0' cellspacing='1' cellpadding='8'><tr class='bg4'><td valign='top'>
        <strong>" . _ADSLIGHT_SENDTO . " ${lid} \"<strong>${type} : ${title}</strong>\" " . _ADSLIGHT_FRIEND . "<br><br>
        <form action=\"sendfriend.php\" method=post>
        <input type=\"hidden\" name=\"lid\" value=\"${lid}\" >";

    if ($GLOBALS['xoopsUser'] instanceof \XoopsUser) {
        $idd  = $GLOBALS['xoopsUser']->getVar('uname', 'E');
        $idde = $GLOBALS['xoopsUser']->getVar('email', 'E');
    }

    echo "
    <table width='99%' class='outer' cellspacing='1'>
    <tr>
      <td class='head' width='30%'>" . _ADSLIGHT_NAME . " </td>
      <td class='even'><input class='textbox' type='text' name='yname' value='${idd}' ></td>
    </tr>
    <tr>
      <td class='head'>" . _ADSLIGHT_MAIL . " </td>
      <td class='even'><input class='textbox' type='text' name='ymail' value='${idde}' ></td>
    </tr>
    <tr>
      <td class='head'>" . _ADSLIGHT_NAMEFR . " </td>
      <td class='even'><input class='textbox' type='text' name='fname' ></td>
    </tr>
    <tr>
      <td class='head'>" . _ADSLIGHT_MAILFR . " </td>
      <td class='even'><input class='textbox' type='text' name='fmail' ></td>
    </tr>";

    if ('1' === $helper->getConfig('adslight_use_captcha')) {
        echo "<tr><td class='head'>" . _ADSLIGHT_CAPTCHA . " </td><td class='even'>";
        $jlm_captcha = new \XoopsFormCaptcha(_ADSLIGHT_CAPTCHA, 'xoopscaptcha', false);
        echo $jlm_captcha->render();
        echo '</td></tr>';
    }

    echo '</table><br>
    <input type=hidden name=op value=MailAd>
    <input type=submit value=' . _ADSLIGHT_SENDFR . '>
    </form></td></tr></table>';
}

/**
 * @param $lid
 * @param $yname
 * @param $ymail
 * @param $fname
 * @param $fmail
 */
function mailAd($lid, $yname, $ymail, $fname, $fmail): void
{
    global $xoopsConfig, $xoopsTpl, $xoopsDB, $xoopsModule, $myts;
    $helper = Helper::getInstance();

    if ('1' === $helper->getConfig('adslight_use_captcha')) {
        xoops_load('xoopscaptcha');
        $xoopsCaptcha = XoopsCaptcha::getInstance();
        $helper       = Helper::getInstance();
        if (!$xoopsCaptcha->verify()) {
            $helper->redirect('index.php', 2, $xoopsCaptcha->getMessage());
        }
    }

    $sql    = 'SELECT lid, title, expire, type, desctext, tel, price, typeprice, date_created, email, submitter, town, country, photo FROM ' . $xoopsDB->prefix('adslight_listing') . ' WHERE lid=' . $xoopsDB->escape($lid);
    $result = $xoopsDB->query($sql);
    [$lid, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date_created, $email, $submitter, $town, $country, $photo] = $xoopsDB->fetchRow($result);

    $title     = $myts->addSlashes($title);
    $expire    = $myts->addSlashes($expire);
    $type      = $myts->addSlashes($type);
    $desctext  = $myts->displayTarea($desctext, 1, 1, 1, 1, 1);
    $tel       = $myts->addSlashes($tel);
    $price     = $myts->addSlashes($price);
    $typeprice = $myts->addSlashes($typeprice);
    $submitter = $myts->addSlashes($submitter);
    $town      = $myts->addSlashes($town);
    $country   = $myts->addSlashes($country);

    $tags                       = [];
    $tags['YNAME']              = stripslashes($yname);
    $tags['YMAIL']              = $ymail;
    $tags['FNAME']              = stripslashes($fname);
    $tags['FMAIL']              = $fmail;
    $tags['HELLO']              = _ADSLIGHT_HELLO;
    $tags['LID']                = $lid;
    $tags['LISTING_NUMBER']     = _ADSLIGHT_LISTING_NUMBER;
    $tags['TITLE']              = $title;
    $tags['TYPE']               = Utility::getNameType($type);
    $tags['DESCTEXT']           = $desctext;
    $tags['PRICE']              = $price;
    $tags['TYPEPRICE']          = $typeprice;
    $tags['TEL']                = $tel;
    $tags['TOWN']               = $town;
    $tags['COUNTRY']            = $country;
    $tags['OTHER']              = '' . _ADSLIGHT_INTERESS . '' . $xoopsConfig['sitename'] . '';
    $tags['LISTINGS']           = XOOPS_URL . '/modules/adslight/';
    $tags['LINK_URL']           = XOOPS_URL . '/modules/adslight/viewads.php?lid=' . $lid;
    $tags['THINKS_INTERESTING'] = _ADSLIGHT_MESSAGE;
    $tags['NO_MAIL']            = _ADSLIGHT_NOMAIL;
    $tags['YOU_CAN_VIEW_BELOW'] = _ADSLIGHT_YOU_CAN_VIEW_BELOW;
    $tags['WEBMASTER']          = _ADSLIGHT_WEBMASTER;
    $tags['NO_REPLY']           = _ADSLIGHT_NOREPLY;
    $subject                    = '' . _ADSLIGHT_SUBJET . ' ' . $xoopsConfig['sitename'] . '';
    $xoopsMailer                = xoops_getMailer();
    $xoopsMailer->multimailer->isHTML(true);
    $xoopsMailer->useMail();
    $xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/' . $xoopsConfig['language'] . '/mail_template/');
    $xoopsMailer->setTemplate('listing_send_friend.tpl');
    $xoopsMailer->setFromEmail($ymail);
    $xoopsMailer->setToEmails($fmail);
    $xoopsMailer->setSubject($subject);
    $xoopsMailer->assign($tags);
    $xoopsMailer->send();
    echo $xoopsMailer->getErrors();
    $helper->redirect('index.php', 3, _ADSLIGHT_ANNSEND);
}

##############################################################
$yname = Request::getString('yname', '', 'POST');
$ymail = Request::getString('ymail', '', 'POST');
$fname = Request::getString('fname', '', 'POST');
$fmail = Request::getString('fmail', '', 'POST');

$lid = Request::getInt('lid', 0);
$op  = Request::getString('op', '');

switch ($op) {
    case 'SendFriend':
        require_once XOOPS_ROOT_PATH . '/header.php';
        sendFriend($lid);
        require_once XOOPS_ROOT_PATH . '/footer.php';
        break;
    case 'MailAd':
        mailAd($lid, $yname, $ymail, $fname, $fmail);
        break;
    default:
        $helper->redirect('index.php', 1, ' ' . _RETURNANN . ' ');
        break;
}
