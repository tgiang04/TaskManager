<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2026 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_SYSTEM')) {
    exit('Stop!!!');
}

define('NV_IS_MOD_TASKMANAGER', true);

/**
 * nv_task_get_project()
 * 
 * @param int $project_id
 * @return array|false
 */
function nv_task_get_project($project_id)
{
    global $db, $db_config, $lang_data, $module_data;
    
    $sql = "SELECT * FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_projects 
            WHERE id = " . intval($project_id);
    $result = $db->query($sql);
    
    if ($result->rowCount()) {
        return $result->fetch();
    }
    
    return false;
}

/**
 * nv_task_get_project_members()
 * 
 * @param int $project_id
 * @return array
 */
function nv_task_get_project_members($project_id)
{
    global $db, $db_config, $lang_data, $module_data;
    
    $members = [];
    $sql = "SELECT pm.*, u.username, u.first_name, u.last_name, u.email 
            FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_project_members pm
            LEFT JOIN " . NV_USERS_GLOBALTABLE . " u ON pm.user_id = u.userid
            WHERE pm.project_id = " . intval($project_id) . "
            ORDER BY pm.role DESC, u.username ASC";
    
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $members[] = $row;
    }
    
    return $members;
}

/**
 * nv_task_check_project_permission()
 * 
 * @param int $project_id
 * @param int $user_id
 * @return bool
 */
function nv_task_check_project_permission($project_id, $user_id)
{
    global $db, $db_config, $lang_data, $module_data;
    
    // Kiểm tra xem user có phải là owner không
    $project = nv_task_get_project($project_id);
    if ($project && $project['owner_id'] == $user_id) {
        return true;
    }
    
    // Kiểm tra xem user có phải là thành viên không
    $sql = "SELECT COUNT(*) FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_project_members
            WHERE project_id = " . intval($project_id) . " AND user_id = " . intval($user_id);
    $count = $db->query($sql)->fetchColumn();
    
    if ($count > 0) {
        return true;
    }
    
    // Kiểm tra xem project có public không
    if ($project && $project['is_public']) {
        return true;
    }
    
    return false;
}

/**
 * nv_task_get_task()
 * 
 * @param int $task_id
 * @return array|false
 */
function nv_task_get_task($task_id)
{
    global $db, $db_config, $lang_data, $module_data;
    
    $sql = "SELECT t.*, 
            u1.username as creator_username, u1.first_name as creator_first_name, u1.last_name as creator_last_name,
            u2.username as assigned_username, u2.first_name as assigned_first_name, u2.last_name as assigned_last_name,
            p.title as project_title
            FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_tasks t
            LEFT JOIN " . NV_USERS_GLOBALTABLE . " u1 ON t.creator_id = u1.userid
            LEFT JOIN " . NV_USERS_GLOBALTABLE . " u2 ON t.assigned_to = u2.userid
            LEFT JOIN " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_projects p ON t.project_id = p.id
            WHERE t.id = " . intval($task_id);
    
    $result = $db->query($sql);
    
    if ($result->rowCount()) {
        return $result->fetch();
    }
    
    return false;
}

/**
 * nv_task_check_task_permission()
 * 
 * @param int $task_id
 * @param int $user_id
 * @return bool
 */
function nv_task_check_task_permission($task_id, $user_id)
{
    global $db, $db_config, $lang_data, $module_data;
    
    $task = nv_task_get_task($task_id);
    if (!$task) {
        return false;
    }
    
    // Kiểm tra xem user có phải là người tạo hoặc người được giao việc không
    if ($task['creator_id'] == $user_id || $task['assigned_to'] == $user_id) {
        return true;
    }
    
    // Kiểm tra xem user có phải là người phối hợp không
    $sql = "SELECT COUNT(*) FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_task_collaborators
            WHERE task_id = " . intval($task_id) . " AND user_id = " . intval($user_id);
    $count = $db->query($sql)->fetchColumn();
    
    if ($count > 0) {
        return true;
    }
    
    // Kiểm tra quyền với project
    if ($task['project_id']) {
        return nv_task_check_project_permission($task['project_id'], $user_id);
    }
    
    return false;
}

/**
 * nv_task_get_status_list()
 * 
 * @return array
 */
function nv_task_get_status_list()
{
    global $db, $db_config, $lang_data, $module_data;
    
    $status_list = [];
    $sql = "SELECT * FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_status 
            ORDER BY weight ASC";
    
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $status_list[$row['status_key']] = $row;
    }
    
    return $status_list;
}

/**
 * nv_task_log_history()
 * 
 * @param int $task_id
 * @param int $user_id
 * @param string $action
 * @param string $old_value
 * @param string $new_value
 * @return bool
 */
