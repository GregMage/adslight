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

use Xmf\Module\Helper\Permission;
use Xmf\Request;
use XoopsModules\Adslight\{
    Helper,
    Tree,
    Utility
};

/** @var Helper $helper */

$GLOBALS['xoopsOption']['template_main'] = 'adslight_addlisting.tpl';
require_once __DIR__ . '/header.php';

global $xoopsModule, $xoopsDB, $xoopsConfig;

$myts = \MyTextSanitizer::getInstance(); // MyTextSanitizer object

$moduleId = $xoopsModule->getVar('mid');
$groups   = $GLOBALS['xoopsUser'] instanceof XoopsUser ? $GLOBALS['xoopsUser']->getGroups() : XOOPS_GROUP_ANONYMOUS;
/** @var \XoopsGroupPermHandler $grouppermHandler */
$grouppermHandler = xoops_getHandler('groupperm');

$permHelper = new Permission();

$perm_itemid = Request::getInt('item_id', 0, 'POST');

if (!$grouppermHandler->checkRight('adslight_submit', $perm_itemid, $groups, $moduleId)) {
    redirect_header(XOOPS_URL . '/index.php', 3, _NOPERM);
}

$premium = $grouppermHandler->checkRight('adslight_premium', $perm_itemid, $groups, $moduleId) ? 1 : 0;

require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

$mytree = new Tree($xoopsDB->prefix('adslight_categories'), 'cid', 'pid');

if (!$GLOBALS['xoopsUser'] instanceof \XoopsUser) {
    redirect_header(XOOPS_URL . '/user.php', 2, _MA_ADSLIGHT_MUSTREGFIRST);
}

