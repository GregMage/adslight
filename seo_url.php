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

define('REAL_MODULE_NAME', 'adslight');
define('SEO_MODULE_NAME', 'annonces');

ob_start('seo_urls');

/**
 * @param $s
 *
 * @return string|string[]|null
 */
function seo_urls($s)
{
    $XPS_URL = str_replace('/', '\/', quotemeta(XOOPS_URL));
    $s       = adslight_absolutize($s); // Fix URLs and HTML.

    $module_name = REAL_MODULE_NAME;

    $search = [
        // Search URLs of modules' directry.
        '/<(a|meta)([^>]*)(href|url)=([\'\"]{0,1})' . $XPS_URL . '\/modules\/' . $module_name . '\/(viewcats.php)([^>\'\"]*)([\'\"]{1})([^>]*)>/i',
        '/<(a|meta)([^>]*)(href|url)=([\'\"]{0,1})' . $XPS_URL . '\/modules\/' . $module_name . '\/(viewads.php)([^>\'\"]*)([\'\"]{1})([^>]*)>/i',
        '/<(a|meta)([^>]*)(href|url)=([\'\"]{0,1})' . $XPS_URL . '\/modules\/' . $module_name . '\/(index.php)([^>\'\"]*)([\'\"]{1})([^>]*)>/i',
        //    '/<(a|meta)([^>]*)(href|url)=([\'\"]{0,1})'.$XPS_URL.'\/modules\/'.$module_name.'\/()([^>\'\"]*)([\'\"]{1})([^>]*)>/i',
    ];

    return preg_replace_callback($search, 'replaceLinks', $s);
}

/**
 * @param $matches
 * @return string
 */
function replaceLinks($matches): string
{
    $req_string = [];
    $add_to_url = '';
    switch ($matches[5]) {
        case 'viewcats.php':
            //            $add_to_url = '';
            $req_string = $matches[6];
            if (!empty($matches[6])) {
                //              replacing cid=x
                if (preg_match('/cid=(\d+)/', $matches[6], $mvars)) {
                    $add_to_url = 'c' . $mvars[1] . '/' . adslight_seo_cat($mvars[1]) . '.html';
                    $req_string = preg_replace('/cid=\d+/', '', $matches[6]);
                } else {
                    return $matches['0'];
                }
            }
            break;
        case 'viewads.php':
            //            $add_to_url = '';
            $req_string = $matches[6];
            if (!empty($matches[6])) {
                //              replacing lid=x
                if (preg_match('/lid=(\d+)/', $matches[6], $mvars)) {
                    $add_to_url = 'p' . $mvars[1] . '/' . adslight_seo_titre($mvars[1]) . '.html';
                    $req_string = preg_replace('/lid=\d+/', '', $matches[6]);
                } else {
                    return $matches['0'];
                }
            }
            break;
        default:
            break;
    }
    if ('?' === $req_string) {
        $req_string = '';
    }

    return '<' . $matches[1] . $matches[2] . $matches[3] . '=' . $matches[4] . XOOPS_URL . '/' . SEO_MODULE_NAME . '/' . $add_to_url . $req_string . $matches[7] . $matches[8] . '>';
}

/**
 * @param $cid
 *
 * @return string|array<string>|null
 */
function adslight_seo_cat($cid)
{
    /** @var \XoopsMySQLDatabase $xoopsDB */
    $xoopsDB = \XoopsDatabaseFactory::getDatabaseConnection();
    $sql     = ' SELECT title FROM ' . $xoopsDB->prefix('adslight_categories') . ' WHERE cid = ' . $cid . ' ';
    $result  = $xoopsDB->query($sql);
    $res     = $xoopsDB->fetchArray($result);

    return adslight_seo_title($res['title']);
}

/**
 * @param $lid
 *
 * @return string|array<string>|null
 */
function adslight_seo_titre($lid)
{
    /** @var \XoopsMySQLDatabase $xoopsDB */
    $xoopsDB = \XoopsDatabaseFactory::getDatabaseConnection();
    $sql     = ' SELECT title FROM ' . $xoopsDB->prefix('adslight_listing') . ' WHERE lid = ' . $lid . ' ';
    $result  = $xoopsDB->query($sql);
    $res     = $xoopsDB->fetchArray($result);

    return adslight_seo_title($res['title']);
}

