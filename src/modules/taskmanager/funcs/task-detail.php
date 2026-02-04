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

$task = nv_task_get_task($id);
if (!$task) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
}

// Kiểm tra quyền truy cập
if (!nv_task_check_task_permission($id, $user_info['userid'])) {
    nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_module['error_permission_denied']);
}

$page_title = $task['title'];
$key_words = $module_info['keywords'];

// Xử lý thêm bình luận
if ($nv_Request->isset_request('add_comment', 'post')) {
    $content = $nv_Request->get_editor('comment_content', '', 'post');
    $parent_id = $nv_Request->get_int('parent_id', 'post', 0);
    
    if (!empty($content)) {
        $sql = "INSERT INTO " . NV_PREFIXLANG . "_taskmanager_comments 
                (task_id, user_id, parent_id, content, created_time, updated_time) 
                VALUES (" . $id . ", " . $user_info['userid'] . ", " . $parent_id . ", :content, " . NV_CURRENTTIME . ", " . NV_CURRENTTIME . ")";
        
        $sth = $db->prepare($sql);
        $sth->bindParam(':content', $content, PDO::PARAM_STR);
        
        if ($sth->execute()) {
            // Log lịch sử
            nv_task_log_history($id, $user_info['userid'], 'comment_added', '', $content);
            
            nv_jsonOutput([
                'status' => 'OK',
                'message' => $lang_module['comment_posted']
            ]);
        }
    }
    
    nv_jsonOutput([
        'status' => 'error',
        'message' => $lang_module['error']
    ]);
}

// Xử lý upload file
if ($nv_Request->isset_request('upload_file', 'post')) {
    if (isset($_FILES['attachment_file']) && $_FILES['attachment_file']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = NV_UPLOADS_REAL_DIR . '/' . $module_name . '/tasks/' . $id . '/';
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = $_FILES['attachment_file']['name'];
        $file_size = $_FILES['attachment_file']['size'];
        $file_tmp = $_FILES['attachment_file']['tmp_name'];
        $file_type = $_FILES['attachment_file']['type'];
        
        // Kiểm tra kích thước file (max 10MB)
        if ($file_size > 10485760) {
            nv_jsonOutput([
                'status' => 'error',
                'message' => $lang_module['error_file_too_large']
            ]);
        }
        
        // Tạo tên file unique
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_file_name = md5($file_name . time()) . '.' . $file_ext;
        $file_path = $upload_dir . $new_file_name;
        
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Lưu vào database
            $relative_path = NV_UPLOADS_DIR . '/' . $module_name . '/tasks/' . $id . '/' . $new_file_name;
            
            $sql = "INSERT INTO " . NV_PREFIXLANG . "_taskmanager_attachments 
                    (task_id, filename, filesize, filepath, mimetype, uploaded_by, uploaded_time) 
                    VALUES (" . $id . ", :filename, " . $file_size . ", :filepath, :mimetype, " . $user_info['userid'] . ", " . NV_CURRENTTIME . ")";
            
            $sth = $db->prepare($sql);
            $sth->bindParam(':filename', $file_name, PDO::PARAM_STR);
            $sth->bindParam(':filepath', $relative_path, PDO::PARAM_STR);
            $sth->bindParam(':mimetype', $file_type, PDO::PARAM_STR);
            
            if ($sth->execute()) {
                // Log lịch sử
                nv_task_log_history($id, $user_info['userid'], 'file_uploaded', '', $file_name);
                
                nv_jsonOutput([
                    'status' => 'OK',
                    'message' => $lang_module['attachment_uploaded']
                ]);
            }
        }
    }
    
    nv_jsonOutput([
        'status' => 'error',
        'message' => $lang_module['error_upload_failed']
    ]);
}

