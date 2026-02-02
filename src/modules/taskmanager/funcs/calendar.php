<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2026 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_MOD_TASKMANAGER')) {
    exit('Stop!!!');
}

$page_title = $lang_module['calendar'];
$key_words = $module_info['keywords'];

// Lấy tháng và năm
$month = $nv_Request->get_int('month', 'get', date('n'));
$year = $nv_Request->get_int('year', 'get', date('Y'));

// Tính ngày đầu và cuối tháng
$first_day = mktime(0, 0, 0, $month, 1, $year);
$last_day = mktime(23, 59, 59, $month, date('t', $first_day), $year);

// Lấy các công việc trong tháng
$tasks = [];
if (defined('NV_IS_USER')) {
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks 
            WHERE assigned_to = " . $user_info['userid'] . "
            AND deadline >= " . $first_day . " AND deadline <= " . $last_day . "
            ORDER BY deadline ASC";
    
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $day = date('j', $row['deadline']);
        if (!isset($tasks[$day])) {
            $tasks[$day] = [];
        }
        $tasks[$day][] = $row;
    }
}

$xtpl = new XTemplate('calendar.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('MONTH', $month);
$xtpl->assign('YEAR', $year);

// Tạo lịch
$days_in_month = date('t', $first_day);
$first_day_of_week = date('w', $first_day);

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
