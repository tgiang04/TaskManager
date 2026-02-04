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

$page_title = $nv_Lang->getModule('kanban_board');
$key_words = $module_info['keywords'];

$project_id = $nv_Request->get_int('project_id', 'get', 0);

// AJAX: Move task
if ($nv_Request->isset_request('move_task', 'post')) {
    $task_id = $nv_Request->get_int('task_id', 'post', 0);
    $status_id = $nv_Request->get_int('status_id', 'post', 0);
    $position = $nv_Request->get_int('position', 'post', 0);
    
    // Kiểm tra quyền
    if (!nv_task_check_project_permission_by_task($task_id, $user_info['userid'])) {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $nv_Lang->getModule('error_permission_denied')
        ]);
    }
    
    // Cập nhật trạng thái
    $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_tasks
            SET status_id = " . $status_id . ",
                kanban_position = " . $position . "
            WHERE id = " . $task_id;
    
    if ($db->exec($sql)) {
        // Ghi log
        $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_history
                (task_id, user_id, action, created_time)
                VALUES (
                    " . $task_id . ",
                    " . $user_info['userid'] . ",
                    'status_changed',
                    " . NV_CURRENTTIME . "
                )";
        $db->query($sql);
        
        nv_jsonOutput([
            'status' => 'success',
            'message' => $nv_Lang->getModule('task_moved')
        ]);
    } else {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $nv_Lang->getModule('error_occurred')
        ]);
    }
}

// Lấy danh sách dự án
$projects = [];
$sql = "SELECT DISTINCT p.*
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_projects p
        LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_project_members pm ON p.id = pm.project_id
        WHERE p.owner_id = " . $user_info['userid'] . "
        OR pm.user_id = " . $user_info['userid'] . "
        OR p.is_public = 1
        ORDER BY p.title ASC";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $projects[] = $row;
}

// Nếu có project_id, kiểm tra quyền
if ($project_id > 0) {
    if (!nv_task_check_project_permission($project_id, $user_info['userid'])) {
        nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
    }
}

// Lấy danh sách trạng thái
$statuses = [];
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_status 
        ORDER BY weight ASC";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $row['tasks'] = [];
    $statuses[$row['id']] = $row;
}

// Lấy danh sách công việc
$where = "1=1";
if ($project_id > 0) {
    $where .= " AND t.project_id = " . $project_id;
} else {
    // Chỉ hiển thị công việc của user
    $where .= " AND (t.assignee_id = " . $user_info['userid'] . " 
                OR t.creator_id = " . $user_info['userid'] . ")";
}

$sql = "SELECT t.*, p.title as project_title, p.color as project_color,
        u.username as assignee_username, u.first_name, u.last_name
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks t
        INNER JOIN " . NV_PREFIXLANG . "_" . $module_data . "_projects p ON t.project_id = p.id
        LEFT JOIN " . NV_USERS_GLOBALTABLE . " u ON t.assignee_id = u.userid
        WHERE " . $where . "
        ORDER BY t.kanban_position ASC, t.created_time DESC";

$result = $db->query($sql);
while ($row = $result->fetch()) {
    if (isset($statuses[$row['status_id']])) {
        $row['assignee_name'] = $row['first_name'] . ' ' . $row['last_name'];
        $row['deadline_formatted'] = $row['deadline'] > 0 ? date('d/m/Y', $row['deadline']) : '';
        $row['is_overdue'] = $row['deadline'] > 0 && $row['deadline'] < NV_CURRENTTIME;
        $statuses[$row['status_id']]['tasks'][] = $row;
    }
}

$xtpl = new XTemplate('kanban.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $nv_Lang);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('PROJECT_ID', $project_id);

// Projects selector
foreach ($projects as $p) {
    $p['selected'] = $p['id'] == $project_id ? 'selected' : '';
    $xtpl->assign('PROJECT', $p);
    $xtpl->parse('main.project');
}

// Kanban columns
foreach ($statuses as $status) {
    $xtpl->assign('STATUS', $status);
    
    // Tasks in column
    foreach ($status['tasks'] as $task) {
        $xtpl->assign('TASK', $task);
        
        if ($task['is_overdue']) {
            $xtpl->parse('main.column.task.overdue');
        }
        
        $xtpl->parse('main.column.task');
    }
    
    $xtpl->parse('main.column');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
