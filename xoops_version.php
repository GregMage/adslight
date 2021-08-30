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

require_once __DIR__ . '/preloads/autoloader.php';

$moduleDirName      = \basename(__DIR__);
$moduleDirNameUpper = mb_strtoupper($moduleDirName);
xoops_loadLanguage('common', $moduleDirName);
// Select Maps
$path = XOOPS_ROOT_PATH . '/modules/adslight/maps';
if ($handle = opendir($path)) {
    while (false !== ($file = readdir($handle))) {
        if (!is_dir($path . '.' . $file) && '.' !== $file && '..' !== $file
            && 'index.html' !== $file) {
            $maps_name                 = $file;
            $adslight_maps[$maps_name] = $file;
        }
    }
    closedir($handle);
}

// sql customized language file
global $xoopsConfig;
if (file_exists(XOOPS_ROOT_PATH . '/modules/adslight/sql/' . $xoopsConfig['language'] . '/mysql.sql')) {
    $adslight_sql = 'sql/' . $xoopsConfig['language'] . '/mysql.sql';
} else {
    $adslight_sql = 'sql/english/mysql.sql';
}

$modversion['version']             = '2.4';
$modversion['module_status']       = 'Alpha 3 NOT RELEASED';
$modversion['release_date']        = '2021/08/30';
$modversion['name']                = _MI_ADSLIGHT_NAME;
$modversion['description']         = _MI_ADSLIGHT_DESC;
$modversion['credits']             = 'AdsLight';
$modversion['author']              = 'Luc Bizet, Mamba';
$modversion['help']                = 'page=help';
$modversion['license']             = 'GPL';
$modversion['license_file']        = 'https://www.gnu.org/licenses/gpl.html';
$modversion['official']            = 0; //1 indicates supported by XOOPS Dev Team, 0 means 3rd party supported
$modversion['image']               = 'assets/images/logoModule.png';
$modversion['dirname']             = $moduleDirName;
$modversion['modicons16']          = 'assets/images/icons/16';
$modversion['modicons32']          = 'assets/images/icons/32';
$modversion['module_website_url']  = 'www.xoops.org';
$modversion['module_website_name'] = 'XOOPS';
$modversion['min_php']             = '7.4';
$modversion['min_xoops']           = '2.5.10';
$modversion['min_admin']           = '1.2';
$modversion['min_db']              = ['mysql' => '5.5'];

$modversion['sqlfile']['mysql'] = $adslight_sql;
$modversion['onInstall']        = 'include/oninstall.php';
$modversion['onUpdate']         = 'include/onupdate.php';

$modversion['release']           = '12-08-2021';
$modversion['support_site_url']  = 'http://#';
$modversion['support_site_name'] = 'AdsLight';

// Tables crée depuis le fichier sql
$modversion['tables'] = [
    $moduleDirName . '_' . 'listing',
    $moduleDirName . '_' . 'categories',
    $moduleDirName . '_' . 'type',
    $moduleDirName . '_' . 'price',
    $moduleDirName . '_' . 'condition',
    $moduleDirName . '_' . 'ip_log',
    $moduleDirName . '_' . 'item_votedata',
    $moduleDirName . '_' . 'user_votedata',
    $moduleDirName . '_' . 'pictures',
    $moduleDirName . '_' . 'replies',
];
// Pour l'administration
$modversion['hasAdmin']    = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex']  = 'admin/index.php';
$modversion['adminmenu']   = 'admin/menu.php';
// ------------------- Help files ------------------- //
$modversion['help']        = 'page=help';
$modversion['helpsection'] = [
    ['name' => _MI_ADSLIGHT_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_ADSLIGHT_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_ADSLIGHT_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_ADSLIGHT_SUPPORT, 'link' => 'page=support'],
];
// ------------------- Templates ------------------- //
$modversion['templates'] = [
    ['file' => 'adslight_index.tpl', 'description' => ''],
    ['file' => 'adslight_category.tpl', 'description' => ''],
    ['file' => 'adslight_item.tpl', 'description' => ''],
    ['file' => 'adslight_rate_item.tpl', 'description' => ''],
    ['file' => 'adslight_rate_user.tpl', 'description' => ''],
    ['file' => 'adslight_view_photos.tpl', 'description' => ''],
    ['file' => 'adslight_addlisting.tpl', 'description' => ''],
    ['file' => 'adslight_members.tpl', 'description' => ''],
    ['file' => 'adslight_replies.tpl', 'description' => ''],
    ['file' => 'adslight_tips_writing_ad.tpl', 'description' => ''],
    ['file' => 'adslight_search.tpl', 'description' => ''],
    ['file' => 'adslight_search_result.tpl', 'description' => ''],
    ['file' => 'adslight_maps.tpl', 'description' => ''],
    ['file' => 'adslight_menu.tpl', 'description' => ''],
    ['file' => 'adslight_bookmark.tpl', 'description' => ''],
    ['file' => 'adslight_xpayment_form.tpl', 'description' => ''],

    ['file' => 'admin/adslight_admin_testtablesorter.tpl', 'description' => ''],


    ['file' => 'adslight_listing.tpl', 'description' => ''],
    ['file' => 'adslight_listing_list0.tpl', 'description' => ''],

];

