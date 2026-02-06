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
 * @param bool $use_cache
 * @return array|false
 */
function nv_task_get_project($project_id, $use_cache = true)
{
    global $db, $db_config, $lang_data, $module_data, $nv_Cache;
    
    $cacheFile = 'project_' . $project_id . '_' . NV_CACHE_PREFIX . '.cache';
    $cacheTTL = 3600; // 1 hour
    
    if ($use_cache && ($cache = $nv_Cache->getItem($module_data, $cacheFile, ttl: $cacheTTL)) != false) {
        return unserialize($cache);
    }
    
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_projects 
            WHERE id = " . intval($project_id);
    $result = $db->query($sql);
    
    if ($result->rowCount()) {
        $project = $result->fetch();
        
        // Lưu vào cache
        if ($use_cache) {
            $nv_Cache->setItem($module_data, $cacheFile, serialize($project), ttl: $cacheTTL);
        }
        
        return $project;
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
            FROM " . NV_PREFIXLANG . "_" . $module_data . "_project_members pm
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
    $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_project_members
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
 * @param bool $use_cache
 * @return array|false
 */
function nv_task_get_task($task_id, $use_cache = true)
{
    global $db, $db_config, $lang_data, $module_data, $nv_Cache;
    
    $cacheFile = 'task_' . $task_id . '_' . NV_CACHE_PREFIX . '.cache';
    $cacheTTL = 1800; // 30 minutes
    
    if ($use_cache && ($cache = $nv_Cache->getItem($module_data, $cacheFile, ttl: $cacheTTL)) != false) {
        return unserialize($cache);
    }
    
    $sql = "SELECT t.*, 
            u1.username as creator_username, u1.first_name as creator_first_name, u1.last_name as creator_last_name,
            u2.username as assigned_username, u2.first_name as assigned_first_name, u2.last_name as assigned_last_name,
            p.title as project_title
            FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks t
            LEFT JOIN " . NV_USERS_GLOBALTABLE . " u1 ON t.creator_id = u1.userid
            LEFT JOIN " . NV_USERS_GLOBALTABLE . " u2 ON t.assigned_to = u2.userid
            LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_projects p ON t.project_id = p.id
            WHERE t.id = " . intval($task_id);
    
    $result = $db->query($sql);
    
    if ($result->rowCount()) {
        $task = $result->fetch();
        
        // Lưu vào cache
        if ($use_cache) {
            $nv_Cache->setItem($module_data, $cacheFile, serialize($task), ttl: $cacheTTL);
        }
        
        return $task;
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
    $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_task_collaborators
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
 * @param bool $use_cache
 * @return array
 */
function nv_task_get_status_list($use_cache = true)
{
    global $db, $db_config, $lang_data, $module_data, $nv_Cache;
    
    $cacheFile = 'status_list_' . NV_CACHE_PREFIX . '.cache';
    $cacheTTL = 7200; // 2 hours
    
    if ($use_cache && ($cache = $nv_Cache->getItem($module_data, $cacheFile, ttl: $cacheTTL)) != false) {
        return unserialize($cache);
    }
    
    $status_list = [];
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_status 
            ORDER BY weight ASC";
    
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $status_list[$row['status_key']] = $row;
    }
    
    // Lưu vào cache
    if ($use_cache) {
        $nv_Cache->setItem($module_data, $cacheFile, serialize($status_list), ttl: $cacheTTL);
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
    
    $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_history 
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
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_custom_fields 
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
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_custom_values 
            WHERE task_id = " . intval($task_id);
    
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $values[$row['field_id']] = $row['field_value'];
    }
    
    return $values;
}

/**
 * nv_task_check_project_permission_by_task()
 * 
 * @param int $task_id
 * @param int $user_id
 * @return bool
 */
function nv_task_check_project_permission_by_task($task_id, $user_id)
{
    global $db, $db_config, $lang_data, $module_data;
    
    $sql = "SELECT project_id FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks 
            WHERE id = " . intval($task_id);
    $project_id = $db->query($sql)->fetchColumn();
    
    if ($project_id) {
        return nv_task_check_project_permission($project_id, $user_id);
    }
    
    return false;
}

/**
 * nv_format_duration()
 * Format duration in seconds to human readable format
 * 
 * @param int $seconds
 * @return string
 */
function nv_format_duration($seconds)
{
    if ($seconds <= 0) {
        return '0 phút';
    }
    
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    
    $parts = [];
    if ($hours > 0) {
        $parts[] = $hours . ' giờ';
    }
    if ($minutes > 0) {
        $parts[] = $minutes . ' phút';
    }
    
    return implode(' ', $parts);
}

/**
 * nv_task_get_dependencies()
 * Get task dependencies
 * 
 * @param int $task_id
 * @return array
 */
function nv_task_get_dependencies($task_id)
{
    global $db, $db_config, $lang_data, $module_data;
    
    $dependencies = [];
    $sql = "SELECT td.*, t.title as task_title
            FROM " . NV_PREFIXLANG . "_" . $module_data . "_task_dependencies td
            INNER JOIN " . NV_PREFIXLANG . "_" . $module_data . "_tasks t ON td.dependency_task_id = t.id
            WHERE td.task_id = " . intval($task_id);
    
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $dependencies[] = $row;
    }
    
    return $dependencies;
}

/**
 * nv_task_add_dependency()
 * Add task dependency
 * 
 * @param int $task_id
 * @param int $dependency_task_id
 * @param string $type (finish_to_start, start_to_start, finish_to_finish, start_to_finish)
 * @return bool
 */
function nv_task_add_dependency($task_id, $dependency_task_id, $type = 'finish_to_start')
{
    global $db, $db_config, $lang_data, $module_data;
    
    // Kiểm tra circular dependency
    if (nv_task_has_circular_dependency($task_id, $dependency_task_id)) {
        return false;
    }
    
    $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_task_dependencies
            (task_id, dependency_task_id, dependency_type)
            VALUES (" . intval($task_id) . ", " . intval($dependency_task_id) . ", " . $db->quote($type) . ")";
    
    return $db->query($sql) ? true : false;
}

/**
 * nv_task_has_circular_dependency()
 * Check if adding a dependency would create a circular reference
 * 
 * @param int $task_id
 * @param int $dependency_task_id
 * @return bool
 */
function nv_task_has_circular_dependency($task_id, $dependency_task_id)
{
    global $db, $db_config, $lang_data, $module_data;
    
    // Nếu task_id == dependency_task_id
    if ($task_id == $dependency_task_id) {
        return true;
    }
    
    // Kiểm tra xem dependency_task_id có phụ thuộc vào task_id không (recursive)
    $checked = [];
    $to_check = [$dependency_task_id];
    
    while (!empty($to_check)) {
        $current_id = array_shift($to_check);
        
        if (in_array($current_id, $checked)) {
            continue;
        }
        
        $checked[] = $current_id;
        
        $sql = "SELECT dependency_task_id FROM " . NV_PREFIXLANG . "_" . $module_data . "_task_dependencies
                WHERE task_id = " . intval($current_id);
        $result = $db->query($sql);
        
        while ($row = $result->fetch()) {
            if ($row['dependency_task_id'] == $task_id) {
                return true; // Circular dependency found
            }
            $to_check[] = $row['dependency_task_id'];
        }
    }
    
    return false;
}

/**
 * nv_task_clear_project_cache()
 * Clear project cache
 * 
 * @param int $project_id
 * @return bool
 */
function nv_task_clear_project_cache($project_id)
{
    global $nv_Cache, $module_data;
    
    $cacheFile = 'project_' . $project_id . '_' . NV_CACHE_PREFIX . '.cache';
    return $nv_Cache->delItem($module_data, $cacheFile);
}

/**
 * nv_task_clear_task_cache()
 * Clear task cache
 * 
 * @param int $task_id
 * @return bool
 */
function nv_task_clear_task_cache($task_id)
{
    global $nv_Cache, $module_data;
    
    $cacheFile = 'task_' . $task_id . '_' . NV_CACHE_PREFIX . '.cache';
    return $nv_Cache->delItem($module_data, $cacheFile);
}

/**
 * nv_task_clear_status_cache()
 * Clear status list cache
 * 
 * @return bool
 */
function nv_task_clear_status_cache()
{
    global $nv_Cache, $module_data;
    
    $cacheFile = 'status_list_' . NV_CACHE_PREFIX . '.cache';
    return $nv_Cache->delItem($module_data, $cacheFile);
}

/**
 * nv_task_clear_all_cache()
 * Clear all module cache
 * 
 * @return bool
 */
function nv_task_clear_all_cache()
{
    global $nv_Cache, $module_data;
    
    // Xóa toàn bộ cache của module
    $nv_Cache->delModule($module_data);
    return true;
}

/**
 * nv_task_get_project_url()
 * Get project friendly URL
 * 
 * @param int $project_id
 * @param string $project_alias
 * @return string
 */
function nv_task_get_project_url($project_id, $project_alias = '')
{
    global $global_config, $module_name;
    
    if (empty($project_alias)) {
        $project = nv_task_get_project($project_id);
        if ($project) {
            $project_alias = !empty($project['alias']) ? $project['alias'] : change_alias($project['title']);
        } else {
            $project_alias = 'project';
        }
    }
    
    if ($global_config['rewrite_enable']) {
        return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=project/' . $project_alias . '-' . $project_id . $global_config['rewrite_exturl'];
    }
    
    return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=project-detail&amp;id=' . $project_id;
}

/**
 * nv_task_get_task_url()
 * Get task friendly URL
 * 
 * @param int $task_id
 * @param string $task_alias
 * @param string $project_alias
 * @return string
 */
function nv_task_get_task_url($task_id, $task_alias = '', $project_alias = '')
{
    global $global_config, $module_name;
    
    if (empty($task_alias)) {
        $task = nv_task_get_task($task_id);
        if ($task) {
            $task_alias = !empty($task['alias']) ? $task['alias'] : change_alias($task['title']);
            
            if (empty($project_alias) && !empty($task['project_id'])) {
                $project = nv_task_get_project($task['project_id']);
                if ($project) {
                    $project_alias = !empty($project['alias']) ? $project['alias'] : change_alias($project['title']);
                }
            }
        } else {
            $task_alias = 'task';
        }
    }
    
    if ($global_config['rewrite_enable']) {
        if (!empty($project_alias)) {
            return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=task/' . $project_alias . '/' . $task_alias . '-' . $task_id . $global_config['rewrite_exturl'];
        }
        return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=task/' . $task_alias . '-' . $task_id . $global_config['rewrite_exturl'];
    }
    
    return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=task-detail&amp;id=' . $task_id;
}

/**
 * nv_task_get_page_url()
 * Get page friendly URL
 * 
 * @param string $page
 * @param int $page_number
 * @return string
 */
function nv_task_get_page_url($page, $page_number = 0)
{
    global $global_config, $module_name;
    
    if ($global_config['rewrite_enable']) {
        if ($page_number > 1) {
            return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $page . '/page-' . $page_number . $global_config['rewrite_exturl'];
        }
        return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $page . $global_config['rewrite_exturl'];
    }
    
    if ($page_number > 1) {
        return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $page . '&amp;page=' . $page_number;
    }
    
    return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $page;
}