/**
 * @param string $title
 * @param bool   $withExt
 *
 * @return string|array<string>|null
 */
function adslight_seo_title($title = '', $withExt = false)
{
    /**
     * if XOOPS ML is present, let's sanitize the title with the current language
     */
    $myts = \MyTextSanitizer::getInstance();
    if (method_exists($myts, 'formatForML')) {
        $title = $myts->formatForML($title);
    }

    // Transformation de la chaine en minuscule
    // String encoding to avoid 500 errors in case of unforeseen characters
    $title = rawurlencode(mb_strtolower($title));

    // Transformation des ponctuations
    //                 Tab     Space      !        "        #        %        &        '        (        )        ,        /        :        ;        <        =        >        ?        @        [        \        ]        ^        {        |        }        ~       .                 +
    $pattern = [
        '/%09/', // Tab
        '/%20/', // Space
        '/%21/', // !
        '/%22/', // "
        '/%23/', // #
        '/%25/', // %
        '/%26/', // &
        '/%27/', // '
        '/%28/', // (
        '/%29/', // )
        '/%2C/', // ,
        '/%2F/', // /
        '/%3A/', // :
        '/%3B/', // ;
        '/%3C/', // <
        '/%3D/', // =
        '/%3E/', // >
        '/%3F/', // ?
        '/%40/', // @
        '/%5B/', // [
        '/%5C/', // \
        '/%5D/', // ]
        '/%5E/', // ^
        '/%7B/', // {
        '/%7C/', // |
        '/%7D/', // }
        '/%7E/', // ~
        '/\./', // .
        '/%2A/',
        '/%2B/',
        '/quot/',
    ];
    $rep_pat = [
        '-',
        '-',
        '',
        '',
        '',
        '-100',
        '',
        '-',
        '',
        '',
        '',
        '-',
        '',
        '',
        '',
        '-',
        '',
        '',
        '-at-',
        '',
        '-',
        '',
        '-',
        '',
        '-',
        '',
        '-',
        '',
        '',
        '+',
        '',
    ];
    $title   = preg_replace($pattern, $rep_pat, $title);

    // Transformation of characters with accents
    //                  °        è        é        ê        ë        ç        à        â        ä        î        ï        ù        ü        û        ô        ö
    $pattern = [
        '/%B0/',        // °
        '/%E8/',        // è
        '/%E9/',        // é
        '/%EA/',        // ê
        '/%EB/',        // ë
        '/%E7/',        // ç
        '/%E0/',        // à
        '/%E2/',        // â
        '/%E4/',        // ä
        '/%EE/',        // î
        '/%EF/',        // ï
        '/%F9/',        // ù
        '/%FC/',        // ü
        '/%FB/',        // û
        '/%F4/',        // ô
        '/%F6/',        // ö
        '/%E3%A8/',
        '/%E3%A9/',
        '/%E3%A0/',
        '/%E3%AA/',
        '/%E3%A2/',
        '/a%80%9C/',
        '/a%80%9D/',
        '/%E3%A7/',
    ];
    $rep_pat = [
        '-',
        'e',
        'e',
        'e',
        'e',
        'c',
        'a',
        'a',
        'a',
        'i',
        'i',
        'u',
        'u',
        'u',
        'o',
        'o',
        'e',
        'e',
        'a',
        'e',
        'a',
        '-',
        '-',
        'c',
    ];
    $title   = preg_replace($pattern, $rep_pat, $title);

    if (count($title) > 0) {
        if ($withExt) {
            $title .= '.html';
        }

        return $title;
    }

    return '';
}

/**
 * @param $s
 *
 * @return string|string[]|null
 */