// ------------------- Blocks ------------------- //
$modversion['blocks'][] = [
    'file'        => 'ads.php',
    'name'        => _MI_ADSLIGHT_BNAME1,
    'description' => _MI_ADSLIGHT_BNAME1_DESC,
    'show_func'   => 'adslight_show',
    'edit_func'   => 'adslight_edit',
    'options'     => 'date_created|10|25|0',
    'template'    => 'adslight_block_new.tpl',
];

$modversion['blocks'][] = [
    'file'        => 'ads.php',
    'name'        => _MI_ADSLIGHT_BNAME2,
    'description' => _MI_ADSLIGHT_BNAME2_DESC,
    'show_func'   => 'adslight_show',
    'edit_func'   => 'adslight_edit',
    'options'     => 'hits|10|25|0',
    'template'    => 'adslight_block_top.tpl',
];

$modversion['blocks'][] = [
    'file'        => 'ads_2.php',
    'name'        => _MI_ADSLIGHT_BNAME3,
    'description' => _MI_ADSLIGHT_BNAME3_DESC,
    'show_func'   => 'adslight_b2_show',
    'edit_func'   => 'adslight_b2_edit',
    'options'     => 'date_created|10|25|0',
    'template'    => 'adslight_block2_new.tpl',
];

$modversion['blocks'][] = [
    'file'        => 'adslight_add.php',
    'name'        => _MI_ADSLIGHT_ADDMENU,
    'description' => _MI_ADSLIGHT_ADDMENU_DESC,
    'show_func'   => 'b_adslight_add',
    'template'    => 'adslight_block_add.tpl',
];

// Bloc Map_France //
$modversion['blocks'][] = [
    'file'        => 'maps.php',
    'name'        => _MI_ADSLIGHT_MAPFRANCE,
    'description' => _MI_ADSLIGHT_MAPFRANCE_DESC,
    'show_func'   => 'adslight_maps_show',
    'edit_func'   => 'adslight_maps_edit',
    'options'     => 'date_created|10|25|0',
    'template'    => 'adslight_block_maps.tpl',
];

// Menu
$modversion['hasMain'] = 1;

if ($GLOBALS['xoopsUser']) {
    $member_usid                  = is_object($GLOBALS['xoopsUser']) ? $GLOBALS['xoopsUser']->getVar('uid') : 0;
    $modversion['sub'][1]['name'] = _MI_ADSLIGHT_SMENU2;
    $modversion['sub'][1]['url']  = 'add.php';
    $modversion['sub'][3]['name'] = _MI_ADSLIGHT_SMENU3;
    $modversion['sub'][3]['url']  = 'search.php';
    $modversion['sub'][2]['name'] = _MI_ADSLIGHT_SMENU1;
    $modversion['sub'][2]['url']  = 'members.php?usid=' . $member_usid . '';
}

// Recherche
$modversion['hasSearch']      = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'adslight_search';

// Commentaires
$modversion['hasComments'] = 1;

$modversion['comments']['itemName']    = 'usid';
$modversion['comments']['pageName']    = 'members.php';
$modversion['comments']['extraParams'] = ['usid'];

// Comment callback functions
$modversion['comments']['callbackFile']        = 'include/comment_functions.php';
$modversion['comments']['callback']['approve'] = 'adslight_com_approve';
$modversion['comments']['callback']['update']  = 'adslight_com_update';

