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

$page_title = $module_info['site_title'];
$key_words = $module_info['keywords'];

// Lấy thống kê tổng quan
$stats = [];

// Tổng số dự án
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_taskmanager_projects";
$stats['total_projects'] = $db->query($sql)->fetchColumn();

// Tổng số công việc
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_taskmanager_tasks";
$stats['total_tasks'] = $db->query($sql)->fetchColumn();

// Công việc đã hoàn thành
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_taskmanager_tasks WHERE status = 'completed'";
$stats['completed_tasks'] = $db->query($sql)->fetchColumn();

// Công việc đang thực hiện
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_taskmanager_tasks WHERE status = 'in_progress'";
$stats['in_progress_tasks'] = $db->query($sql)->fetchColumn();

// Công việc quá hạn
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_taskmanager_tasks 
        WHERE deadline > 0 AND deadline < " . NV_CURRENTTIME . " AND status NOT IN ('completed', 'cancelled')";
$stats['overdue_tasks'] = $db->query($sql)->fetchColumn();

// Công việc của tôi
if (defined('NV_IS_USER')) {
    $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_taskmanager_tasks 
            WHERE assigned_to = " . $user_info['userid'] . " AND status NOT IN ('completed', 'cancelled')";
    $stats['my_tasks'] = $db->query($sql)->fetchColumn();
} else {
    $stats['my_tasks'] = 0;
}

// Dự án gần đây
$recent_projects = [];
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_taskmanager_projects 
        WHERE is_public = 1 OR owner_id = " . (defined('NV_IS_USER') ? $user_info['userid'] : 0) . "
        ORDER BY created_time DESC 
        LIMIT 5";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $recent_projects[] = $row;
}

// Công việc gần đây
$recent_tasks = [];
if (defined('NV_IS_USER')) {
    $sql = "SELECT t.*, p.title as project_title 
            FROM " . NV_PREFIXLANG . "_taskmanager_tasks t
        LEFT JOIN " . NV_PREFIXLANG . "_taskmanager_projects p ON t.project_id = p.id
            WHERE t.assigned_to = " . $user_info['userid'] . "
            ORDER BY t.created_time DESC 
            LIMIT 10";
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $recent_tasks[] = $row;
    }
}

$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('STATS', $stats);
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
$xtpl->assign('TEMPLATE', $global_config['module_theme']);
$xtpl->assign('MODULE_FILE', $module_file);
$xtpl->assign('BASE_URL', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);

if (defined('NV_IS_USER')) {
    $xtpl->parse('main.quick_actions');
}

// Hiển thị dự án gần đây
if (!empty($recent_projects)) {
    foreach ($recent_projects as $project) {
        $project['link'] = nv_task_get_project_url($project['id'], $project['alias'] ?? '');
        // Lấy số lượng công việc
        $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_taskmanager_tasks WHERE project_id = " . $project['id'];
        $project['total_tasks'] = $db->query($sql)->fetchColumn();
        $project['created_time_format'] = nv_task_format_date($project['created_time'], 'd/m/Y');
        
        $xtpl->assign('PROJECT', $project);
        $xtpl->parse('main.project');
    }
} else {
    if (defined('NV_IS_USER')) {
        $xtpl->parse('main.no_projects.add_project_link');
    }
    $xtpl->parse('main.no_projects');
}

// Hiển thị công việc gần đây
if (!empty($recent_tasks)) {
    $status_list = nv_task_get_status_list();
    
    foreach ($recent_tasks as $task) {
        $task['link'] = nv_task_get_task_url($task['id'], $task['alias'] ?? '', '');
        $task['status_name'] = isset($status_list[$task['status']]) ? $status_list[$task['status']]['status_name'] : $task['status'];
        $task['status_color'] = isset($status_list[$task['status']]) ? $status_list[$task['status']]['color'] : '#6c757d';
        $task['priority_class'] = '';
        $task['priority'] = isset($task['priority']) ? $task['priority'] : 'medium';
        
        switch ($task['priority']) {
            case 'low':
                $task['priority_class'] = 'low';
                $task['priority_name'] = isset($lang_module['task_priority_low']) ? $lang_module['task_priority_low'] : 'Thấp';
                break;
            case 'medium':
                $task['priority_class'] = 'medium';
                $task['priority_name'] = isset($lang_module['task_priority_medium']) ? $lang_module['task_priority_medium'] : 'Trung bình';
                break;
            case 'high':
                $task['priority_class'] = 'high';
                $task['priority_name'] = isset($lang_module['task_priority_high']) ? $lang_module['task_priority_high'] : 'Cao';
                break;
            case 'urgent':
                $task['priority_class'] = 'high';
                $task['priority_name'] = isset($lang_module['task_priority_urgent']) ? $lang_module['task_priority_urgent'] : 'Khẩn cấp';
                break;
            default:
                $task['priority_class'] = 'medium';
                $task['priority_name'] = $task['priority'];
        }
        
        $xtpl->assign('TASK', $task);
        $xtpl->parse('main.task');
    }
} else {
    $xtpl->parse('main.no_tasks');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