function adslight_absolutize($s)
{
    if (preg_match('/\/$/', $_SERVER['REQUEST_URI'])) {
        $req_dir = preg_replace('/\/$/', '', $_SERVER['REQUEST_URI']);
        $req_php = '';
    } else {
        $req_dir = dirname($_SERVER['REQUEST_URI']);
        $req_php = preg_replace('/.*(\/[a-zA-Z0-9_\-]+)\.php.*/', '\\1.php', $_SERVER['REQUEST_URI']);
    }
    $req_dir = '\\' === $req_dir || '/' === $req_dir ? '' : $req_dir;
    $dir_arr = explode('/', $req_dir);
    $m       = count($dir_arr) - 1;
    $d1      = @str_replace('/' . $dir_arr[$m], '', $req_dir);
    $d2      = @str_replace('/' . $dir_arr[$m - 1], '', $d1);
    $d3      = @str_replace('/' . $dir_arr[$m - 2], '', $d2);
    $d4      = @str_replace('/' . $dir_arr[$m - 3], '', $d3);
    $d5      = @str_replace('/' . $dir_arr[$m - 4], '', $d4);
    $host    = 'http://' . $_SERVER['HTTP_HOST'];
    $in      = [
        '/<([^>\?\&]*)(href|src|action|background|window\.location)=([^\"\' >]+)([^>]*)>/i',
        '/<([^>\?\&]*)(href|src|action|background|window\.location)=([\"\']{1})\.\.\/\.\.\/\.\.\/([^\"\']*)([\"\']{1})([^>]*)>/i',
        '/<([^>\?\&]*)(href|src|action|background|window\.location)=([\"\']{1})\.\.\/\.\.\/([^\"\']*)([\"\']{1})([^>]*)>/i',
        '/<([^>\?\&]*)(href|src|action|background|window\.location)=([\"\']{1})\.\.\/([^\"\']*)([\"\']{1})([^>]*)>/i',
        '/<([^>\?\&]*)(href|src|action|background|window\.location)=([\"\']{1})\/([^\"\']*)([\"\']{1})([^>]*)>/i',
        '/<([^>\?\&]*)(href|src|action|background|window\.location)=([\"\']{1})\?([^\"\']*)([\"\']{1})([^>]*)>/i'//This dir
        ,
        '/<([^>\?\&]*)(href|src|action|background|window\.location)=([\"\']{1})([^#]{1}[^\/\"\'>]*)([\"\']{1})([^>]*)>/i',
        '/<([^>\?\&]*)(href|src|action|background|window\.location)=([\"\']{1})(?:\.\/)?([^\"\'\/:]*\/*)?([^\"\'\/:]*\/*)?([^\"\'\/:]*\/*)?([a-zA-Z0-9_\-]+)\.([^\"\'>]*)([\"\']{1})([^>]*)>/i',
        '/[^"\'a-zA-Z_0-9](window\.open|url)\(([\"\']{0,1})(?:\.\/)?([^\"\'\/]*)\.([^\"\'\/]+)([\"\']*)([^\)]*)/i',
        '/<meta([^>]*)url=([a-zA-Z0-9_\-]+)\.([^\"\'>]*)([\"\']{1})([^>]*)>/i',
    ];
    $out     = [
        '<\\1\\2="\\3"\\4>',
        '<\\1\\2=\\3' . $host . $d3 . '/\\4\\5\\6>',
        '<\\1\\2=\\3' . $host . $d2 . '/\\4\\5\\6>',
        '<\\1\\2=\\3' . $host . $d1 . '/\\4\\5\\6>',
        '<\\1\\2=\\3' . $host . '/\\4\\5\\6>',
        '<\\1\\2=\\3' . $host . $_SERVER['SCRIPT_NAME'] . '?\\4\\5\\6>'//This dir.
        ,
        '<\\1\\2=\\3' . $host . $req_dir . '/\\4\\5\\6\\7>',
        '<\\1\\2=\\3' . $host . $req_dir . '/\\4\\5\\6\\7.\\8\\9\\10>',
        '$1($2' . $host . $req_dir . '/$3.$4$5$6',
        '<meta$1url=' . $host . $req_dir . '/$2.$3$4$5>',
    ];

    return preg_replace($in, $out, $s);
}
