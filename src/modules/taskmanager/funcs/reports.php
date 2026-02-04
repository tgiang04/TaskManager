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

$page_title = $nv_Lang->getModule('reports');
$key_words = $module_info['keywords'];

$report_type = $nv_Request->get_title('type', 'get', 'overview');
$project_id = $nv_Request->get_int('project_id', 'get', 0);
$date_from = $nv_Request->get_title('date_from', 'get', date('Y-m-01'));
$date_to = $nv_Request->get_title('date_to', 'get', date('Y-m-t'));

// AJAX: Export to CSV
if ($nv_Request->isset_request('export_csv', 'get')) {
    $report_data = [];
    
    switch ($report_type) {
        case 'tasks':
            $where = "1=1";
            if ($project_id > 0) {
                $where .= " AND t.project_id = " . $project_id;
            }
            
            $sql = "SELECT t.*, p.title as project_title, s.title as status_title,
                    u1.username as assignee_name, u2.username as creator_name
                    FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks t
                    LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_projects p ON t.project_id = p.id
                    LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_status s ON t.status_id = s.id
                    LEFT JOIN " . NV_USERS_GLOBALTABLE . " u1 ON t.assignee_id = u1.userid
                    LEFT JOIN " . NV_USERS_GLOBALTABLE . " u2 ON t.creator_id = u2.userid
                    WHERE " . $where;
            
            $result = $db->query($sql);
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=tasks_report_' . date('Ymd') . '.csv');
            
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            fputcsv($output, ['ID', 'Title', 'Project', 'Status', 'Priority', 'Assignee', 'Creator', 'Created', 'Deadline', 'Completed']);
            
            while ($row = $result->fetch()) {
                fputcsv($output, [
                    $row['id'],
                    $row['title'],
                    $row['project_title'],
                    $row['status_title'],
                    $row['priority'],
                    $row['assignee_name'],
                    $row['creator_name'],
                    date('Y-m-d H:i', $row['created_time']),
                    $row['deadline'] > 0 ? date('Y-m-d', $row['deadline']) : '',
                    $row['completed_time'] > 0 ? date('Y-m-d H:i', $row['completed_time']) : ''
                ]);
            }
            
            fclose($output);
            exit();
            break;
            
        case 'time':
            $where = "1=1";
            if ($project_id > 0) {
                $where .= " AND t.project_id = " . $project_id;
            }
            
            $sql = "SELECT tl.*, t.title as task_title, p.title as project_title, u.username
                    FROM " . NV_PREFIXLANG . "_" . $module_data . "_time_logs tl
                    INNER JOIN " . NV_PREFIXLANG . "_" . $module_data . "_tasks t ON tl.task_id = t.id
                    INNER JOIN " . NV_PREFIXLANG . "_" . $module_data . "_projects p ON t.project_id = p.id
                    LEFT JOIN " . NV_USERS_GLOBALTABLE . " u ON tl.user_id = u.userid
                    WHERE " . $where . " AND tl.end_time > 0";
            
            $result = $db->query($sql);
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=time_report_' . date('Ymd') . '.csv');
            
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($output, ['User', 'Project', 'Task', 'Description', 'Start', 'End', 'Duration (hours)']);
            
            while ($row = $result->fetch()) {
                $duration = ($row['end_time'] - $row['start_time']) / 3600;
                fputcsv($output, [
                    $row['username'],
                    $row['project_title'],
                    $row['task_title'],
                    $row['description'],
                    date('Y-m-d H:i', $row['start_time']),
                    date('Y-m-d H:i', $row['end_time']),
                    round($duration, 2)
                ]);
            }
            
            fclose($output);
            exit();
            break;
    }
}