// Préférences
$modversion['hasconfig'] = 1;
// default admin editor
xoops_load('XoopsEditorHandler');
$editorHandler = \XoopsEditorHandler::getInstance();
$editorList    = array_flip($editorHandler->getList());
// ------------------- Config Options ------------------- //
$modversion['config'][] = [
    'name'        => 'editorAdmin',
    'title'       => '_MI_ADSLIGHT_EDITOR_ADMIN',
    'description' => '_MI_ADSLIGHT_EDITOR_ADMIN_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'dhtmltextarea',
    'options'     => $editorList,
];

$modversion['config'][] = [
    'name'        => 'editorUser',
    'title'       => '_MI_ADSLIGHT_EDITOR_USER',
    'description' => '_MI_ADSLIGHT_EDITOR_USER_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'dhtmltextarea',
    'options'     => $editorList,
];

$modversion['config'][] = [
    'name'        => 'adslight_currency_code',
    'title'       => '_MI_ADSLIGHT_CURRENCY_CODE',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'EUR',
];

$modversion['config'][] = [
    'name'        => 'adslight_currency_symbol',
    'title'       => '_MI_ADSLIGHT_CURRENCY_SYMBOL',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '&euro;',
];

/**
 * Currency's place (left or right) ?
 */
$modversion['config'][] = [
    'name'        => 'currency_position',
    'title'       => '_MI_ADSLIGHT_CURRENCY_POSITION',
    'description' => '_MI_ADSLIGHT_CURRENCY_POSITION_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => _MI_ADSLIGHT_SETTING_7,
];

$modversion['config'][] = [
    'name'        => 'adslight_maps_set',
    'title'       => '_MI_ADSLIGHT_MAPSSET',
    'description' => '_MI_ADSLIGHT_MAPSSET_DESC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'france',
    'options'     => $adslight_maps,
];

$modversion['config'][] = [
    'name'        => 'adslight_maps_width',
    'title'       => '_MI_ADSLIGHT_MAPSW_TITLE',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '400',
];

$modversion['config'][] = [
    'name'        => 'adslight_maps_height',
    'title'       => '_MI_ADSLIGHT_MAPSH_TITLE',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '400',
];

$modversion['config'][] = [
    'name'        => 'adslight_perpage',
    'title'       => '_MI_ADSLIGHT_PERPAGE',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => '15',
    'options'     => [
        '10' => 10,
        '15' => 15,
        '20' => 20,
        '25' => 25,
        '30' => 30,
        '35' => 35,
        '40' => 40,
        '50' => 50,
    ],
];

$modversion['config'][] = [
    'name'        => 'adslight_newad',
    'title'       => '_MI_ADSLIGHT_VIEWNEWCLASS',
    'description' => '_MI_ADSLIGHT_ONHOME',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_newcount',
    'title'       => '_MI_ADSLIGHT_NUMNEW',
    'description' => '_MI_ADSLIGHT_ONHOME',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => '10',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_countday',
    'title'       => '_MI_ADSLIGHT_NEWTIME',
    'description' => '_MI_ADSLIGHT_INDAYS',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => '3',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_howlong',
    'title'       => '_MI_ADSLIGHT_DAYS',
    'description' => '_MI_ADSLIGHT_INDAYS',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => '14',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_sold_days',
    'title'       => '_MI_ADSLIGHT_SOLD_DAYS',
    'description' => '_MI_ADSLIGHT_SOLDINDAYS',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => '3',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_not_premium',
    'title'       => '_MI_ADSLIGHT_NOT_PREMIUM',
    'description' => '_MI_ADSLIGHT_NOT_PREMIUM_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => '1',
];

$modversion['config'][] = [
    'name'        => 'adslight_nb_pict',
    'title'       => '_MI_ADSLIGHT_NUMBPICT_TITLE',
    'description' => '_MI_ADSLIGHT_NUMBPICT_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => '12',
];

$modversion['config'][] = [
    'name'        => 'adslight_path_upload',
    'title'       => '_MI_ADSLIGHT_UPLOAD_TITLE',
    'description' => '_MI_ADSLIGHT_UPLOAD_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => XOOPS_ROOT_PATH . '/uploads/adslight/',
];

