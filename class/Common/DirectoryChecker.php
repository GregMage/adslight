<?php

declare(strict_types=1);

namespace XoopsModules\Adslight\Common;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * Module: Adslight
 *
 * @category        Module
 * @author          XOOPS Development Team <https://xoops.org>
 * @copyright       {@link https://xoops.org/ XOOPS Project}
 * @license         GPL 2.0 or later
 */

use Xmf\Request;

require_once \dirname(__DIR__, 4) . '/mainfile.php';
$moduleDirName      = \basename(\dirname(__DIR__, 2));
$moduleDirNameUpper = \mb_strtoupper($moduleDirName);
\xoops_loadLanguage('directorychecker', $moduleDirName);

/**
 * Class DirectoryChecker
 * check status of a directory
 */
class DirectoryChecker
{
    /**
     * @param     $path
     * @param int $mode
     * @param     $redirectFile
     *
     * @return bool|string
     */
    public static function getDirectoryStatus($path, $mode = 0777, $redirectFile = null)
    {
        global $pathIcon16;
        $moduleDirName      = \basename(\dirname(__DIR__, 2));
        $moduleDirNameUpper = \mb_strtoupper($moduleDirName);

        if (empty($path)) {
            return false;
        }
        if (null === $redirectFile) {
            $redirectFile = $_SERVER['SCRIPT_NAME'];
        }
        if (!@\is_dir($path)) {
            $path_status = "<img src='$pathIcon16/0.png' >";
            $path_status .= "$path (" . \constant('CO_' . $moduleDirNameUpper . '_' . 'NOTAVAILABLE') . ') ';
            $path_status .= "<form action='" . $_SERVER['SCRIPT_NAME'] . "' method='post'>";
            $path_status .= "<input type='hidden' name='op' value='createdir'>";
            $path_status .= "<input type='hidden' name='path' value='$path'>";
            $path_status .= "<input type='hidden' name='redirect' value='$redirectFile'>";
            $path_status .= "<button class='submit' onClick='this.form.submit();'>" . \constant('CO_' . $moduleDirNameUpper . '_' . 'CREATETHEDIR') . '</button>';
            $path_status .= '</form>';
        } elseif (@\is_writable($path)) {
            $path_status = "<img src='$pathIcon16/1.png' >";
            $path_status .= "$path (" . \constant('CO_' . $moduleDirNameUpper . '_' . 'AVAILABLE') . ') ';
            $currentMode = \mb_substr(\decoct(\fileperms($path)), 2);
            if ($currentMode != \decoct($mode)) {
                $path_status = "<img src='$pathIcon16/0.png' >";
                $path_status .= $path . \sprintf(\constant('CO_' . $moduleDirNameUpper . '_' . 'NOTWRITABLE'), \decoct($mode), $currentMode);
                $path_status .= "<form action='" . $_SERVER['SCRIPT_NAME'] . "' method='post'>";
                $path_status .= "<input type='hidden' name='op' value='setdirperm'>";
                $path_status .= "<input type='hidden' name='mode' value='$mode'>";
                $path_status .= "<input type='hidden' name='path' value='$path'>";
                $path_status .= "<input type='hidden' name='redirect' value='$redirectFile'>";
                $path_status .= "<button class='submit' onClick='this.form.submit();'>" . \constant('CO_' . $moduleDirNameUpper . '_' . 'SETMPERM') . '</button>';
                $path_status .= '</form>';
            }
        } else {
            $currentMode = \mb_substr(\decoct(\fileperms($path)), 2);
            $path_status = "<img src='$pathIcon16/0.png' >";
            $path_status .= $path . \sprintf(\constant('CO_' . $moduleDirNameUpper . '_' . 'NOTWRITABLE'), \decoct($mode), $currentMode);
            $path_status .= "<form action='" . $_SERVER['SCRIPT_NAME'] . "' method='post'>";
            $path_status .= "<input type='hidden' name='op' value='setdirperm'>";
            $path_status .= "<input type='hidden' name='mode' value='$mode'>";
            $path_status .= "<input type='hidden' name='path' value='$path'>";
            $path_status .= "<input type='hidden' name='redirect' value='$redirectFile'>";
            $path_status .= "<button class='submit' onClick='this.form.submit();'>" . \constant('CO_' . $moduleDirNameUpper . '_' . 'SETMPERM') . '</button>';
            $path_status .= '</form>';
        }

        return $path_status;
    }

    /**
     * @param     $target
     * @param int $mode
     *
     * @return bool
     */
    public static function createDirectory($target, $mode = 0777): bool
    {
        $target = \str_replace('..', '', $target);

        // http://www.php.net/manual/en/function.mkdir.php
        return \is_dir($target) || (self::createDirectory(\dirname($target), $mode) && !\mkdir($target, $mode) && !\is_dir($target));
    }

    /**
     * @param     $target
     * @param int $mode
     *
     * @return bool
     */
    public static function setDirectoryPermissions($target, $mode = 0777): bool
    {
        $target = \str_replace('..', '', $target);

        return @\chmod($target, (int)$mode);
    }

    /**
     * @param   $dir_path
     *
     * @return bool
     */
    public static function dirExists($dir_path): bool
    {
        return \is_dir($dir_path);
    }
}

$op = Request::getString('op', '', 'POST');
switch ($op) {
    case 'createdir':
        if (Request::hasVar('path', 'POST')) {
            $path = $_POST['path'];
        }
        if (Request::hasVar('redirect', 'POST')) {
            $redirect = $_POST['redirect'];
        }
        $msg = DirectoryChecker::createDirectory($path) ? \constant('CO_' . $moduleDirNameUpper . '_' . 'DIRCREATED') : \constant('CO_' . $moduleDirNameUpper . '_' . 'DIRNOTCREATED');
        \redirect_header($redirect, 2, $msg . ': ' . $path);
        break;
    case 'setdirperm':
        if (Request::hasVar('path', 'POST')) {
            $path = $_POST['path'];
        }
        if (Request::hasVar('redirect', 'POST')) {
            $redirect = $_POST['redirect'];
        }
        if (Request::hasVar('mode', 'POST')) {
            $mode = $_POST['mode'];
        }
        $msg = DirectoryChecker::setDirectoryPermissions($path, $mode) ? \constant('CO_' . $moduleDirNameUpper . '_' . 'PERMSET') : \constant('CO_' . $moduleDirNameUpper . '_' . 'PERMNOTSET');
        \redirect_header($redirect, 2, $msg . ': ' . $path);
        break;
}
