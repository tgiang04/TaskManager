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

$page_title = $nv_Lang->getModule('gantt_chart');
$key_words = $module_info['keywords'];

$project_id = $nv_Request->get_int('project_id', 'get', 0);

if ($project_id == 0) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

// Kiểm tra quyền truy cập
if (!nv_task_check_project_permission($project_id, $user_info['userid'])) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

// Lấy thông tin dự án
$project = nv_task_get_project($project_id);
if (!$project) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

// Lấy danh sách công việc
$tasks = [];
$sql = "SELECT t.*, s.title as status_title, s.color as status_color,
        u.username as assignee_username, u.first_name, u.last_name
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks t
        LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_status s ON t.status_id = s.id
        LEFT JOIN " . NV_USERS_GLOBALTABLE . " u ON t.assignee_id = u.userid
        WHERE t.project_id = " . $project_id . "
        ORDER BY t.created_time ASC";

$result = $db->query($sql);
$min_date = null;
$max_date = null;

while ($row = $result->fetch()) {
    // Tính toán ngày bắt đầu và kết thúc
    if ($row['start_date'] == 0) {
        $row['start_date'] = $row['created_time'];
    }
    if ($row['deadline'] == 0) {
        $row['deadline'] = $row['start_date'] + (7 * 86400); // Mặc định 7 ngày
    }
    
    // Cập nhật min/max date
    if ($min_date === null || $row['start_date'] < $min_date) {
        $min_date = $row['start_date'];
    }
    if ($max_date === null || $row['deadline'] > $max_date) {
        $max_date = $row['deadline'];
    }
    
    // Format dữ liệu cho Gantt
    $row['assignee_name'] = !empty($row['first_name']) ? $row['first_name'] . ' ' . $row['last_name'] : $row['assignee_username'];
    $row['start_formatted'] = date('Y-m-d', $row['start_date']);
    $row['end_formatted'] = date('Y-m-d', $row['deadline']);
    $row['duration_days'] = ceil(($row['deadline'] - $row['start_date']) / 86400);
    
    // Lấy dependencies
    $row['dependencies'] = [];
    $sql_dep = "SELECT dependency_task_id 
                FROM " . NV_PREFIXLANG . "_" . $module_data . "_task_dependencies
                WHERE task_id = " . $row['id'];
    $result_dep = $db->query($sql_dep);
    while ($dep = $result_dep->fetch()) {
        $row['dependencies'][] = $dep['dependency_task_id'];
    }
    
    $tasks[] = $row;
}

// Tạo timeline (theo tuần)
$timeline = [];
if ($min_date && $max_date) {
    $current = strtotime('monday this week', $min_date);
    $end = strtotime('sunday this week', $max_date);
    
    while ($current <= $end) {
        $week_start = $current;
        $week_end = strtotime('+6 days', $current);
        
        $timeline[] = [
            'start' => $week_start,
            'end' => $week_end,
            'label' => date('d/m', $week_start) . ' - ' . date('d/m', $week_end),
            'month' => date('M Y', $week_start)
        ];
        
        $current = strtotime('+7 days', $current);
    }
}

$xtpl = new XTemplate('gantt.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $nv_Lang);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('PROJECT', $project);

// Timeline headers
$current_month = '';
foreach ($timeline as $week) {
    $month = date('M Y', $week['start']);
    if ($month != $current_month) {
        $xtpl->assign('MONTH', ['label' => $month]);
        $xtpl->parse('main.month_header');
        $current_month = $month;
    }
}

// Timeline weeks
foreach ($timeline as $week) {
    $xtpl->assign('WEEK', $week);
    $xtpl->parse('main.week_header');
}

// Tasks rows
foreach ($tasks as $task) {
    $xtpl->assign('TASK', $task);
    
    // Calculate position and width
    if (!empty($timeline)) {
        $first_week_start = $timeline[0]['start'];
        $total_days = ($max_date - $min_date) / 86400;
        
        $task_start_offset = ($task['start_date'] - $first_week_start) / 86400;
        $task_duration = ($task['deadline'] - $task['start_date']) / 86400;
        
        $left_percent = ($task_start_offset / $total_days) * 100;
        $width_percent = ($task_duration / $total_days) * 100;
        
        $xtpl->assign('BAR', [
            'left' => $left_percent,
            'width' => $width_percent,
            'color' => $task['status_color']
        ]);
    }
    
    // Week cells
    foreach ($timeline as $week) {
        $is_in_week = ($task['start_date'] <= $week['end'] && $task['deadline'] >= $week['start']);
        
        if ($is_in_week) {
            $xtpl->parse('main.task_row.week.active');
        }
        
        $xtpl->parse('main.task_row.week');
    }
    
    $xtpl->parse('main.task_row');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
