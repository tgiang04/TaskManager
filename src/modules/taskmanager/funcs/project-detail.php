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

if (!defined('NV_IS_USER')) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login');
}

$id = $nv_Request->get_int('id', 'get', 0);

$project = nv_task_get_project($id);
if (!$project) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

// Kiểm tra quyền truy cập
if (!nv_task_check_project_permission($id, $user_info['userid'])) {
    nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_module['error_permission_denied']);
}

$page_title = $project['title'];
$key_words = $module_info['keywords'];

// Lấy thành viên dự án
$members = nv_task_get_project_members($id);

// Lấy danh sách công việc
$tasks = [];
$sql = "SELECT t.*, 
        u1.username as creator_username,
        u2.username as assigned_username
        FROM " . NV_PREFIXLANG . "_taskmanager_tasks t
        LEFT JOIN " . NV_USERS_GLOBALTABLE . " u1 ON t.creator_id = u1.userid
        LEFT JOIN " . NV_USERS_GLOBALTABLE . " u2 ON t.assigned_to = u2.userid
        WHERE t.project_id = " . $id . "
        ORDER BY t.weight ASC, t.created_time DESC";

$result = $db->query($sql);
while ($row = $result->fetch()) {
    $tasks[] = $row;
}

// Thống kê
$stats = [];
$stats['total_tasks'] = count($tasks);
$stats['completed_tasks'] = 0;
$stats['in_progress_tasks'] = 0;
$stats['overdue_tasks'] = 0;

foreach ($tasks as $task) {
    if ($task['status'] == 'completed') {
        $stats['completed_tasks']++;
    } elseif ($task['status'] == 'in_progress') {
        $stats['in_progress_tasks']++;
    }
    
    if ($task['deadline'] > 0 && $task['deadline'] < NV_CURRENTTIME && !in_array($task['status'], ['completed', 'cancelled'])) {
        $stats['overdue_tasks']++;
    }
}

$stats['progress'] = $stats['total_tasks'] > 0 ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100) : 0;

$xtpl = new XTemplate('project_detail.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('PROJECT', $project);
$xtpl->assign('STATS', $stats);

// Hiển thị thành viên
if (!empty($members)) {
    foreach ($members as $member) {
        $member['full_name'] = $member['first_name'] . ' ' . $member['last_name'];
        $xtpl->assign('MEMBER', $member);
        $xtpl->parse('main.member');
    }
}

// Hiển thị công việc
if (!empty($tasks)) {
    $status_list = nv_task_get_status_list();
    
    foreach ($tasks as $task) {
        $task['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=task-detail&amp;id=' . $task['id'];
        $task['status_name'] = isset($status_list[$task['status']]) ? $status_list[$task['status']]['status_name'] : $task['status'];
        $task['status_color'] = isset($status_list[$task['status']]) ? $status_list[$task['status']]['color'] : '#6c757d';
        $task['deadline_format'] = nv_task_format_date($task['deadline'], 'd/m/Y');
        
        $xtpl->assign('TASK', $task);
        $xtpl->parse('main.task');
    }
} else {
    $xtpl->parse('main.no_tasks');
}

if ($project['owner_id'] == $user_info['userid']) {
    $xtpl->parse('main.is_owner');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
