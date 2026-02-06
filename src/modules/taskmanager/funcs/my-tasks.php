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

$page_title = $lang_module['my_tasks'];
$key_words = $module_info['keywords'];

$per_page = isset($module_config[$module_name]['per_page']) ? $module_config[$module_name]['per_page'] : 20;
$page = $nv_Request->get_int('page', 'get', 1);

// Lấy danh sách công việc của user
$sql = "SELECT t.*, p.title as project_title,
        u1.username as creator_username
        FROM " . NV_PREFIXLANG . "_taskmanager_tasks t
        LEFT JOIN " . NV_PREFIXLANG . "_taskmanager_projects p ON t.project_id = p.id
        LEFT JOIN " . NV_USERS_GLOBALTABLE . " u1 ON t.creator_id = u1.userid
        WHERE t.assigned_to = " . $user_info['userid'] . "
        ORDER BY t.deadline ASC, t.created_time DESC";

$result = $db->query($sql);
$total = $result->rowCount();

$tasks = [];
$result = $db->query($sql . " LIMIT " . (($page - 1) * $per_page) . ", " . $per_page);
while ($row = $result->fetch()) {
    $tasks[] = $row;
}

$xtpl = new XTemplate('my_tasks.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);

if (!empty($tasks)) {
    $status_list = nv_task_get_status_list();
    
    foreach ($tasks as $task) {
        $task['link'] = nv_task_get_task_url($task['id'], $task['alias'] ?? '', '');
        $task['status_name'] = isset($status_list[$task['status']]) ? $status_list[$task['status']]['status_name'] : $task['status'];
        $task['status_color'] = isset($status_list[$task['status']]) ? $status_list[$task['status']]['color'] : '#6c757d';
        $task['deadline_format'] = nv_task_format_date($task['deadline'], 'd/m/Y H:i');
        
        // Kiểm tra overdue
        if ($task['deadline'] > 0 && $task['deadline'] < NV_CURRENTTIME && !in_array($task['status'], ['completed', 'cancelled'])) {
            $task['is_overdue'] = true;
            $xtpl->parse('main.task.overdue');
        } elseif ($task['deadline'] > 0 && $task['deadline'] < (NV_CURRENTTIME + 86400 * (isset($module_config[$module_name]['deadline_warning_days']) ? $module_config[$module_name]['deadline_warning_days'] : 3))) {
            $task['is_due_soon'] = true;
            $xtpl->parse('main.task.due_soon');
        }
        
        $xtpl->assign('TASK', $task);
        $xtpl->parse('main.task');
    }
    
    // Phân trang
    if ($total > $per_page) {
        $base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op;
        $generate_page = nv_generate_page($base_url, $total, $per_page, $page);
        if (!empty($generate_page)) {
            $xtpl->assign('GENERATE_PAGE', $generate_page);
            $xtpl->parse('main.generate_page');
        }
    }
} else {
    $xtpl->parse('main.empty');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
