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
/////////////////////////////////////
// AdsLight UrlRewrite By Nikita   //
// http://www.aideordi.com         //
/////////////////////////////////////

use Xmf\Module\Admin;
use XoopsModules\Adslight\{
    Helper
};

/** @var Admin $adminObject */
/** @var Helper $helper */

require __DIR__ . '/preloads/autoloader.php';
// $GLOBALS['xoopsOption']['template_main'] = 'adslight_index.tpl';

$moduleDirName = \basename(__DIR__);
require_once \dirname(__DIR__, 2) . '/mainfile.php';

$helper = Helper::getInstance();
if ($helper->getConfig('active_rewriteurl') > 0) {
    require_once __DIR__ . '/seo_url.php';
}

$pathIcon16 = Admin::iconUrl('', '16');

$myts = \MyTextSanitizer::getInstance();

// Load language files
$helper->loadLanguage('main');