$modversion['config'][] = [
    'name'        => 'adslight_link_upload',
    'title'       => '_MI_ADSLIGHT_LINKUPLOAD_TI',
    'description' => '_MI_ADSLIGHT_LINKUPLOAD_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => XOOPS_URL . '/uploads/adslight',
];

$modversion['config'][] = [
    'name'        => 'adslight_thumb_width',
    'title'       => '_MI_ADSLIGHT_THUMW_TITLE',
    'description' => '_MI_ADSLIGHT_THUMBW_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '125',
];

$modversion['config'][] = [
    'name'        => 'adslight_thumb_height',
    'title'       => '_MI_ADSLIGHT_THUMBH_TITLE',
    'description' => '_MI_ADSLIGHT_THUMBH_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '175',
];

$modversion['config'][] = [
    'name'        => 'adslight_resized_width',
    'title'       => '_MI_ADSLIGHT_RESIZEDW_TITLE',
    'description' => '_MI_ADSLIGHT_RESIZEDW_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '650',
];

$modversion['config'][] = [
    'name'        => 'adslight_resized_height',
    'title'       => '_MI_ADSLIGHT_RESIZEDH_TITLE',
    'description' => '_MI_ADSLIGHT_RESIZEDH_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '450',
];

$modversion['config'][] = [
    'name'        => 'adslight_max_orig_width',
    'title'       => '_MI_ADSLIGHT_ORIGW_TITLE',
    'description' => '_MI_ADSLIGHT_ORIGW_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '2048',
];

$modversion['config'][] = [
    'name'        => 'adslight_max_orig_height',
    'title'       => '_MI_ADSLIGHT_ORIGH_TITLE',
    'description' => '_MI_ADSLIGHT_ORIGH_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '1600',
];

$modversion['config'][] = [
    'name'        => 'adslight_maxfilesize',
    'title'       => '_MI_ADSLIGHT_MAXFILEBYTES_T',
    'description' => '_MI_ADSLIGHT_MAXFILEBYTES_D',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '512000',
];

$modversion['config'][] = [
    'name'        => 'adslight_souscat',
    'title'       => '_MI_ADSLIGHT_DISPLSUBCAT',
    'description' => '_MI_ADSLIGHT_ONHOME',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_cat_desc',
    'title'       => '_MI_ADSLIGHT_CAT_META',
    'description' => '_MI_ADSLIGHT_CAT_META_DESCRIPTION',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_nbsouscat',
    'title'       => '_MI_ADSLIGHT_NBDISPLSUBCAT',
    'description' => '',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => '4',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_csortorder',
    'title'       => '_MI_ADSLIGHT_CSORT_ORDER',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'title',
    'options'     => [
        '_MI_ADSLIGHT_ORDERALPHA' => 'title',
        '_MI_ADSLIGHT_ORDERPERSO' => 'order',
    ],
];

$modversion['config'][] = [
    'name'        => 'adslight_lsort_order',
    'title'       => '_MI_ADSLIGHT_LSORT_ORDER',
    'description' => '',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'date_created DESC',
    'options'     => [
        '_MI_ADSLIGHT_ORDER_DATE'  => 'date_created DESC',
        '_MI_ADSLIGHT_ORDER_PRICE' => 'price ASC',
        '_MI_ADSLIGHT_ORDER_TITLE' => 'title ASC',
        '_MI_ADSLIGHT_ORDER_POP'   => 'hits DESC',
    ],
];

