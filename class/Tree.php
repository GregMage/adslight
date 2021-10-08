<?php

declare(strict_types=1);

namespace XoopsModules\Adslight;

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

use Xmf\Module\Admin;

/**
 * Class Tree
 */
class Tree
{
    public $table;
    public $id;
    public $pid;
    public $order;
    public $title;
    /**
     * @var \XoopsMySQLDatabase
     */
    public $db;

    /**
     * @param $table_name
     * @param $id_name
     * @param $pid_name
     */
    public function __construct(
        $table_name,
        $id_name,
        $pid_name
    ) {
        $this->db    = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->table = $table_name;
        $this->id    = $id_name;
        $this->pid   = $pid_name;
        $this->order = '';
        $this->title = '';
    }

    /**
     * @param int    $sel_id
     * @param string $order
     */
    public function getFirstChild($sel_id, $order = ''): array
    {
        $arr = [];
        $sql = 'SELECT SQL_CACHE * FROM ' . $this->table . ' WHERE ' . $this->pid . '=' . $sel_id . ' ';

        $categories = Utility::getMyItemIds('adslight_view');
        if (\is_array($categories) && $categories !== []) {
            $sql .= ' AND ' . $this->pid . ' IN (' . \implode(',', $categories) . ') ';
        }

        if ('' !== $order) {
            $sql .= " ORDER BY ${order}";
        }

        $result = $this->db->query($sql);
        $count  = $this->db->getRowsNum($result);
        if (0 === $count) {
            return $arr;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $arr[] = $myrow;
        }

        return $arr;
    }

    /**
     * @param $sel_id
     * @return array
     */
    public function getFirstChildId($sel_id): array
    {
        $idarray = [];
        $sel_id  = (int)$sel_id;
        $sql = 'SELECT SQL_CACHE ' . $this->id . ' FROM ' . $this->table . ' WHERE ' . $this->pid . '=' . $sel_id;
        $result  = $this->db->query($sql);

        $categories = Utility::getMyItemIds('adslight_view');
        if (\is_array($categories) && $categories !== []) {
            $result .= ' AND ' . $this->pid . ' IN (' . \implode(',', $categories) . ') ';
        }

        $count = $this->db->getRowsNum($result);
        if (0 === $count) {
            return $idarray;
        }
        while (false !== [$id] = $this->db->fetchRow($result)) {
            $idarray[] = $id;
        }

        return $idarray;
    }

    /**
     * @param        $sel_id
     * @param string $order
     * @param array  $idarray
     * @return array
     */
    public function getAllChildId($sel_id, $order = '', $idarray = []): array
    {
        $sel_id = (int)$sel_id;
        $sql    = 'SELECT SQL_CACHE ' . $this->id . ' FROM ' . $this->table . ' WHERE ' . $this->pid . '=' . $sel_id;

        $categories = Utility::getMyItemIds('adslight_view');
        if (\is_array($categories) && $categories !== []) {
            $sql .= ' AND ' . $this->pid . ' IN (' . \implode(',', $categories) . ') ';
        }

        if ('' !== $order) {
            $sql .= " ORDER BY {$order}";
        }
        $result = $this->db->query($sql);
        $count  = $this->db->getRowsNum($result);
        if (0 === $count) {
            return $idarray;
        }
        while (false !== [$r_id] = $this->db->fetchRow($result)) {
            $idarray[] = $r_id;
            $idarray   = $this->getAllChildId($r_id, $order, $idarray);
        }

        return $idarray;
    }

    /**
     * @param        $sel_id
     * @param string $order
     * @param array  $idarray
     * @return array
     */
    public function getAllParentId($sel_id, $order = '', $idarray = []): array
    {
        $sql = 'SELECT ' . $this->pid . ' FROM ' . $this->table . ' WHERE ' . $this->id . '=' . (int)$sel_id;

        $categories = Utility::getMyItemIds('adslight_view');
        if (\is_array($categories) && $categories !== []) {
            $sql .= ' AND ' . $this->pid . ' IN (' . \implode(',', $categories) . ') ';
        }

        if ('' !== $order) {
            $sql .= " ORDER BY {$order}";
        }
        $result = $this->db->query($sql);
        [$r_id] = $this->db->fetchRow($result);
        if (0 === $r_id) {
            return $idarray;
        }
        $idarray[] = $r_id;

        return $this->getAllParentId($r_id, $order, $idarray);
    }

