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

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];

// Tạo database tables khi cài đặt module
$sql_create_module = [];

// Bảng dự án
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . NV_PREFIXLANG . "_" . $module_data . "_projects (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    description text,
    start_date int(11) unsigned NOT NULL DEFAULT '0',
    end_date int(11) unsigned NOT NULL DEFAULT '0',
    status varchar(50) NOT NULL DEFAULT 'active',
    is_public tinyint(1) unsigned NOT NULL DEFAULT '0',
    owner_id int(11) unsigned NOT NULL DEFAULT '0',
    created_time int(11) unsigned NOT NULL DEFAULT '0',
    updated_time int(11) unsigned NOT NULL DEFAULT '0',
    weight int(11) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (id),
    KEY owner_id (owner_id),
    KEY status (status)
) ENGINE=InnoDB";

// Bảng thành viên dự án
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . NV_PREFIXLANG . "_" . $module_data . "_project_members (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    project_id int(11) unsigned NOT NULL,
    user_id int(11) unsigned NOT NULL,
    role varchar(50) NOT NULL DEFAULT 'member',
    added_time int(11) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (id),
    UNIQUE KEY project_user (project_id, user_id),
    KEY project_id (project_id),
    KEY user_id (user_id)
) ENGINE=InnoDB";

// Bảng công việc
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . NV_PREFIXLANG . "_" . $module_data . "_tasks (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    project_id int(11) unsigned NOT NULL DEFAULT '0',
    title varchar(255) NOT NULL,
    description text,
    status varchar(50) NOT NULL DEFAULT 'new',
    priority varchar(20) NOT NULL DEFAULT 'medium',
    progress tinyint(3) unsigned NOT NULL DEFAULT '0',
    assigned_to int(11) unsigned NOT NULL DEFAULT '0',
    creator_id int(11) unsigned NOT NULL DEFAULT '0',
    deadline int(11) unsigned NOT NULL DEFAULT '0',
    created_time int(11) unsigned NOT NULL DEFAULT '0',
    updated_time int(11) unsigned NOT NULL DEFAULT '0',
    weight int(11) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (id),
    KEY project_id (project_id),
    KEY assigned_to (assigned_to),
    KEY creator_id (creator_id),
    KEY status (status),
    KEY deadline (deadline)
) ENGINE=InnoDB";

// Bảng người phối hợp công việc
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . NV_PREFIXLANG . "_" . $module_data . "_task_collaborators (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    task_id int(11) unsigned NOT NULL,
    user_id int(11) unsigned NOT NULL,
    added_time int(11) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (id),
    UNIQUE KEY task_user (task_id, user_id),
    KEY task_id (task_id),
    KEY user_id (user_id)
) ENGINE=InnoDB";

// Bảng bình luận
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . NV_PREFIXLANG . "_" . $module_data . "_comments (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    task_id int(11) unsigned NOT NULL,
    user_id int(11) unsigned NOT NULL,
    parent_id int(11) unsigned NOT NULL DEFAULT '0',
    content text NOT NULL,
    created_time int(11) unsigned NOT NULL DEFAULT '0',
    updated_time int(11) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (id),
    KEY task_id (task_id),
    KEY user_id (user_id),
    KEY parent_id (parent_id)
) ENGINE=InnoDB";

// Bảng đính kèm file
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . NV_PREFIXLANG . "_" . $module_data . "_attachments (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    task_id int(11) unsigned NOT NULL,
    filename varchar(255) NOT NULL,
    filesize int(11) unsigned NOT NULL DEFAULT '0',
    filepath varchar(255) NOT NULL,
    mimetype varchar(100) NOT NULL,
    uploaded_by int(11) unsigned NOT NULL,
    uploaded_time int(11) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (id),
    KEY task_id (task_id),
    KEY uploaded_by (uploaded_by)
) ENGINE=InnoDB";

// Bảng lịch sử thay đổi (Audit Log)
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . NV_PREFIXLANG . "_" . $module_data . "_history (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    task_id int(11) unsigned NOT NULL,
    user_id int(11) unsigned NOT NULL,
    action varchar(100) NOT NULL,
    old_value text,
    new_value text,
    created_time int(11) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (id),
    KEY task_id (task_id),
    KEY user_id (user_id),
    KEY created_time (created_time)
) ENGINE=InnoDB";

// Bảng trạng thái tùy biến
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . NV_PREFIXLANG . "_" . $module_data . "_status (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    status_key varchar(50) NOT NULL,
    status_name varchar(100) NOT NULL,
    color varchar(20) NOT NULL DEFAULT '#6c757d',
    weight int(11) unsigned NOT NULL DEFAULT '0',
    is_default tinyint(1) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (id),
    UNIQUE KEY status_key (status_key)
) ENGINE=InnoDB";

// Bảng trường dữ liệu tùy biến
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . NV_PREFIXLANG . "_" . $module_data . "_custom_fields (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    field_name varchar(100) NOT NULL,
    field_label varchar(255) NOT NULL,
    field_type varchar(50) NOT NULL,
    field_options text,
    is_required tinyint(1) unsigned NOT NULL DEFAULT '0',
    weight int(11) unsigned NOT NULL DEFAULT '0',
    status tinyint(1) unsigned NOT NULL DEFAULT '1',
    PRIMARY KEY (id),
    UNIQUE KEY field_name (field_name)
) ENGINE=InnoDB";

// Bảng giá trị trường tùy biến
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . NV_PREFIXLANG . "_" . $module_data . "_custom_values (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    task_id int(11) unsigned NOT NULL,
    field_id int(11) unsigned NOT NULL,
    field_value text,
    PRIMARY KEY (id),
    UNIQUE KEY task_field (task_id, field_id),
    KEY task_id (task_id),
    KEY field_id (field_id)
) ENGINE=InnoDB";

// Thêm trạng thái mặc định
$sql_create_module[] = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_status 
    (status_key, status_name, color, weight, is_default) VALUES
    ('new', 'Mới', '#17a2b8', 1, 1),
    ('in_progress', 'Đang làm', '#ffc107', 2, 0),
    ('pending', 'Chờ duyệt', '#fd7e14', 3, 0),
    ('completed', 'Hoàn thành', '#28a745', 4, 0),
    ('cancelled', 'Hủy bỏ', '#dc3545', 5, 0)";

// Cấu hình module
$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES
    ('" . $lang_data . "', '" . $module_name . "', 'per_page', '20'),
    ('" . $lang_data . "', '" . $module_name . "', 'enable_email', '1'),
    ('" . $lang_data . "', '" . $module_name . "', 'allow_create_project_groups', ''),
    ('" . $lang_data . "', '" . $module_name . "', 'deadline_warning_days', '3'),
    ('" . $lang_data . "', '" . $module_name . "', 'auto_assign_creator', '0')";