$modversion['config'][] = [
    'name'        => 'adslight_diff_name',
    'title'       => '_MI_ADSLIGHT_DIFF_NAME',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '0',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_diff_email',
    'title'       => '_MI_ADSLIGHT_DIFF_EMAIL',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '0',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_rate_user',
    'title'       => '_MI_ADSLIGHT_RATE_USER',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_rate_item',
    'title'       => '_MI_ADSLIGHT_RATE_ITEM',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_use_country',
    'title'       => '_MI_ADSLIGHT_USE_COUNTRY',
    'description' => '_MI_ADSLIGHT_USE_COUNTRY_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_lightbox',
    'title'       => '_MI_ADSLIGHT_LIGHTBOX',
    'description' => '_MI_ADSLIGHT_LIGHTBOX_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_almost',
    'title'       => '_MI_ADSLIGHT_ALMOST',
    'description' => '_MI_ADSLIGHT_INDAYS',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => '3',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_main_cat',
    'title'       => '_MI_ADSLIGHT_MAIN_CAT',
    'description' => '_MI_ADSLIGHT_MAIN_CAT_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_use_catscode',
    'title'       => '_MI_ADSLIGHT_CAT_DESC',
    'description' => '_MI_ADSLIGHT_DESC_CAT_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '0',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_cats_code',
    'title'       => '_MI_ADSLIGHT_ADSLIGHT_CATS_CODE',
    'description' => '_MI_ADSLIGHT_ADSLIGHT_CATS_CODE_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'adslight_use_captcha',
    'title'       => '_MI_ADSLIGHT_USE_CAPTCHA',
    'description' => '_MI_ADSLIGHT_USE_CAPTCHA_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'active_rewriteurl',
    'title'       => '_MI_ADSLIGHT_ACTIVE_REWRITEURL',
    'description' => '_MI_ADSLIGHT_ACTIVE_REWRITEURL_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '0',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'active_thumbsindex',
    'title'       => '_MI_ADSLIGHT_ACTIVE_THUMBSINDEX',
    'description' => '_MI_ADSLIGHT_ACTIVE_THUMBSINDEX_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '0',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'active_thumbscats',
    'title'       => '_MI_ADSLIGHT_ACTIVE_THUMBSCATS',
    'description' => '_MI_ADSLIGHT_ACTIVE_THUMBSCATS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '0',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_use_index_code',
    'title'       => '_MI_ADSLIGHT_ADSLIGHT_USE_INDEX_CODE',
    'description' => '_MI_ADSLIGHT_ADSLIGHT_USE_INDEX_CODE_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '0',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_use_banner',
    'title'       => '_MI_ADSLIGHT_ADSLIGHT_USE_BANNER',
    'description' => '_MI_ADSLIGHT_ADSLIGHT_USE_BANNER_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '0',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_index_code',
    'title'       => '_MI_ADSLIGHT_ADSLIGHT_INDEX_CODE',
    'description' => '_MI_ADSLIGHT_ADSLIGHT_INDEX_CODE_DESC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'adslight_index_code_place',
    'title'       => '_MI_ADSLIGHT_ADSLIGHT_INDEX_CODE_PLACE',
    'description' => '_MI_ADSLIGHT_ADSLIGHT_INDEX_CODE_PLACE_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '5',
];

$modversion['config'][] = [
    'name'        => 'adslight_use_tipswrite',
    'title'       => '_MI_ADSLIGHT_USE_TIPS_WRITE',
    'description' => '_MI_ADSLIGHT_USEDESC_TIPS_WRITE',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '0',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_active_menu',
    'title'       => '_MI_ADSLIGHT_ACTIVE_MENU',
    'description' => '_MI_ADSLIGHT_USEDESC_ACTIVEMENU',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_active_rss',
    'title'       => '_MI_ADSLIGHT_ACTIVE_RSS',
    'description' => '_MI_ADSLIGHT_USEDESC_ACTIVERSS',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_active_bookmark',
    'title'       => '_MI_ADSLIGHT_ACTIVE_BOOKMARK',
    'description' => '_MI_ADSLIGHT_USEDESC_ACTIVEBOOKMARK',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [],
];

$modversion['config'][] = [
    'name'        => 'adslight_style_bookmark',
    'title'       => '_MI_ADSLIGHT_STBOOKMARK',
    'description' => '_MI_ADSLIGHT_DESC_STBOOKMARK',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => '1',
    'options'     => [
        'Twitter'  => 1,
        'Facebook' => 2,
        'Google'   => 3,
    ],
];

$modversion['config'][] = [
    'name'        => 'adslight_tips_writetitle',
    'title'       => '_MI_ADSLIGHT_TITLE_TIPS_WRITE',
    'description' => '_MI_ADSLIGHT_TITLEDESC_TIPS_WRITE',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => _MI_ADSLIGHT_ADVISE_TITLE,
];

$modversion['config'][] = [
    'name'        => 'adslight_tips_writetxt',
    'title'       => '_MI_ADSLIGHT_TEXT_TIPS_WRITE',
    'description' => '_MI_ADSLIGHT_TEXTDESC_TIPS_WRITE',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => _MI_ADSLIGHT_ADVISE_TEXT,
];