    /**
     * @param        $sel_id
     * @param        $title
     * @param string $path
     * @return string
     */
    public function getPathFromId($sel_id, $title, $path = ''): string
    {
        $sql = 'SELECT ' . $this->pid . ', ' . $title . ' FROM ' . $this->table . ' WHERE ' . $this->id . '=' . (int)$sel_id . '';
        //        $result = $this->db->query('SELECT ' . $this->pid . ', ' . $title . ' FROM ' . $this->table . ' WHERE ' . $this->id . '=' . $this->db->escape($sel_id) . "'");

        $categories = Utility::getMyItemIds('adslight_view');
        if (\is_array($categories) && $categories !== []) {
            //            $result .= ' AND cid IN (' . implode(',', $categories) . ') ';
            $sql .= ' AND cid IN (' . \implode(',', $categories) . ') ';
        }

        $result = $this->db->query($sql);

        if (0 === $this->db->getRowsNum($result)) {
            return $path;
        }
        [$parentid, $name] = $this->db->fetchRow($result);
        \MyTextSanitizer::getInstance();
        $name = \htmlspecialchars($name, \ENT_QUOTES | \ENT_HTML5);
        $path = '/' . $name . $path . '';
        if (0 === $parentid) {
            return $path;
        }

        return $this->getPathFromId($parentid, $title, $path);
    }

    /**
     * @param        $title
     * @param string $order
     * @param int    $preset_id
     * @param int    $none
     * @param string $sel_name
     * @param string $onchange
     */
    public function makeMySelBox($title, $order = '', $preset_id = 0, $none = 0, $sel_name = '', $onchange = ''): void
    {
        if ('' === $sel_name) {
            $sel_name = $this->id;
        }
        $myts = \MyTextSanitizer::getInstance();
        echo '<select name="' . $sel_name . '"';
        if ('' !== $onchange) {
            echo ' onchange="' . $onchange . '"';
        }
        echo '>';

        $sql        = 'SELECT SQL_CACHE cid, title FROM ' . $this->table . ' WHERE pid=0';
        $categories = Utility::getMyItemIds('adslight_submit');

        if (\is_array($categories) && $categories !== []) {
            $sql .= ' AND cid IN (' . \implode(',', $categories) . ') ';
        }

        if ('' !== $order) {
            $sql .= " ORDER BY ${order}";
        }

        $result = $this->db->query($sql);
        if (0 !== $none) {
            echo '<option value="0">----</option>';
        }
        while (false !== [$catid, $name] = $this->db->fetchRow($result)) {
            $sel = '';
            if ($catid === $preset_id) {
                $sel = ' selected';
            }
            echo "<option value=\"{$catid}\"{$sel}>{$name}</option>";
            $sel = '';
            $arr = $this->getChildTreeArray($catid, $order);
            foreach ($arr as $option) {
                $option['prefix'] = \str_replace('.', '--', $option['prefix']);
                $catpath          = $option['prefix'] . '&nbsp;' . $myts->displayTarea($option[$title]);
                if ($option['cid'] === $preset_id) {
                    $sel = ' selected';
                }
                echo "<option value=\"{$option['cid']}\"{$sel}>{$catpath}</option>";
                $sel = '';
            }
        }
        echo '</select>';
    }

    /**
     * @param        $sel_id
     * @param        $title
     * @param        $funcURL
     * @param string $path
     * @return string
     */
    public function getNicePathFromId($sel_id, $title, $funcURL, $path = ''): string
    {
        $sql    = 'SELECT SQL_CACHE ' . $this->pid . ", {$title} FROM " . $this->table . ' WHERE ' . $this->id . '=' . (int)$sel_id;
        $result = $this->db->query($sql);
        if (0 === $this->db->getRowsNum($result)) {
            return $path;
        }
        [$parentid, $name] = $this->db->fetchRow($result);
        \MyTextSanitizer::getInstance();
        $name = \htmlspecialchars($name, \ENT_QUOTES | \ENT_HTML5);

        $arrow = '<img src="' . XOOPS_URL . '/modules/adslight/assets/images/arrow.gif" alt="&raquo;" >';

        $path = "&nbsp;&nbsp;{$arrow}&nbsp;&nbsp;<a title=\"" . \_ADSLIGHT_ANNONCES . " {$name}\" href=\"{$funcURL}" . $this->id . '=' . (int)$sel_id . "\">{$name}</a>{$path}";

        if (0 === $parentid) {
            return $path;
        }

        return $this->getNicePathFromId($parentid, $title, $funcURL, $path);
    }

