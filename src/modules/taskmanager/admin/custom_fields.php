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

$page_title = $lang_module['custom_fields'];

// Xóa trường
if ($nv_Request->isset_request('delete', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    
    if ($id > 0) {
        // Xóa dữ liệu trường
        $db->query("DELETE FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_custom_values WHERE field_id = " . $id);
        
        // Xóa trường
        $db->query("DELETE FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_custom_fields WHERE id = " . $id);
        
        nv_jsonOutput([
            'status' => 'OK',
            'message' => $lang_module['field_deleted']
        ]);
    }
    
    nv_jsonOutput([
        'status' => 'error',
        'message' => $lang_module['error']
    ]);
}

// Thêm/Sửa trường
if ($nv_Request->isset_request('submit', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $field_name = $nv_Request->get_title('field_name', 'post', '');
    $field_label = $nv_Request->get_title('field_label', 'post', '');
    $field_type = $nv_Request->get_title('field_type', 'post', 'text');
    $field_options = $nv_Request->get_textarea('field_options', 'post', '');
    $is_required = $nv_Request->get_int('is_required', 'post', 0);
    $status = $nv_Request->get_int('status', 'post', 1);
    $weight = $nv_Request->get_int('weight', 'post', 0);
    
    if (empty($field_name) || empty($field_label)) {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $lang_module['error_required_fields']
        ]);
    }
    
    // Kiểm tra tên trường đã tồn tại chưa
    $sql = "SELECT COUNT(*) FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_custom_fields 
            WHERE field_name = :field_name";
    if ($id > 0) {
        $sql .= " AND id != " . $id;
    }
    
    $sth = $db->prepare($sql);
    $sth->bindParam(':field_name', $field_name, PDO::PARAM_STR);
    $sth->execute();
    
    if ($sth->fetchColumn() > 0) {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $lang_module['field_name_exists']
        ]);
    }
    
    if ($id > 0) {
        // Cập nhật
        $sql = "UPDATE " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_custom_fields SET 
                field_name = :field_name,
                field_label = :field_label,
                field_type = :field_type,
                field_options = :field_options,
                is_required = :is_required,
                weight = :weight,
                status = :status
                WHERE id = " . $id;
    } else {
        // Thêm mới
        $sql = "INSERT INTO " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_custom_fields 
                (field_name, field_label, field_type, field_options, is_required, weight, status) 
                VALUES (:field_name, :field_label, :field_type, :field_options, :is_required, :weight, :status)";
    }
    
    $sth = $db->prepare($sql);
    $sth->bindParam(':field_name', $field_name, PDO::PARAM_STR);
    $sth->bindParam(':field_label', $field_label, PDO::PARAM_STR);
    $sth->bindParam(':field_type', $field_type, PDO::PARAM_STR);
    $sth->bindParam(':field_options', $field_options, PDO::PARAM_STR);
    $sth->bindParam(':is_required', $is_required, PDO::PARAM_INT);
    $sth->bindParam(':weight', $weight, PDO::PARAM_INT);
    $sth->bindParam(':status', $status, PDO::PARAM_INT);
    
    if ($sth->execute()) {
        nv_jsonOutput([
            'status' => 'OK',
            'message' => $id > 0 ? $lang_module['field_updated'] : $lang_module['field_created']
        ]);
    }
    
    nv_jsonOutput([
        'status' => 'error',
        'message' => $lang_module['error']
    ]);
}

// Lấy danh sách trường
$fields_list = [];
$sql = "SELECT * FROM " . $db_config['prefix'] . "_" . $lang_data . "_" . $module_data . "_custom_fields ORDER BY weight ASC";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $fields_list[] = $row;
}

$xtpl = new XTemplate('custom_fields.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file . '/admin');
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);

$field_types = [
    'text' => $lang_module['field_type_text'],
    'textarea' => $lang_module['field_type_textarea'],
    'select' => $lang_module['field_type_select'],
    'date' => $lang_module['field_type_date'],
    'number' => $lang_module['field_type_number']
];

if (!empty($fields_list)) {
    foreach ($fields_list as $field) {
        $field['field_type_name'] = isset($field_types[$field['field_type']]) ? $field_types[$field['field_type']] : $field['field_type'];
        $field['status_name'] = $field['status'] ? $lang_module['field_active'] : $lang_module['field_inactive'];
        $field['is_required_name'] = $field['is_required'] ? $lang_global['yes'] : $lang_global['no'];
        
        $xtpl->assign('FIELD', $field);
        $xtpl->parse('main.field');
    }
} else {
    $xtpl->parse('main.empty');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
