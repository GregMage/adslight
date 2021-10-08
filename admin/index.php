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
 */

use Xmf\Module\Admin;
use Xmf\Request;
use XoopsModules\Adslight\{
    Common\Configurator,
    Common\DirectoryChecker,
    Common\TestdataButtons,
    ListingHandler,
    CategoriesHandler,
    PriceHandler,
    Form,
    Utility
};

/** @var Admin $adminObject */
/** @var Helper $helper */
/** @var ListingHandler $listingHandler */
/** @var CategoriesHandler $categoriesHandler */
/** @var PriceHandler $priceHandler */
/** @var TypeHandler $typeHandler */
/** @var ConditionHandler $conditionHandler */
/** @var IplogHandler $iplogHandler */
/** @var ItemvotedataHandler $itemvotesHandler */
/** @var UservotesHandler $uservotesHandler */
/** @var PictureHandler $picturesHandler */
/** @var RepliesHandler $repliesHandler */

require_once __DIR__ . '/admin_header.php';
// Display Admin header
xoops_cp_header();

$utility = new Utility();
$adminObject->displayNavigation(basename(__FILE__));

//count "total Listing"
$totalListing = $listingHandler->getCount();
//count "total Categories"
$totalCategories = $categoriesHandler->getCount();
//count "total Type"

$totalType = $typeHandler->getCount();
//count "total Price"
$totalPrice = $priceHandler->getCount();
//count "total Condition"
$totalCondition = $conditionHandler->getCount();
//count "total Iplog"
$totalIplog = $iplogHandler->getCount();
//count "total Itemvotes"
$totalItemvotes = $itemvotesHandler->getCount();
//count "total Uservotes"
$totalUservotes = $uservotesHandler->getCount();
//count "total Pictures"
$totalPictures = $picturesHandler->getCount();
//count "total Replies"
$totalReplies = $repliesHandler->getCount();
// InfoBox Statistics
$adminObject->addInfoBox(AM_ADSLIGHT_STATISTICS);
// InfoBox listing
$adminObject->addInfoBoxLine(sprintf(AM_ADSLIGHT_THEREARE_LISTING, $totalListing));
// InfoBox categories
$adminObject->addInfoBoxLine(sprintf(AM_ADSLIGHT_THEREARE_CATEGORIES, $totalCategories));
// InfoBox type
$adminObject->addInfoBoxLine(sprintf(AM_ADSLIGHT_THEREARE_TYPE, $totalType));
// InfoBox price
$adminObject->addInfoBoxLine(sprintf(AM_ADSLIGHT_THEREARE_PRICE, $totalPrice));
// InfoBox user
$adminObject->addInfoBoxLine(sprintf(AM_ADSLIGHT_THEREARE_CONDITION, $totalCondition));
// InfoBox iplog
$adminObject->addInfoBoxLine(sprintf(AM_ADSLIGHT_THEREARE_IPLOG, $totalIplog));
// InfoBox itemvotes
$adminObject->addInfoBoxLine(sprintf(AM_ADSLIGHT_THEREARE_ITEMVOTES, $totalItemvotes));
// InfoBox uservotes
$adminObject->addInfoBoxLine(sprintf(AM_ADSLIGHT_THEREARE_USERVOTES, $totalUservotes));
// InfoBox pictures
$adminObject->addInfoBoxLine(sprintf(AM_ADSLIGHT_THEREARE_PICTURES, $totalPictures));
// InfoBox replies
$adminObject->addInfoBoxLine(sprintf(AM_ADSLIGHT_THEREARE_REPLIES, $totalReplies));

//------ check Upload Folders ---------------
$adminObject->addConfigBoxLine('');
$redirectFile = $_SERVER['SCRIPT_NAME'];

$configurator  = new Configurator();
$uploadFolders = $configurator->uploadFolders;

//foreach (array_keys($uploadFolders) as $i) {
//    $adminObject->addConfigBoxLine(DirectoryChecker::getDirectoryStatus($uploadFolders[$i], 0777, $redirectFile));
//}

//check for upload folders, create if needed
$configurator = new Configurator();
foreach (array_keys($configurator->uploadFolders) as $i) {
    $utility::createFolder($configurator->uploadFolders[$i]);
    $adminObject->addConfigBoxLine($configurator->uploadFolders[$i], 'folder');
}

//------------- Test Data Buttons ----------------------------
if ($helper->getConfig('displaySampleButton')) {
    TestdataButtons::loadButtonConfig($adminObject);
    $adminObject->displayButton('left', '');
}
$op = Request::getString('op', 0, 'GET');
switch ($op) {
    case 'hide_buttons':
        TestdataButtons::hideButtons();
        break;
    case 'show_buttons':
        TestdataButtons::showButtons();
        break;
}
//------------- End Test Data Buttons ----------------------------

$adminObject->displayIndex();
echo $utility::getServerStats();

//codeDump(__FILE__);
require_once __DIR__ . '/admin_footer.php';
