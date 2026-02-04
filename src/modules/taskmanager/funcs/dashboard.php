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

$page_title = $nv_Lang->getModule('dashboard');
$key_words = $module_info['keywords'];

// Lấy thống kê tổng quan
$stats = [];

// Tổng số dự án
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_projects";
$stats['total_projects'] = $db->query($sql)->fetchColumn();

// Dự án đang hoạt động
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_projects 
        WHERE status = 'active'";
$stats['active_projects'] = $db->query($sql)->fetchColumn();

// Tổng số công việc
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks";
$stats['total_tasks'] = $db->query($sql)->fetchColumn();

// Công việc của tôi
if ($user_info['userid'] > 0) {
    $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks
            WHERE assignee_id = " . $user_info['userid'];
    $stats['my_tasks'] = $db->query($sql)->fetchColumn();
    
    // Công việc quá hạn
    $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks
            WHERE assignee_id = " . $user_info['userid'] . "
            AND deadline < " . NV_CURRENTTIME . "
            AND status_id NOT IN (SELECT id FROM " . NV_PREFIXLANG . "_" . $module_data . "_status WHERE is_completed = 1)";
    $stats['overdue_tasks'] = $db->query($sql)->fetchColumn();
    
    // Công việc hoàn thành tuần này
    $week_start = strtotime('monday this week');
    $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks
            WHERE assignee_id = " . $user_info['userid'] . "
            AND completed_time >= " . $week_start . "
            AND status_id IN (SELECT id FROM " . NV_PREFIXLANG . "_" . $module_data . "_status WHERE is_completed = 1)";
    $stats['completed_this_week'] = $db->query($sql)->fetchColumn();
}

// Thống kê theo trạng thái
$stats_by_status = [];
$sql = "SELECT s.id, s.title, s.color, COUNT(t.id) as count
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_status s
        LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_tasks t ON s.id = t.status_id";
if ($user_info['userid'] > 0) {
    $sql .= " AND t.assignee_id = " . $user_info['userid'];
}
$sql .= " GROUP BY s.id, s.title, s.color ORDER BY s.weight ASC";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $stats_by_status[] = $row;
}

// Thống kê theo độ ưu tiên
$stats_by_priority = [];
$priorities = ['low' => $nv_Lang->getModule('priority_low'), 
               'medium' => $nv_Lang->getModule('priority_medium'), 
               'high' => $nv_Lang->getModule('priority_high'),
               'urgent' => $nv_Lang->getModule('priority_urgent')];

foreach ($priorities as $key => $label) {
    $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks
            WHERE priority = " . $db->quote($key);
    if ($user_info['userid'] > 0) {
        $sql .= " AND assignee_id = " . $user_info['userid'];
    }
    $count = $db->query($sql)->fetchColumn();
    $stats_by_priority[] = [
        'priority' => $key,
        'label' => $label,
        'count' => $count
    ];
}

// Hoạt động gần đây
$recent_activities = [];
if ($user_info['userid'] > 0) {
    $sql = "SELECT h.*, t.title as task_title, p.title as project_title, u.username
            FROM " . NV_PREFIXLANG . "_" . $module_data . "_history h
            LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_tasks t ON h.task_id = t.id
            LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_projects p ON t.project_id = p.id
            LEFT JOIN " . NV_USERS_GLOBALTABLE . " u ON h.user_id = u.userid
            WHERE t.assignee_id = " . $user_info['userid'] . "
            OR h.user_id = " . $user_info['userid'] . "
            ORDER BY h.created_time DESC
            LIMIT 10";
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $recent_activities[] = $row;
    }
}

// Thống kê tiến độ theo tháng (6 tháng gần nhất)
$monthly_stats = [];
for ($i = 5; $i >= 0; $i--) {
    $month_time = strtotime("-$i month");
    $month_start = strtotime('first day of this month', $month_time);
    $month_end = strtotime('last day of this month', $month_time);
    
    $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks
            WHERE completed_time >= " . $month_start . "
            AND completed_time <= " . $month_end;
    if ($user_info['userid'] > 0) {
        $sql .= " AND assignee_id = " . $user_info['userid'];
    }
    $completed = $db->query($sql)->fetchColumn();
    
    $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks
            WHERE created_time >= " . $month_start . "
            AND created_time <= " . $month_end;
    if ($user_info['userid'] > 0) {
        $sql .= " AND assignee_id = " . $user_info['userid'];
    }
    $created = $db->query($sql)->fetchColumn();
    
    $monthly_stats[] = [
        'month' => date('M Y', $month_time),
        'completed' => $completed,
        'created' => $created
    ];
}

// Top người thực hiện
$top_performers = [];
$sql = "SELECT u.userid, u.username, u.first_name, u.last_name, 
        COUNT(t.id) as total_tasks,
        SUM(CASE WHEN s.is_completed = 1 THEN 1 ELSE 0 END) as completed_tasks
        FROM " . NV_USERS_GLOBALTABLE . " u
        INNER JOIN " . NV_PREFIXLANG . "_" . $module_data . "_tasks t ON u.userid = t.assignee_id
        LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_status s ON t.status_id = s.id
        GROUP BY u.userid, u.username, u.first_name, u.last_name
        ORDER BY completed_tasks DESC
        LIMIT 10";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $row['completion_rate'] = $row['total_tasks'] > 0 ? round(($row['completed_tasks'] / $row['total_tasks']) * 100, 1) : 0;
    $top_performers[] = $row;
}

$xtpl = new XTemplate('dashboard.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $nv_Lang);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('STATS', $stats);

// Stats by status
foreach ($stats_by_status as $stat) {
    $xtpl->assign('STATUS', $stat);
    $xtpl->parse('main.status');
}

// Stats by priority
foreach ($stats_by_priority as $stat) {
    $xtpl->assign('PRIORITY', $stat);
    $xtpl->parse('main.priority');
}

// Recent activities
foreach ($recent_activities as $activity) {
    $activity['time_ago'] = nv_time2friendlyformat($activity['created_time']);
    $xtpl->assign('ACTIVITY', $activity);
    $xtpl->parse('main.activity');
}

// Monthly stats
foreach ($monthly_stats as $stat) {
    $xtpl->assign('MONTH_STAT', $stat);
    $xtpl->parse('main.month_stat');
}

// Top performers
foreach ($top_performers as $performer) {
    $xtpl->assign('PERFORMER', $performer);
    $xtpl->parse('main.performer');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
