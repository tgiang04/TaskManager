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

if ($user_info['userid'] == 0) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt($client_info['selfurl']));
}

$page_title = $nv_Lang->getModule('time_tracking');
$key_words = $module_info['keywords'];

$task_id = $nv_Request->get_int('task_id', 'get', 0);

// AJAX: Start timer
if ($nv_Request->isset_request('start', 'post')) {
    $task_id = $nv_Request->get_int('task_id', 'post', 0);
    $description = $nv_Request->get_title('description', 'post', '');
    
    // Kiểm tra quyền
    if (!nv_task_check_project_permission_by_task($task_id, $user_info['userid'])) {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $nv_Lang->getModule('error_permission_denied')
        ]);
    }
    
    // Dừng timer đang chạy (nếu có)
    $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_time_logs
            SET end_time = " . NV_CURRENTTIME . "
            WHERE user_id = " . $user_info['userid'] . "
            AND end_time = 0";
    $db->query($sql);
    
    // Tạo timer mới
    $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_time_logs
            (task_id, user_id, description, start_time, created_time)
            VALUES (
                " . $task_id . ",
                " . $user_info['userid'] . ",
                " . $db->quote($description) . ",
                " . NV_CURRENTTIME . ",
                " . NV_CURRENTTIME . "
            )";
    
    if ($db->insert_id($sql, 'id')) {
        nv_jsonOutput([
            'status' => 'success',
            'message' => $nv_Lang->getModule('timer_started')
        ]);
    } else {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $nv_Lang->getModule('error_occurred')
        ]);
    }
}

// AJAX: Stop timer
if ($nv_Request->isset_request('stop', 'post')) {
    $log_id = $nv_Request->get_int('log_id', 'post', 0);
    
    $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_time_logs
            SET end_time = " . NV_CURRENTTIME . "
            WHERE id = " . $log_id . "
            AND user_id = " . $user_info['userid'] . "
            AND end_time = 0";
    
    if ($db->exec($sql)) {
        nv_jsonOutput([
            'status' => 'success',
            'message' => $nv_Lang->getModule('timer_stopped')
        ]);
    } else {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $nv_Lang->getModule('error_occurred')
        ]);
    }
}

// AJAX: Delete log
if ($nv_Request->isset_request('delete', 'post')) {
    $log_id = $nv_Request->get_int('log_id', 'post', 0);
    
    $sql = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_time_logs
            WHERE id = " . $log_id . "
            AND user_id = " . $user_info['userid'];
    
    if ($db->exec($sql)) {
        nv_jsonOutput([
            'status' => 'success',
            'message' => $nv_Lang->getModule('deleted')
        ]);
    } else {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $nv_Lang->getModule('error_occurred')
        ]);
    }
}

// AJAX: Update log
if ($nv_Request->isset_request('update', 'post')) {
    $log_id = $nv_Request->get_int('log_id', 'post', 0);
    $description = $nv_Request->get_title('description', 'post', '');
    $duration = $nv_Request->get_int('duration', 'post', 0); // in minutes
    
    $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_time_logs
            SET description = " . $db->quote($description) . ",
                end_time = start_time + " . ($duration * 60) . "
            WHERE id = " . $log_id . "
            AND user_id = " . $user_info['userid'];
    
    if ($db->exec($sql)) {
        nv_jsonOutput([
            'status' => 'success',
            'message' => $nv_Lang->getModule('updated')
        ]);
    } else {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $nv_Lang->getModule('error_occurred')
        ]);
    }
}

// Lấy timer đang chạy
$active_timer = null;
$sql = "SELECT tl.*, t.title as task_title, p.title as project_title
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_time_logs tl
        INNER JOIN " . NV_PREFIXLANG . "_" . $module_data . "_tasks t ON tl.task_id = t.id
        INNER JOIN " . NV_PREFIXLANG . "_" . $module_data . "_projects p ON t.project_id = p.id
        WHERE tl.user_id = " . $user_info['userid'] . "
        AND tl.end_time = 0
        LIMIT 1";
$result = $db->query($sql);
if ($result->rowCount()) {
    $active_timer = $result->fetch();
    $active_timer['elapsed'] = NV_CURRENTTIME - $active_timer['start_time'];
}

// Lọc
$filter_task = $nv_Request->get_int('filter_task', 'get', 0);
$filter_date_from = $nv_Request->get_title('date_from', 'get', '');
$filter_date_to = $nv_Request->get_title('date_to', 'get', '');

// Phân trang
$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 20;

// Đếm tổng số bản ghi
$where = "tl.user_id = " . $user_info['userid'];
if ($filter_task > 0) {
    $where .= " AND tl.task_id = " . $filter_task;
}
if (!empty($filter_date_from)) {
    $date_from = strtotime($filter_date_from);
    $where .= " AND tl.start_time >= " . $date_from;
}
if (!empty($filter_date_to)) {
    $date_to = strtotime($filter_date_to . ' 23:59:59');
    $where .= " AND tl.start_time <= " . $date_to;
}

$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_time_logs tl WHERE " . $where;
$total = $db->query($sql)->fetchColumn();

// Lấy danh sách logs
$logs = [];
$sql = "SELECT tl.*, t.title as task_title, p.title as project_title
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_time_logs tl
        INNER JOIN " . NV_PREFIXLANG . "_" . $module_data . "_tasks t ON tl.task_id = t.id
        INNER JOIN " . NV_PREFIXLANG . "_" . $module_data . "_projects p ON t.project_id = p.id
        WHERE " . $where . "
        ORDER BY tl.start_time DESC
        LIMIT " . (($page - 1) * $per_page) . ", " . $per_page;

$result = $db->query($sql);
while ($row = $result->fetch()) {
    $row['duration'] = $row['end_time'] > 0 ? ($row['end_time'] - $row['start_time']) : 0;
    $row['duration_formatted'] = nv_format_duration($row['duration']);
    $row['start_time_formatted'] = date('d/m/Y H:i', $row['start_time']);
    $logs[] = $row;
}

// Tổng thời gian
$sql = "SELECT SUM(end_time - start_time) as total_time
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_time_logs tl
        WHERE " . $where . " AND end_time > 0";
$total_time = $db->query($sql)->fetchColumn();

// Lấy danh sách tasks của user
$my_tasks = [];
$sql = "SELECT t.id, t.title, p.title as project_title
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks t
        INNER JOIN " . NV_PREFIXLANG . "_" . $module_data . "_projects p ON t.project_id = p.id
        WHERE t.assignee_id = " . $user_info['userid'] . "
        ORDER BY t.created_time DESC";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $my_tasks[$row['id']] = $row['project_title'] . ' - ' . $row['title'];
}

// Generate page
$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=time-tracking';
$generate_page = nv_alias_page($page_title, $base_url, $total, $per_page, $page);

$xtpl = new XTemplate('time-tracking.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $nv_Lang);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('TOTAL_TIME', nv_format_duration($total_time));
$xtpl->assign('GENERATE_PAGE', $generate_page);

// Active timer
if ($active_timer) {
    $xtpl->assign('TIMER', $active_timer);
    $xtpl->parse('main.active_timer');
}

// My tasks for selector
foreach ($my_tasks as $tid => $ttitle) {
    $xtpl->assign('TASK', ['id' => $tid, 'title' => $ttitle]);
    $xtpl->parse('main.task_option');
}

// Logs
foreach ($logs as $log) {
    $xtpl->assign('LOG', $log);
    $xtpl->parse('main.log');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