// Báo cáo tổng quan
if ($report_type == 'overview') {
    $stats = [];
    
    // Thống kê theo dự án
    $where = "1=1";
    if ($project_id > 0) {
        $where .= " AND t.project_id = " . $project_id;
    }
    
    $sql = "SELECT 
            COUNT(*) as total_tasks,
            SUM(CASE WHEN s.is_completed = 1 THEN 1 ELSE 0 END) as completed_tasks,
            SUM(CASE WHEN t.deadline > 0 AND t.deadline < " . NV_CURRENTTIME . " AND s.is_completed = 0 THEN 1 ELSE 0 END) as overdue_tasks
            FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks t
            LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_status s ON t.status_id = s.id
            WHERE " . $where;
    
    $stats['tasks'] = $db->query($sql)->fetch();
    $stats['tasks']['completion_rate'] = $stats['tasks']['total_tasks'] > 0 
        ? round(($stats['tasks']['completed_tasks'] / $stats['tasks']['total_tasks']) * 100, 1) 
        : 0;
    
    // Thống kê thời gian
    $sql = "SELECT 
            COUNT(*) as total_logs,
            SUM(end_time - start_time) as total_time
            FROM " . NV_PREFIXLANG . "_" . $module_data . "_time_logs tl
            INNER JOIN " . NV_PREFIXLANG . "_" . $module_data . "_tasks t ON tl.task_id = t.id
            WHERE " . $where . " AND tl.end_time > 0";
    
    $stats['time'] = $db->query($sql)->fetch();
    $stats['time']['avg_time_per_task'] = $stats['tasks']['completed_tasks'] > 0 
        ? $stats['time']['total_time'] / $stats['tasks']['completed_tasks'] 
        : 0;
}

// Báo cáo theo người thực hiện
elseif ($report_type == 'users') {
    $users_stats = [];
    
    $where = "1=1";
    if ($project_id > 0) {
        $where .= " AND t.project_id = " . $project_id;
    }
    
    $sql = "SELECT u.userid, u.username, u.first_name, u.last_name,
            COUNT(t.id) as total_tasks,
            SUM(CASE WHEN s.is_completed = 1 THEN 1 ELSE 0 END) as completed_tasks,
            SUM(CASE WHEN t.deadline > 0 AND t.deadline < " . NV_CURRENTTIME . " AND s.is_completed = 0 THEN 1 ELSE 0 END) as overdue_tasks
            FROM " . NV_USERS_GLOBALTABLE . " u
            INNER JOIN " . NV_PREFIXLANG . "_" . $module_data . "_tasks t ON u.userid = t.assignee_id
            LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_status s ON t.status_id = s.id
            WHERE " . $where . "
            GROUP BY u.userid, u.username, u.first_name, u.last_name
            ORDER BY completed_tasks DESC";
    
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $row['completion_rate'] = $row['total_tasks'] > 0 
            ? round(($row['completed_tasks'] / $row['total_tasks']) * 100, 1) 
            : 0;
        
        // Tổng thời gian
        $sql = "SELECT SUM(end_time - start_time) as total_time
                FROM " . NV_PREFIXLANG . "_" . $module_data . "_time_logs
                WHERE user_id = " . $row['userid'] . " AND end_time > 0";
        $row['total_time'] = $db->query($sql)->fetchColumn();
        
        $users_stats[] = $row;
    }
}

// Lấy danh sách dự án
$projects = [];
$sql = "SELECT id, title FROM " . NV_PREFIXLANG . "_" . $module_data . "_projects ORDER BY title ASC";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $projects[] = $row;
}

$xtpl = new XTemplate('reports.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $nv_Lang);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('REPORT_TYPE', $report_type);

// Projects
foreach ($projects as $p) {
    $p['selected'] = $p['id'] == $project_id ? 'selected' : '';
    $xtpl->assign('PROJECT', $p);
    $xtpl->parse('main.project');
}

// Overview report
if ($report_type == 'overview' && isset($stats)) {
    $xtpl->assign('STATS', $stats['tasks']);
    $xtpl->assign('TIME_STATS', $stats['time']);
    $xtpl->parse('main.overview');
}

// Users report
if ($report_type == 'users' && isset($users_stats)) {
    foreach ($users_stats as $user) {
        $xtpl->assign('USER', $user);
        $xtpl->parse('main.users.user');
    }
    $xtpl->parse('main.users');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
