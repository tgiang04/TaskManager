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

$page_title = $nv_Lang->getModule('templates');
$key_words = $module_info['keywords'];

$action = $nv_Request->get_title('action', 'get', '');
$template_id = $nv_Request->get_int('id', 'get', 0);

// AJAX: Use template
if ($nv_Request->isset_request('use_template', 'post')) {
    $template_id = $nv_Request->get_int('template_id', 'post', 0);
    $project_title = $nv_Request->get_title('project_title', 'post', '');
    
    if (empty($project_title)) {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $nv_Lang->getModule('error_project_title_required')
        ]);
    }
    
    // Lấy template
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_templates
            WHERE id = " . $template_id;
    $template = $db->query($sql)->fetch();
    
    if (!$template) {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $nv_Lang->getModule('template_not_found')
        ]);
    }
    
    // Tạo dự án mới từ template
    $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_projects
            (title, description, owner_id, is_public, status, created_time, color)
            VALUES (
                " . $db->quote($project_title) . ",
                " . $db->quote($template['description']) . ",
                " . $user_info['userid'] . ",
                0,
                'active',
                " . NV_CURRENTTIME . ",
                " . $db->quote($template['color']) . "
            )";
    
    $project_id = $db->insert_id($sql, 'id');
    
    if ($project_id) {
        // Copy tasks từ template
        $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_template_tasks
                WHERE template_id = " . $template_id . "
                ORDER BY position ASC";
        
        $result = $db->query($sql);
        $task_map = []; // Map old task ID to new task ID
        
        while ($task = $result->fetch()) {
            $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_tasks
                    (project_id, title, description, priority, creator_id, created_time, status_id)
                    VALUES (
                        " . $project_id . ",
                        " . $db->quote($task['title']) . ",
                        " . $db->quote($task['description']) . ",
                        " . $db->quote($task['priority']) . ",
                        " . $user_info['userid'] . ",
                        " . NV_CURRENTTIME . ",
                        (SELECT id FROM " . NV_PREFIXLANG . "_" . $module_data . "_status WHERE is_default = 1 LIMIT 1)
                    )";
            
            $new_task_id = $db->insert_id($sql, 'id');
            $task_map[$task['id']] = $new_task_id;
        }
        
        // Copy dependencies (nếu có)
        foreach ($task_map as $old_id => $new_id) {
            $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_template_task_dependencies
                    WHERE task_id = " . $old_id;
            $result = $db->query($sql);
            
            while ($dep = $result->fetch()) {
                if (isset($task_map[$dep['dependency_task_id']])) {
                    $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_task_dependencies
                            (task_id, dependency_task_id, dependency_type)
                            VALUES (
                                " . $new_id . ",
                                " . $task_map[$dep['dependency_task_id']] . ",
                                " . $db->quote($dep['dependency_type']) . "
                            )";
                    $db->query($sql);
                }
            }
        }
        
        nv_jsonOutput([
            'status' => 'success',
            'message' => $nv_Lang->getModule('project_created_from_template'),
            'project_id' => $project_id
        ]);
    } else {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $nv_Lang->getModule('error_occurred')
        ]);
    }
}

// AJAX: Save as template
if ($nv_Request->isset_request('save_as_template', 'post')) {
    $project_id = $nv_Request->get_int('project_id', 'post', 0);
    $template_name = $nv_Request->get_title('template_name', 'post', '');
    $is_public = $nv_Request->get_int('is_public', 'post', 0);
    
    // Kiểm tra quyền
    if (!nv_task_check_project_permission($project_id, $user_info['userid'])) {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $nv_Lang->getModule('error_permission_denied')
        ]);
    }
    
    $project = nv_task_get_project($project_id);
    
    // Tạo template
    $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_templates
            (title, description, category, color, is_public, creator_id, created_time)
            VALUES (
                " . $db->quote($template_name) . ",
                " . $db->quote($project['description']) . ",
                'custom',
                " . $db->quote($project['color']) . ",
                " . $is_public . ",
                " . $user_info['userid'] . ",
                " . NV_CURRENTTIME . "
            )";
    
    $template_id = $db->insert_id($sql, 'id');
    
    if ($template_id) {
        // Copy tasks
        $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_tasks
                WHERE project_id = " . $project_id;
        
        $result = $db->query($sql);
        $task_map = [];
        $position = 0;
        
        while ($task = $result->fetch()) {
            $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_template_tasks
                    (template_id, title, description, priority, position)
                    VALUES (
                        " . $template_id . ",
                        " . $db->quote($task['title']) . ",
                        " . $db->quote($task['description']) . ",
                        " . $db->quote($task['priority']) . ",
                        " . $position++ . "
                    )";
            
            $new_task_id = $db->insert_id($sql, 'id');
            $task_map[$task['id']] = $new_task_id;
        }
        
        nv_jsonOutput([
            'status' => 'success',
            'message' => $nv_Lang->getModule('template_saved')
        ]);
    } else {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $nv_Lang->getModule('error_occurred')
        ]);
    }
}

// Lấy danh sách templates
$category = $nv_Request->get_title('category', 'get', 'all');

$where = "1=1";
if ($category != 'all') {
    $where .= " AND category = " . $db->quote($category);
}

// Chỉ hiển thị templates public hoặc của user
$where .= " AND (is_public = 1 OR creator_id = " . $user_info['userid'] . ")";

$templates = [];
$sql = "SELECT t.*, u.username as creator_username
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_templates t
        LEFT JOIN " . NV_USERS_GLOBALTABLE . " u ON t.creator_id = u.userid
        WHERE " . $where . "
        ORDER BY t.created_time DESC";

$result = $db->query($sql);
while ($row = $result->fetch()) {
    // Đếm số tasks
    $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_template_tasks
            WHERE template_id = " . $row['id'];
    $row['task_count'] = $db->query($sql)->fetchColumn();
    
    $templates[] = $row;
}

// Danh mục templates
$categories = [
    'all' => $nv_Lang->getModule('all_categories'),
    'software' => $nv_Lang->getModule('software_development'),
    'marketing' => $nv_Lang->getModule('marketing'),
    'design' => $nv_Lang->getModule('design'),
    'business' => $nv_Lang->getModule('business'),
    'custom' => $nv_Lang->getModule('custom')
];

$xtpl = new XTemplate('templates.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $nv_Lang);
$xtpl->assign('MODULE_NAME', $module_name);

// Categories
foreach ($categories as $cat_key => $cat_label) {
    $cat_data = [
        'key' => $cat_key,
        'label' => $cat_label,
        'active' => $cat_key == $category ? 'active' : ''
    ];
    $xtpl->assign('CATEGORY', $cat_data);
    $xtpl->parse('main.category');
}

// Templates
foreach ($templates as $tpl) {
    $xtpl->assign('TEMPLATE', $tpl);
    $xtpl->parse('main.template');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
