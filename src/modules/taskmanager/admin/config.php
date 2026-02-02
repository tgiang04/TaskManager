<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2026 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_ADMIN')) {
    exit('Stop!!!');
}

$page_title = $lang_module['config'];

// Xử lý submit form
if ($nv_Request->isset_request('submit', 'post')) {
    $array_config = [];
    $array_config['per_page'] = $nv_Request->get_int('per_page', 'post', 20);
    $array_config['enable_email'] = $nv_Request->get_int('enable_email', 'post', 0);
    $array_config['deadline_warning_days'] = $nv_Request->get_int('deadline_warning_days', 'post', 3);
    $array_config['auto_assign_creator'] = $nv_Request->get_int('auto_assign_creator', 'post', 0);
    $array_config['allow_create_project_groups'] = $nv_Request->get_typed_array('allow_create_project_groups', 'post', 'int', []);
    
    foreach ($array_config as $config_name => $config_value) {
        if (is_array($config_value)) {
            $config_value = implode(',', $config_value);
        }
        
        $sth = $db->prepare("UPDATE " . NV_CONFIG_GLOBALTABLE . " 
                            SET config_value = :config_value 
                            WHERE lang = :lang AND module = :module AND config_name = :config_name");
        $sth->bindParam(':config_value', $config_value, PDO::PARAM_STR);
        $sth->bindParam(':lang', $lang, PDO::PARAM_STR);
        $sth->bindParam(':module', $module_name, PDO::PARAM_STR);
        $sth->bindParam(':config_name', $config_name, PDO::PARAM_STR);
        $sth->execute();
    }
    
    nv_del_moduleCache($module_name);
    nv_jsonOutput([
        'status' => 'OK',
        'message' => $lang_module['config_saved']
    ]);
}

// Lấy danh sách nhóm thành viên
$groups_list = [];
$result = $db->query("SELECT group_id, title FROM " . NV_GROUPS_GLOBALTABLE . " WHERE group_id > 3 ORDER BY title ASC");
while ($row = $result->fetch()) {
    $groups_list[$row['group_id']] = $row['title'];
}

$allow_create_project_groups = !empty($module_config[$module_name]['allow_create_project_groups']) 
    ? array_map('intval', explode(',', $module_config[$module_name]['allow_create_project_groups'])) 
    : [];

$xtpl = new XTemplate('config.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file . '/admin');
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);

$xtpl->assign('DATA', [
    'per_page' => $module_config[$module_name]['per_page'],
    'enable_email' => $module_config[$module_name]['enable_email'],
    'deadline_warning_days' => $module_config[$module_name]['deadline_warning_days'],
    'auto_assign_creator' => $module_config[$module_name]['auto_assign_creator']
]);

foreach ($groups_list as $group_id => $group_title) {
    $xtpl->assign('GROUP', [
        'id' => $group_id,
        'title' => $group_title,
        'checked' => in_array($group_id, $allow_create_project_groups) ? ' checked="checked"' : ''
    ]);
    $xtpl->parse('main.group');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
