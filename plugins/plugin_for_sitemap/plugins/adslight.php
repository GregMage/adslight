<?php

declare(strict_types=1);

/**
 * @return mixed
 */
function b_sitemap_adslight()
{
    $xoopsDB = \XoopsDatabaseFactory::getDatabaseConnection();
    return sitemap_get_categories_map($xoopsDB->prefix('adslight_categories'), 'cid', 'pid', 'title', 'viewcats.php?cid=', 'title');
}