    /**
     * @param        $sel_id
     * @param string $path
     * @return string
     */
    public function getIdPathFromId($sel_id, $path = ''): string
    {
        $sel_id = (int)$sel_id;
        $sql = 'SELECT SQL_CACHE ' . $this->pid . ' FROM ' . $this->table . ' WHERE ' . $this->id . '=' . $sel_id;
        $result = $this->db->query($sql);
        if (0 === $this->db->getRowsNum($result)) {
            return $path;
        }
        [$parentid] = $this->db->fetchRow($result);
        $path = "/{$sel_id}{$path}";
        if (0 === $parentid) {
            return $path;
        }

        return $this->getIdPathFromId($parentid, $path);
    }

    /**
     * @param int    $sel_id
     * @param string $order
     * @param array  $parray
     */
    public function getAllChild($sel_id = 0, $order = '', $parray = []): array
    {
        $sql = 'SELECT SQL_CACHE * FROM ' . $this->table . ' WHERE ' . $this->pid . '=' . $sel_id;

        $categories = Utility::getMyItemIds('adslight_view');
        if (\is_array($categories) && $categories !== []) {
            $sql .= ' AND ' . $this->pid . ' IN (' . \implode(',', $categories) . ') ';
        }

        if ('' !== $order) {
            $sql .= " ORDER BY {$order}";
        }

        $result = $this->db->query($sql);
        $count  = $this->db->getRowsNum($result);
        if (0 === $count) {
            return $parray;
        }
        while (false !== ($row = $this->db->fetchArray($result))) {
            $parray[] = $row;
            $parray   = $this->getAllChild($row[$this->id], $order, $parray);
        }

        return $parray;
    }

    /**
     * @param int    $sel_id
     * @param string $order
     * @param array  $parray
     * @param string $r_prefix
     */
    public function getChildTreeArray($sel_id = 0, $order = '', $parray = [], $r_prefix = ''): array
    {
        $sql = 'SELECT SQL_CACHE * FROM ' . $this->table . ' WHERE ' . $this->pid . '=' . $sel_id;

        $categories = Utility::getMyItemIds('adslight_view');
        if (\is_array($categories) && $categories !== []) {
            $sql .= ' AND cid IN (' . \implode(',', $categories) . ') ';
        }

        if ('' !== $order) {
            $sql .= " ORDER BY {$order}";
        }
        $result = $this->db->query($sql);
        $count  = $this->db->getRowsNum($result);
        if (0 === $count) {
            return $parray;
        }
        while (false !== ($row = $this->db->fetchArray($result))) {
            $row['prefix'] = $r_prefix . '.';
            $parray[]      = $row;
            $parray        = $this->getChildTreeArray($row[$this->id], $order, $parray, $row['prefix']);
        }

        return $parray;
    }