//$modversion['config'][] = [
//    'name'        => 'adslight_active_xpayment',
//    'title'       => '_MI_ADSLIGHT_ACTIVE_XPAYMENT',
//    'description' => '_MI_ADSLIGHT_TEXTDESC_XPAYMENT',
//    'formtype'    => 'yesno',
//    'valuetype'   => 'int',
//    'default'     => '0',
//    'options'     => [],
//];

/**
 * Make Sample button visible?
 */
$modversion['config'][] = [
    'name'        => 'displaySampleButton',
    'title'       => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_SAMPLE_BUTTON',
    'description' => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_SAMPLE_BUTTON_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

/**
 * Show Developer Tools?
 */
$modversion['config'][] = [
    'name'        => 'displayDeveloperTools',
    'title'       => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_DEV_TOOLS',
    'description' => 'CO_' . $moduleDirNameUpper . '_' . 'SHOW_DEV_TOOLS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

//Notifications
$modversion['hasNotification'] = 1;

$modversion['notification']['lookup_file'] = 'include/notification.inc.php';
$modversion['notification']['lookup_func'] = 'adslight_notify_iteminfo';

//Categories
$modversion['notification']['category'][1]['name']           = 'category';
$modversion['notification']['category'][1]['title']          = _MI_ADSLIGHT_CATEGORY_NOTIFY;
$modversion['notification']['category'][1]['description']    = _MI_ADSLIGHT_CATEGORY_NOTIFYDSC;
$modversion['notification']['category'][1]['subscribe_from'] = 'viewcats.php';
$modversion['notification']['category'][1]['item_name']      = 'cid';
$modversion['notification']['category'][1]['allow_bookmark'] = 0;
$modversion['notification']['category'][1]['extraParams']    = ['pa'];

$modversion['notification']['category'][2]['name']           = 'listing';
$modversion['notification']['category'][2]['title']          = _MI_ADSLIGHT_NOTIFY;
$modversion['notification']['category'][2]['description']    = _MI_ADSLIGHT_NOTIFYDSC;
$modversion['notification']['category'][2]['subscribe_from'] = 'index.php';
$modversion['notification']['category'][2]['item_name']      = 'lid';
$modversion['notification']['category'][2]['allow_bookmark'] = 0;
$modversion['notification']['category'][2]['extraParams']    = ['pa'];

$modversion['notification']['category'][3]['name']           = 'global';
$modversion['notification']['category'][3]['title']          = _MI_ADSLIGHT_GLOBAL_NOTIFY;
$modversion['notification']['category'][3]['description']    = _MI_ADSLIGHT_GLOBAL_NOTIFYDSC;
$modversion['notification']['category'][3]['subscribe_from'] = 'index.php';
$modversion['notification']['category'][3]['extraParams']    = ['pa'];

// AdsLight notice for new listing in a cateogory
$modversion['notification']['event'][1]['name']          = 'new_listing';
$modversion['notification']['event'][1]['category']      = 'category';
$modversion['notification']['event'][1]['title']         = _MI_ADSLIGHT_NEWPOST_NOTIFY;
$modversion['notification']['event'][1]['caption']       = _MI_ADSLIGHT_NEWPOST_NOTIFYCAP;
$modversion['notification']['event'][1]['description']   = _MI_ADSLIGHT_NEWPOST_NOTIFYDSC;
$modversion['notification']['event'][1]['mail_template'] = 'listing_newpost_notify';
$modversion['notification']['event'][1]['mail_subject']  = _MI_ADSLIGHT_NEWPOST_NOTIFYSBJ;

// AdsLight notice for new listing any category
$modversion['notification']['event'][2]['name']          = 'new_listing';
$modversion['notification']['event'][2]['category']      = 'global';
$modversion['notification']['event'][2]['title']         = _MI_ADSLIGHT_GLOBAL_NEWPOST_NOTIFY;
$modversion['notification']['event'][2]['caption']       = _MI_ADSLIGHT_GLOBAL_NEWPOST_NOTIFYCAP;
$modversion['notification']['event'][2]['description']   = _MI_ADSLIGHT_GLOBAL_NEWPOST_NOTIFYDSC;
$modversion['notification']['event'][2]['mail_template'] = 'listing_newpost_notify';
$modversion['notification']['event'][2]['mail_subject']  = _MI_ADSLIGHT_GLOBAL_NEWPOST_NOTIFYSBJ;