// Xử lý cập nhật trạng thái
if ($nv_Request->isset_request('update_status', 'post')) {
    $status = $nv_Request->get_title('status', 'post', '');
    
    if (!empty($status)) {
        $old_status = $task['status'];
        
        $sql = "UPDATE " . NV_PREFIXLANG . "_taskmanager_tasks 
                SET status = :status, updated_time = " . NV_CURRENTTIME . " 
                WHERE id = " . $id;
        
        $sth = $db->prepare($sql);
        $sth->bindParam(':status', $status, PDO::PARAM_STR);
        
        if ($sth->execute()) {
            // Log lịch sử
            nv_task_log_history($id, $user_info['userid'], 'status_changed', $old_status, $status);
            
            // Gửi thông báo nếu hoàn thành
            if ($status == 'completed' && $task['creator_id'] != $user_info['userid']) {
                nv_task_send_notification($id, $task['creator_id'], 'completed');
            }
            
            nv_jsonOutput([
                'status' => 'OK',
                'message' => $lang_module['task_updated']
            ]);
        }
    }
    
    nv_jsonOutput([
        'status' => 'error',
        'message' => $lang_module['error']
    ]);
}

// Xử lý cập nhật tiến độ
if ($nv_Request->isset_request('update_progress', 'post')) {
    $progress = $nv_Request->get_int('progress', 'post', 0);
    $progress = max(0, min(100, $progress));
    
    $old_progress = $task['progress'];
    
    $sql = "UPDATE " . NV_PREFIXLANG . "_taskmanager_tasks 
            SET progress = " . $progress . ", updated_time = " . NV_CURRENTTIME . " 
            WHERE id = " . $id;
    
    if ($db->exec($sql)) {
        // Log lịch sử
        nv_task_log_history($id, $user_info['userid'], 'progress_updated', $old_progress, $progress);
        
        nv_jsonOutput([
            'status' => 'OK',
            'message' => $lang_module['task_updated']
        ]);
    }
    
    nv_jsonOutput([
        'status' => 'error',
        'message' => $lang_module['error']
    ]);
}

// Lấy danh sách bình luận
$comments = [];
$sql = "SELECT c.*, u.username, u.first_name, u.last_name 
        FROM " . NV_PREFIXLANG . "_taskmanager_comments c
        LEFT JOIN " . NV_USERS_GLOBALTABLE . " u ON c.user_id = u.userid
        WHERE c.task_id = " . $id . "
        ORDER BY c.created_time ASC";

$result = $db->query($sql);
while ($row = $result->fetch()) {
    $comments[] = $row;
}

// Lấy danh sách file đính kèm
$attachments = [];
$sql = "SELECT a.*, u.username 
        FROM " . NV_PREFIXLANG . "_taskmanager_attachments a
        LEFT JOIN " . NV_USERS_GLOBALTABLE . " u ON a.uploaded_by = u.userid
        WHERE a.task_id = " . $id . "
        ORDER BY a.uploaded_time DESC";

$result = $db->query($sql);
while ($row = $result->fetch()) {
    $attachments[] = $row;
}

// Lấy lịch sử thay đổi
$history = [];
$sql = "SELECT h.*, u.username 
        FROM " . NV_PREFIXLANG . "_taskmanager_history h
        LEFT JOIN " . NV_USERS_GLOBALTABLE . " u ON h.user_id = u.userid
        WHERE h.task_id = " . $id . "
        ORDER BY h.created_time DESC
        LIMIT 20";

$result = $db->query($sql);
while ($row = $result->fetch()) {
    $history[] = $row;
}

// Lấy danh sách trạng thái
$status_list = nv_task_get_status_list();

// Lấy thông tin dự án
$project_title = '';
if ($task['project_id'] > 0) {
    $sql = "SELECT title FROM " . NV_PREFIXLANG . "_taskmanager_projects WHERE id = " . $task['project_id'];
    $project_title = $db->query($sql)->fetchColumn();
}

// Lấy thông tin người tạo và người được giao
$task['creator_username'] = '';
$task['assigned_username'] = '';

if ($task['creator_id'] > 0) {
    $sql = "SELECT username FROM " . NV_USERS_GLOBALTABLE . " WHERE userid = " . $task['creator_id'];
    $task['creator_username'] = $db->query($sql)->fetchColumn();
}