if (Request::hasVar('submit', 'POST')) {
    $howlong = $helper->getConfig('adslight_howlong');

    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header(XOOPS_URL . '/', 3, $GLOBALS['xoopsSecurity']->getErrors());
    }

    //    xoops_load("xoopscaptcha");
    //    $xoopsCaptcha = XoopsCaptcha::getInstance();
    //    if ( !$xoopsCaptcha->verify() ) {
    //        redirect_header( XOOPS_URL . "/modules/adslight/index.php", 2, $xoopsCaptcha->getMessage() );
    //    }
    if (Request::hasVar('submit', 'POST')) {
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        /** @var \XoopsModule $myModule */
        $myModule = $moduleHandler->getByDirname('adslight');
        $myModule->setErrors('Could not connect to the database.');
    }

    $cid       = Request::getInt('cid', 0, 'POST');
    $cat_perms = Utility::getMyItemIds('adslight_submit');
    if (!\in_array((int)$cid, $cat_perms, true)) {
        redirect_header(XOOPS_URL, 2, _NOPERM);
    }

    $title         = Request::getString('title', '', 'POST');
    $status        = Request::getInt('status', 0, 'POST');
    $expire        = Request::getString('expire', '', 'POST');
    $type          = Request::getString('type', '', 'POST');
    $desctext      = Request::getText('desctext', '', 'POST'); // $myts->displayTarea($_POST['desctext'], 1, 1, 1);
    $tel           = Request::getString('tel', '', 'POST');
    $price         = Request::getFloat('price', 0, 'POST');
    $typeprice     = Request::getString('typeprice', '', 'POST');
    $typecondition = Request::getString('typecondition', '', 'POST');
    //$date_created  = Request::getInt('date_created', 0, 'POST');
    $email        = Request::getString('email', '', 'POST');
    $submitter    = Request::getString('submitter', '', 'POST');
    $usid         = Request::getString('usid', '', 'POST');
    $town         = Request::getString('town', '', 'POST');
    $country      = Request::getString('country', '', 'POST');
    $contactby    = Request::getString('contactby', '', 'POST');
    $premium      = Request::getString('premium', '', 'POST');
    $valid        = Request::getString('valid', '', 'POST');
    $date_created = time();
    $newid        = $xoopsDB->genId($xoopsDB->prefix('adslight_listing') . '_lid_seq');

    $sql     = sprintf(
        "INSERT INTO `%s` (lid, cid, title, STATUS, EXPIRE, type, desctext, tel, price, typeprice, typecondition, date_created, email, submitter, usid, town, country, contactby, premium, valid) VALUES (%u, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
        $xoopsDB->prefix('adslight_listing'),
        $newid,
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
        $usid,
        $town,
        $country,
        $contactby,
        $premium,
        $valid
    );
    $success = $xoopsDB->query($sql);
    if (!$success) {
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        /** @var XoopsModule $myModule */
        $myModule = $moduleHandler->getByDirname('adslight');
        $myModule->setErrors('Could not query the database.');
    }

    $lid = $xoopsDB->getInsertId();

    if ('Yes' === $valid) {
        /** @var \XoopsNotificationHandler $notificationHandler */
        $notificationHandler = xoops_getHandler('notification');
        //$lid = $xoopsDB->getInsertId();
        $tags                    = [];
        $tags['TITLE']           = $title;
        $tags['ADDED_TO_CAT']    = _ADSLIGHT_ADDED_TO_CAT;
        $tags['RECIEVING_NOTIF'] = _ADSLIGHT_RECIEVING_NOTIF;
        $tags['ERROR_NOTIF']     = _ADSLIGHT_ERROR_NOTIF;
        $tags['WEBMASTER']       = _ADSLIGHT_WEBMASTER;
        $tags['HELLO']           = _ADSLIGHT_HELLO;
        $tags['FOLLOW_LINK']     = _ADSLIGHT_FOLLOW_LINK;
        $tags['TYPE']            = Utility::getNameType($type);
        $tags['LINK_URL']        = XOOPS_URL . '/modules/adslight/viewads.php?' . '&lid=' . $lid;
        $sql                     = 'SELECT title FROM ' . $xoopsDB->prefix('adslight_categories') . ' WHERE cid=' . $cid;
        $result2 = $xoopsDB->query($sql);
        if ($result2 instanceof \mysqli_result) {
            $row = $xoopsDB->fetchArray($result2);
            $tags['CATEGORY_TITLE'] = $row['title'];
        }
        $tags['CATEGORY_URL']   = XOOPS_URL . '/modules/adslight/viewcats.php?cid="' . addslashes((string)$cid);
        /** @var \XoopsNotificationHandler $notificationHandler */
        $notificationHandler = xoops_getHandler('notification');
        $notificationHandler->triggerEvent('global', 0, 'new_listing', $tags);
        $notificationHandler->triggerEvent('category', $cid, 'new_listing', $tags);
        $notificationHandler->triggerEvent('listing', $lid, 'new_listing', $tags);
    } else {
        $tags                  = [];
        $subject               = '' . _ADSLIGHT_NEW_WAITING_SUBJECT . '';
        $tags['TITLE']         = $title;
        $tags['DESCTEXT']      = $desctext;
        $tags['ADMIN']         = _ADSLIGHT_ADMIN;
        $tags['NEW_WAITING']   = _ADSLIGHT_NEW_WAITING;
        $tags['PLEASE_CHECK']  = _ADSLIGHT_PLEASE_CHECK;
        $tags['WEBMASTER']     = _ADSLIGHT_WEBMASTER;
        $tags['HELLO']         = _ADSLIGHT_HELLO;
        $tags['FOLLOW_LINK']   = _ADSLIGHT_FOLLOW_LINK;
        $tags['TYPE']          = Utility::getNameType($type);
        $tags['NEED_TO_LOGIN'] = _ADSLIGHT_NEED_TO_LOGIN;
        $tags['ADMIN_LINK']    = XOOPS_URL . '/modules/adslight/admin/validate_ads.php';
        $sql                   = 'SELECT title FROM ' . $xoopsDB->prefix('adslight_categories') . ' WHERE cid=' . $cid;
        $result2                = $xoopsDB->query($sql);
        if ($result2 instanceof \mysqli_result) {
            $row = $xoopsDB->fetchArray($result2);
            $tags['CATEGORY_TITLE'] = $row['title'];
        }
        $tags['NEWAD']          = _ADSLIGHT_NEWAD;

        $mail = xoops_getMailer();
        //@todo - add check to see if directory (and file) exists, otherwise use english
        $mail->setTemplateDir(XOOPS_ROOT_PATH . '/modules/adslight/language/' . $xoopsConfig['language'] . '/mail_template/');
        $mail->setTemplate('listing_notify_admin.tpl');
        $mail->useMail();
        $mail->multimailer->isHTML(true);
        $mail->setFromName($xoopsConfig['sitename']);
        $mail->setFromEmail($xoopsConfig['adminmail']);
        $mail->setToEmails($xoopsConfig['adminmail']);
        $mail->setSubject($subject);
        $mail->assign($tags);
        $mail->send();
        echo $mail->getErrors();
    }

    $addphotonow = Request::getInt('addphotonow', 0, 'POST');
    if ($addphotonow) {
        //$lid = $xoopsDB->getInsertId();
        $helper->redirect("view_photos.php?lid=${lid}&uid=${usid}", 3, _ADSLIGHT_ADSADDED);
    } else {
        $helper->redirect('index.php', 3, _ADSLIGHT_ADSADDED);
    }
} else {
    $GLOBALS['xoopsOption']['template_main'] = 'adslight_addlisting.tpl';
    require_once XOOPS_ROOT_PATH . '/header.php';
    //    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    $cid          = Request::getInt('cide', 0, 'GET');
    $cat_moderate = Request::getInt('cat_moderate', 0, 'POST');
    $howlong      = $helper->getConfig('adslight_howlong');
    $member_usid  = $GLOBALS['xoopsUser']->getVar('uid', 'E');
    $member_email = $GLOBALS['xoopsUser']->getVar('email', 'E');
    $member_uname = $GLOBALS['xoopsUser']->getVar('uname', 'E');

    $sql     = 'SELECT id_type, nom_type FROM ' . $xoopsDB->prefix('adslight_type') . ' ORDER BY nom_type';
    $result  = $xoopsDB->query($sql);
    $sql2    = 'SELECT id_price, nom_price FROM ' . $xoopsDB->prefix('adslight_price') . ' ORDER BY id_price';
    $result2 = $xoopsDB->query($sql2);
    $sql3    = 'SELECT id_condition, nom_condition FROM ' . $xoopsDB->prefix('adslight_condition') . ' ORDER BY id_condition';
    $result3 = $xoopsDB->query($sql3);

    ob_start();
    $form = new \XoopsThemeForm(_ADSLIGHT_ADD_LISTING, 'submitform', 'add.php', 'post', true);
    $form->setExtra('enctype="multipart/form-data"');
    //    $GLOBALS['xoopsGTicket']->addTicketXoopsFormElement($form, __LINE__, 1800, 'token');
    //@todo - this 'if' code doesn't do anything, what should happen for premium accounts?
    if ($cat_moderate) {
        if (0 !== $premium) {
            echo '';
        } else {
            echo '';
        }
    } elseif (0 !== $premium) {
        echo '';
    } else {
        echo '';
    }

    if ('1' === $helper->getConfig('adslight_diff_name')) {
        $form->addElement(new \XoopsFormText(_ADSLIGHT_SUBMITTER, 'submitter', 50, 50, $member_uname), true);
    } else {
        $form->addElement(new \XoopsFormLabel(_ADSLIGHT_SUBMITTER, $member_uname));
        $form->addElement(new \XoopsFormHidden('submitter', $member_uname), true);
    }
    if ('1' === $helper->getConfig('adslight_diff_email')) {
        $form->addElement(new \XoopsFormText(_ADSLIGHT_EMAIL, 'email', 50, 50, $member_email), true);
    } else {
        $form->addElement(new \XoopsFormLabel(_ADSLIGHT_EMAIL, $member_email));
        $form->addElement(new \XoopsFormHidden('email', $member_email), true);
    }
    $form->addElement(new \XoopsFormText(_ADSLIGHT_TOWN, 'town', 50, 50, ''), false);
    if ('1' === $helper->getConfig('adslight_use_country')) {
        $form->addElement(new \XoopsFormText(_ADSLIGHT_COUNTRY, 'country', 50, 50, ''), false);
    } else {
        $form->addElement(new \XoopsFormHidden('country', ''), false);
    }
    $form->addElement(new \XoopsFormText(_ADSLIGHT_TEL, 'tel', 50, 50, ''), false);

    //     $cid = $_GET['cid'];
    $cid       = 0;
    $cat_perms = Utility::getMyItemIds('adslight_submit');
    if (is_array($cat_perms) && $cat_perms !== []) {
        if (!\in_array($cid, $cat_perms, true)) {
            //mb            $helper->redirect('index.php', 3, _NOPERM);
        }

        // Category select box
        ob_start();
        $mytree->makeMySelBox('title', 'title', $cid, 1, 'cid');
        $form->addElement(new \XoopsFormLabel(_ADSLIGHT_CAT3, (string)ob_get_clean())??'', true);
        $sql = 'SELECT title, cat_moderate FROM ' . $xoopsDB->prefix('adslight_categories') . " WHERE cid='" . $xoopsDB->escape($cid) . "'";
        $category = $xoopsDB->query($sql);
        if ($category instanceof \mysqli_result) {
            [$cat_title, $cat_moderate] = $xoopsDB->fetchRow($category);
        }
        if (1 === (int)$premium) {
            $radio        = new \XoopsFormRadio(_ADSLIGHT_STATUS, 'status', '');
            $options['0'] = _ADSLIGHT_ACTIVE;
            $options['1'] = _ADSLIGHT_INACTIVE;
            $radio->addOptionArray($options);
            $form->addElement($radio, true);
        } else {
            $form->addElement(new \XoopsFormHidden('status', '0'), true);
        }
        if (1 === $premium) {
            $form->addElement(new \XoopsFormText(_ADSLIGHT_HOW_LONG, 'expire', 3, 3, $helper->getConfig('adslight_howlong')), true);
        } else {
            $form->addElement(new \XoopsFormLabel(_ADSLIGHT_WILL_LAST, $helper->getConfig('adslight_howlong')));
            $form->addElement(new \XoopsFormHidden('expire', $helper->getConfig('adslight_howlong')), false);
        }

        // Type
        $type_form = new \XoopsFormSelect(_ADSLIGHT_TYPE, 'type', '', 1);
        while ([$nom_type, $id_type] = $xoopsDB->fetchRow($result)) {
            $type_form->addOption($nom_type, $id_type);
        }

        // Item Condition
        $condition_form = new \XoopsFormSelect(_ADSLIGHT_TYPE_CONDITION, 'typecondition', '', 1);
        while (false !== [$nom_condition, $id_condition] = $xoopsDB->fetchRow($result3)) {
            $condition_form->addOption($nom_condition, $id_condition);
        }

        $form->addElement($type_form, true);
        $form->addElement($condition_form, true);

        $form->addElement(new \XoopsFormText(_ADSLIGHT_TITLE2, 'title', 40, 50, ''), true);

        $form->addElement(Utility::getEditor($helper), true);

        //        $form->addElement(new \XoopsFormEditor(_ADSLIGHT_DESC, $helper->getConfig('adslightEditorUser'), $options, $nohtml = FALSE, $onfailure = 'textarea'));
        //        $optionsTrayNote->addElement($bodynote);

        $form->addElement(new \XoopsFormText(_ADSLIGHT_PRICE2, 'price', 40, 50, ''), true);

        // Price Type
        $sel_form = new \XoopsFormSelect(_ADSLIGHT_PRICETYPE, 'typeprice', '', 1);
        while ([$nom_price, $id_price] = $xoopsDB->fetchRow($result2)) {
            $sel_form->addOption($nom_price, $id_price);
        }

        $form->addElement($sel_form);

        $contactby_form = new \XoopsFormSelect(_ADSLIGHT_CONTACTBY, 'contactby', '', 1);
        $contactby_form->addOption('1', _ADSLIGHT_CONTACT_BY_EMAIL);
        $contactby_form->addOption('2', _ADSLIGHT_CONTACT_BY_PM);
        $contactby_form->addOption('3', _ADSLIGHT_CONTACT_BY_BOTH);
        $contactby_form->addOption('4', _ADSLIGHT_CONTACT_BY_PHONE);
        $form->addElement($contactby_form, true);
        $form->addElement(new \XoopsFormRadioYN(_ADSLIGHT_ADD_PHOTO_NOW, 'addphotonow', _YES));
        /*
                if ('1' == $helper->getConfig("adslight_use_captcha")) {
                    $form->addElement(new \XoopsFormCaptcha(_ADSLIGHT_CAPTCHA, "xoopscaptcha", false), true);
                }
        */
        if (0 !== (int)$premium) {
            $form->addElement(new \XoopsFormHidden('premium', 'yes'), false);
        } else {
            $form->addElement(new \XoopsFormHidden('premium', 'no'), false);
        }
        if ('1' === $cat_moderate) {
            $form->addElement(new \XoopsFormHidden('valid', 'No'), false);
            $form->addElement(new \XoopsFormHidden('cat_moderate', '1'), false);
        } else {
            $form->addElement(new \XoopsFormHidden('valid', 'Yes'), false);
        }
        $form->addElement(new \XoopsFormHidden('usid', $member_usid), false);
        $form->addElement(new \XoopsFormHidden('date_created', (string)time()), false);
        $form->addElement(new \XoopsFormButton('', 'submit', _ADSLIGHT_SUBMIT, 'submit'));
        $form->display();
        $GLOBALS['xoopsTpl']->assign('submit_form', ob_get_clean());
    } else {    // User can't see any category
        redirect_header(XOOPS_URL . '/index.php', 3, _NOPERM);
    }
    require_once XOOPS_ROOT_PATH . '/footer.php';
}