function nv_task_log_history($task_id, $user_id, $action, $old_value = '', $new_value = '')
{
    global $db, $db_config, $lang_data, $module_data;
    
    $sql = "INSERT INTO " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_history 
            (task_id, user_id, action, old_value, new_value, created_time) 
            VALUES (" . intval($task_id) . ", " . intval($user_id) . ", :action, :old_value, :new_value, " . NV_CURRENTTIME . ")";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':action', $action, PDO::PARAM_STR);
    $stmt->bindParam(':old_value', $old_value, PDO::PARAM_STR);
    $stmt->bindParam(':new_value', $new_value, PDO::PARAM_STR);
    
    return $stmt->execute();
}

/**
 * nv_task_send_notification()
 * 
 * @param int $task_id
 * @param int $user_id
 * @param string $type
 * @return bool
 */
function nv_task_send_notification($task_id, $user_id, $type = 'assigned')
{
    global $db, $db_config, $lang_data, $module_data, $module_config, $lang_module, $global_config;
    
    // Kiểm tra xem có cho phép gửi email không
    if (empty($module_config[$module_data]['enable_email'])) {
        return false;
    }
    
    $task = nv_task_get_task($task_id);
    if (!$task) {
        return false;
    }
    
    // Lấy thông tin user
    $sql = "SELECT userid, username, email, first_name, last_name FROM " . NV_USERS_GLOBALTABLE . " WHERE userid = " . intval($user_id);
    $user = $db->query($sql)->fetch();
    
    if (!$user || empty($user['email'])) {
        return false;
    }
    
    // Chuẩn bị nội dung email
    $subject = '';
    $message = '';
    
    switch ($type) {
        case 'assigned':
            $subject = sprintf($lang_module['notify_task_assigned'], $task['title']);
            $message = sprintf($lang_module['notify_task_assigned'], $task['title']);
            break;
        case 'updated':
            $subject = sprintf($lang_module['notify_task_updated'], $task['title']);
            $message = sprintf($lang_module['notify_task_updated'], $task['title']);
            break;
        case 'completed':
            $subject = sprintf($lang_module['notify_task_completed'], $task['title']);
            $message = sprintf($lang_module['notify_task_completed'], $task['title']);
            break;
        case 'deadline_warning':
            $subject = sprintf($lang_module['notify_deadline_warning'], $task['title']);
            $message = sprintf($lang_module['notify_deadline_warning'], $task['title']);
            break;
        case 'overdue':
            $subject = sprintf($lang_module['notify_deadline_overdue'], $task['title']);
            $message = sprintf($lang_module['notify_deadline_overdue'], $task['title']);
            break;
    }
    
    // Gửi email
    $from = [$global_config['site_email'], $global_config['site_name']];
    return nv_sendmail($from, $user['email'], $subject, $message);
}

/**
 * nv_task_search_users()
 * 
 * @param string $keyword
 * @param int $limit
 * @return array
 */
function nv_task_search_users($keyword, $limit = 10)
{
    global $db;
    
    $users = [];
    $keyword = $db->dblikeescape($keyword);
    
    $sql = "SELECT userid, username, first_name, last_name, email 
            FROM " . NV_USERS_GLOBALTABLE . " 
            WHERE username LIKE '%" . $keyword . "%' 
            OR first_name LIKE '%" . $keyword . "%' 
            OR last_name LIKE '%" . $keyword . "%'
            ORDER BY username ASC
            LIMIT " . intval($limit);
    
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $row['full_name'] = $row['first_name'] . ' ' . $row['last_name'];
        $users[] = $row;
    }
    
    return $users;
}

/**
 * nv_task_format_date()
 * 
 * @param int $timestamp
 * @param string $format
 * @return string
 */
function nv_task_format_date($timestamp, $format = 'd/m/Y H:i')
{
    if (empty($timestamp)) {
        return '';
    }
    
    return date($format, $timestamp);
}

/**
 * nv_task_get_custom_fields()
 * 
 * @return array
 */
function nv_task_get_custom_fields()
{
    global $db, $db_config, $lang_data, $module_data;
    
    $fields = [];
    $sql = "SELECT * FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_custom_fields 
            WHERE status = 1 
            ORDER BY weight ASC";
    
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $fields[] = $row;
    }
    
    return $fields;
}

/**
 * nv_task_get_custom_values()
 * 
 * @param int $task_id
 * @return array
 */
function nv_task_get_custom_values($task_id)
{
    global $db, $db_config, $lang_data, $module_data;
    
    $values = [];
    $sql = "SELECT * FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_custom_values 
            WHERE task_id = " . intval($task_id);
    
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $values[$row['field_id']] = $row['field_value'];
    }
    
    return $values;
}
