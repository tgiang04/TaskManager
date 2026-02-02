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

$page_title = $lang_module['projects'];
$key_words = $module_info['keywords'];

$per_page = $module_config[$module_name]['per_page'];
$page = $nv_Request->get_int('page', 'get', 1);

// Xử lý thêm/sửa dự án
if ($nv_Request->isset_request('save', 'post') && defined('NV_IS_USER')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $title = $nv_Request->get_title('title', 'post', '');
    $description = $nv_Request->get_editor('description', '', 'post');
    $start_date = $nv_Request->get_title('start_date', 'post', '');
    $end_date = $nv_Request->get_title('end_date', 'post', '');
    $is_public = $nv_Request->get_int('is_public', 'post', 0);
    
    if (empty($title)) {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $lang_module['error_required_fields']
        ]);
    }
    
    $start_timestamp = !empty($start_date) ? strtotime($start_date) : 0;
    $end_timestamp = !empty($end_date) ? strtotime($end_date) : 0;
    
    if ($id > 0) {
        // Kiểm tra quyền sửa
        $project = nv_task_get_project($id);
        if (!$project || $project['owner_id'] != $user_info['userid']) {
            nv_jsonOutput([
                'status' => 'error',
                'message' => $lang_module['error_permission_denied']
            ]);
        }
        
        // Cập nhật
        $sql = "UPDATE " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_projects SET 
                title = :title,
                description = :description,
                start_date = :start_date,
                end_date = :end_date,
                is_public = :is_public,
                updated_time = " . NV_CURRENTTIME . "
                WHERE id = " . $id;
    } else {
        // Thêm mới
        $sql = "INSERT INTO " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_projects 
                (title, description, start_date, end_date, is_public, owner_id, created_time, updated_time) 
                VALUES (:title, :description, :start_date, :end_date, :is_public, " . $user_info['userid'] . ", " . NV_CURRENTTIME . ", " . NV_CURRENTTIME . ")";
    }
    
    $sth = $db->prepare($sql);
    $sth->bindParam(':title', $title, PDO::PARAM_STR);
    $sth->bindParam(':description', $description, PDO::PARAM_STR);
    $sth->bindParam(':start_date', $start_timestamp, PDO::PARAM_INT);
    $sth->bindParam(':end_date', $end_timestamp, PDO::PARAM_INT);
    $sth->bindParam(':is_public', $is_public, PDO::PARAM_INT);
    
    if ($sth->execute()) {
        if ($id == 0) {
            $id = $db->lastInsertId();
            // Tự động thêm owner làm thành viên
            $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_project_members 
                       (project_id, user_id, role, added_time) VALUES (" . $id . ", " . $user_info['userid'] . ", 'owner', " . NV_CURRENTTIME . ")");
        }
        
        nv_jsonOutput([
            'status' => 'OK',
            'message' => $lang_module['project_created'],
            'redirect' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=project-detail&id=' . $id
        ]);
    }
    
    nv_jsonOutput([
        'status' => 'error',
        'message' => $lang_module['error']
    ]);
}

// Xử lý xóa dự án
if ($nv_Request->isset_request('delete', 'post') && defined('NV_IS_USER')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    
    $project = nv_task_get_project($id);
    if ($project && $project['owner_id'] == $user_info['userid']) {
        // Xóa thành viên
        $db->query("DELETE FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_project_members WHERE project_id = " . $id);
        
        // Xóa dự án
        $db->query("DELETE FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_projects WHERE id = " . $id);
        
        nv_jsonOutput([
            'status' => 'OK',
            'message' => $lang_module['project_deleted']
        ]);
    }
    
    nv_jsonOutput([
        'status' => 'error',
        'message' => $lang_module['error_permission_denied']
    ]);
}

// Lấy danh sách dự án
$where = [];
if (defined('NV_IS_USER')) {
    $where[] = "(p.is_public = 1 OR p.owner_id = " . $user_info['userid'] . " OR pm.user_id = " . $user_info['userid'] . ")";
} else {
    $where[] = "p.is_public = 1";
}

$sql = "SELECT p.*, COUNT(DISTINCT t.id) as total_tasks 
        FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_projects p
        LEFT JOIN " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_project_members pm ON p.id = pm.project_id
        LEFT JOIN " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_tasks t ON p.id = t.project_id
        " . (!empty($where) ? " WHERE " . implode(' AND ', $where) : "") . "
        GROUP BY p.id
        ORDER BY p.created_time DESC";

$result = $db->query($sql);
$total = $result->rowCount();

$projects = [];
$result = $db->query($sql . " LIMIT " . (($page - 1) * $per_page) . ", " . $per_page);
while ($row = $result->fetch()) {
    $projects[] = $row;
}

$xtpl = new XTemplate('projects.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);

if (defined('NV_IS_USER')) {
    $xtpl->parse('main.add_button');
}

if (!empty($projects)) {
    foreach ($projects as $project) {
        $project['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=project-detail&amp;id=' . $project['id'];
        $project['start_date_format'] = nv_task_format_date($project['start_date'], 'd/m/Y');
        $project['end_date_format'] = nv_task_format_date($project['end_date'], 'd/m/Y');
        $project['status_name'] = $lang_module['status_' . $project['status']] ?? $project['status'];
        
        $xtpl->assign('PROJECT', $project);
        $xtpl->parse('main.project');
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