    /**
     * @param        $title
     * @param string $order
     * @param int    $preset_id
     * @param int    $none
     * @param string $sel_name
     * @param string $onchange
     */
    public function makeAdSelBox($title, $order = '', $preset_id = 0, $none = 0, $sel_name = '', $onchange = ''): void
    {
        global $myts, $xoopsDB;
        $helper = Helper::getInstance();
        $pathIcon16 = Admin::iconUrl('', '16');
        //        require_once XOOPS_ROOT_PATH . '/modules/adslight/include/gtickets.php';

        if ('' === $sel_name) {
            $sel_name = $this->id;
        }

        $sql = 'SELECT ' . $this->id . ', ' . $title . ', cat_order FROM ' . $this->table . ' WHERE ' . $this->pid . '=0';
        if ('' !== $order) {
            $sql .= " ORDER BY {$order}";
        }
        $result = $xoopsDB->query($sql);
        while (false !== [$catid, $name, $cat_order] = $xoopsDB->fetchRow($result)) {
            echo '<table class="width100 bnone outer"><tr>
                <th class="left">';
            if ('cat_order' === $helper->getConfig('adslight_csortorder')) {
                echo "({$cat_order})";
            }
            echo "&nbsp;&nbsp;{$name}&nbsp;&nbsp;</th>
                <th class=\"center width10\"><a href=\"category.php?op=AdsNewCat&amp;cid={$catid}\"><img src=\"{$pathIcon16}/add.png\" border=\"0\" width=\"18\" height=\"18\" alt=\"" . \_AM_ADSLIGHT_ADDSUBCAT . '" title="' . \_AM_ADSLIGHT_ADDSUBCAT . "\"></a></th>
                <th class=\"center width10\"><a href=\"category.php?op=AdsModCat&amp;cid={$catid}\"><img src=\"{$pathIcon16}/edit.png\" border=\"0\" width=\"18\" height=\"18\" alt=\"" . \_AM_ADSLIGHT_MODIFSUBCAT . '" title="' . \_AM_ADSLIGHT_MODIFSUBCAT . "\"></a></th>
                <th class=\"center width10\"><a href=\"category.php?op=AdsDelCat&amp;cid={$catid}\"><img src=\"{$pathIcon16}/delete.png\" border=\"0\" width=\"18\" height=\"18\" alt=\"" . \_AM_ADSLIGHT_DELSUBCAT . '" title="' . \_AM_ADSLIGHT_DELSUBCAT . '"></a></th>
                </tr>';

            $arr   = $this->getChildTreeMapArray($catid, $order);
            $class = 'odd';
            foreach ($arr as $option) {
                echo "<tr class=\"{$class}\"><td>";

                $option['prefix'] = \str_replace('.', ' &nbsp;&nbsp;-&nbsp;', $option['prefix']);
                $catpath          = $option['prefix'] . '&nbsp;&nbsp;' . \htmlspecialchars($option[$title], \ENT_QUOTES | \ENT_HTML5);
                $cat_orderS       = $option['cat_order'];
                if ('cat_order' === $helper->getConfig('adslight_csortorder')) {
                    echo "({$cat_orderS})";
                }
                echo '' . $catpath . '</a></td>
                    <td align="center"><a href="category.php?op=AdsNewCat&amp;cid=' . $option[$this->id] . '"><img src="' . $pathIcon16 . '/add.png' . '" border=0 width=18 height=18 alt="' . \_AM_ADSLIGHT_ADDSUBCAT . '"title="' . \_AM_ADSLIGHT_ADDSUBCAT . '"></a></td>
                    <td align="center"><a href="category.php?op=AdsModCat&amp;cid=' . $option[$this->id] . '"><img src="' . $pathIcon16 . '/edit.png' . '" border=0 width=18 height=18 alt="' . \_AM_ADSLIGHT_MODIFSUBCAT . '" title ="' . \_AM_ADSLIGHT_MODIFSUBCAT . '"></a></td>
                    <td align="center"><a href="category.php?op=AdsDelCat&amp;cid=' . $option[$this->id] . '"><img src="' . $pathIcon16 . '/delete.png' . '" border=0 width=18 height=18 alt="' . \_AM_ADSLIGHT_DELSUBCAT . '" title="' . \_AM_ADSLIGHT_DELSUBCAT . '"></a></td>';

                $class = 'even' === $class ? 'odd' : 'even';
            }
            echo '</td></tr></table><br>';
        }
    }

    /**
     * @param int    $sel_id
     * @param string $order
     * @param array  $parray
     * @param string $r_prefix
     */
    public function getChildTreeMapArray($sel_id = 0, $order = '', $parray = [], $r_prefix = ''): array
    {
        global $xoopsDB;
        $sql = 'SELECT SQL_CACHE * FROM ' . $this->table . ' WHERE ' . $this->pid . '=' . $sel_id . ' ';

        $categories = Utility::getMyItemIds('adslight_view');
        if (\is_array($categories) && $categories !== []) {
            $sql .= ' AND ' . $this->pid . ' IN (' . \implode(',', $categories) . ') ';
        }

        if ('' !== $order) {
            $sql .= " ORDER BY {$order}";
        }
        $result = $xoopsDB->query($sql);
        $count  = $xoopsDB->getRowsNum($result);
        if (0 === $count) {
            return $parray;
        }
        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            $row['prefix'] = $r_prefix . '.';
            $parray[]      = $row;
            $parray        = $this->getChildTreeMapArray($row[$this->id], $order, $parray, $row['prefix']);
        }

        return $parray;
    }

    public function getCategoryList(): array
    {
        $sql = 'SELECT SQL_CACHE cid, pid, title FROM ' . $this->table;
        $result = $this->db->query($sql);
        $ret    = [];
        \MyTextSanitizer::getInstance();
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$myrow['cid']] = [
                'title' => \htmlspecialchars($myrow['title'], \ENT_QUOTES | \ENT_HTML5),
                'pid'   => $myrow['pid'],
            ];
        }

        return $ret;
    }
}