if ($task['assigned_to'] > 0) {
    $sql = "SELECT username FROM " . NV_USERS_GLOBALTABLE . " WHERE userid = " . $task['assigned_to'];
    $task['assigned_username'] = $db->query($sql)->fetchColumn();
}

// Format dates
$task['deadline_format'] = $task['deadline'] > 0 ? nv_task_format_date($task['deadline'], 'd/m/Y') : '';
$task['created_time_format'] = nv_task_format_date($task['created_time'], 'd/m/Y H:i');
$task['project_title'] = $project_title;

// Priority name
switch ($task['priority']) {
    case 'low':
        $task['priority_name'] = isset($lang_module['task_priority_low']) ? $lang_module['task_priority_low'] : 'Thấp';
        break;
    case 'medium':
        $task['priority_name'] = isset($lang_module['task_priority_medium']) ? $lang_module['task_priority_medium'] : 'Trung bình';
        break;
    case 'high':
        $task['priority_name'] = isset($lang_module['task_priority_high']) ? $lang_module['task_priority_high'] : 'Cao';
        break;
    case 'urgent':
        $task['priority_name'] = isset($lang_module['task_priority_urgent']) ? $lang_module['task_priority_urgent'] : 'Khẩn cấp';
        break;
    default:
        $task['priority_name'] = $task['priority'];
}

$task['status_name'] = isset($status_list[$task['status']]) ? $status_list[$task['status']]['status_name'] : $task['status'];
$task['status_color'] = isset($status_list[$task['status']]) ? $status_list[$task['status']]['color'] : '#6c757d';

$xtpl = new XTemplate('task_detail.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('TASK', $task);
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
$xtpl->assign('TEMPLATE', $global_config['module_theme']);
$xtpl->assign('MODULE_FILE', $module_file);
$xtpl->assign('COMMENT_COUNT', count($comments));
$xtpl->assign('ATTACHMENT_COUNT', count($attachments));

// Hiển thị nút actions nếu user có quyền
if (defined('NV_IS_USER') && ($task['creator_id'] == $user_info['userid'] || $task['assigned_to'] == $user_info['userid'])) {
    $xtpl->parse('main.task_actions');
}

// Hiển thị các trạng thái
foreach ($status_list as $status_key => $status) {
    $status['selected'] = $task['status'] == $status_key ? ' selected="selected"' : '';
    $xtpl->assign('STATUS', $status);
    $xtpl->parse('main.status_option');
}

// Hiển thị bình luận
if (!empty($comments)) {
    foreach ($comments as $comment) {
        $comment['full_name'] = $comment['first_name'] . ' ' . $comment['last_name'];
        $comment['time_format'] = nv_task_format_date($comment['created_time'], 'd/m/Y H:i');
        
        $xtpl->assign('COMMENT', $comment);
        $xtpl->parse('main.comment');
    }
} else {
    $xtpl->parse('main.no_comments');
}

// Hiển thị file đính kèm
if (!empty($attachments)) {
    foreach ($attachments as $attachment) {
        $attachment['filesize_format'] = nv_convertfromBytes($attachment['filesize']);
        $attachment['time_format'] = nv_task_format_date($attachment['uploaded_time'], 'd/m/Y H:i');
        
        $xtpl->assign('ATTACHMENT', $attachment);
        $xtpl->parse('main.attachment');
    }
} else {
    $xtpl->parse('main.no_attachments');
}

// Hiển thị nút upload nếu có quyền
if (defined('NV_IS_USER') && ($task['creator_id'] == $user_info['userid'] || $task['assigned_to'] == $user_info['userid'])) {
    $xtpl->parse('main.upload_button');
    $xtpl->parse('main.upload_modal');
}

// Hiển thị lịch sử
if (!empty($history)) {
    foreach ($history as $item) {
        $item['time_format'] = nv_task_format_date($item['created_time'], 'd/m/Y H:i');
        
        $xtpl->assign('HISTORY', $item);
        $xtpl->parse('main.history_item');
    }
} else {
    $xtpl->parse('main.no_history');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
