<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2026 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

$lang_translator['author'] = 'VINADES.,JSC';
$lang_translator['createdate'] = '03/02/2026, 07:00';
$lang_translator['copyright'] = '@Copyright (C) 2009-2026 VINADES.,JSC. All rights reserved';
$lang_translator['info'] = '';
$lang_translator['langtype'] = 'lang_module';

// Menu
$lang_module['main'] = 'Cấu hình chung';
$lang_module['config'] = 'Cấu hình module';
$lang_module['status_manage'] = 'Quản lý trạng thái';
$lang_module['custom_fields'] = 'Trường dữ liệu tùy biến';
$lang_module['permissions'] = 'Phân quyền';

// Config
$lang_module['config_per_page'] = 'Số công việc hiển thị/trang';
$lang_module['config_enable_email'] = 'Cho phép gửi email thông báo';
$lang_module['config_deadline_warning_days'] = 'Cảnh báo trước deadline (ngày)';
$lang_module['config_auto_assign_creator'] = 'Tự động gán người tạo làm người thực hiện';
$lang_module['config_allow_create_project_groups'] = 'Nhóm được tạo dự án';
$lang_module['config_allow_create_project_groups_note'] = 'Chọn các nhóm thành viên được phép tạo dự án mới. Để trống = tất cả đều được phép';
$lang_module['config_saved'] = 'Cấu hình đã được lưu';

// Status Management
$lang_module['status_list'] = 'Danh sách trạng thái';
$lang_module['status_add'] = 'Thêm trạng thái';
$lang_module['status_edit'] = 'Sửa trạng thái';
$lang_module['status_key'] = 'Mã trạng thái';
$lang_module['status_name'] = 'Tên trạng thái';
$lang_module['status_color'] = 'Màu sắc';
$lang_module['status_default'] = 'Mặc định';
$lang_module['status_weight'] = 'Thứ tự';
$lang_module['status_created'] = 'Trạng thái đã được thêm';
$lang_module['status_updated'] = 'Trạng thái đã được cập nhật';
$lang_module['status_deleted'] = 'Trạng thái đã được xóa';
$lang_module['status_cannot_delete_default'] = 'Không thể xóa trạng thái mặc định';
$lang_module['status_cannot_delete_in_use'] = 'Không thể xóa trạng thái đang được sử dụng';

// Custom Fields
$lang_module['field_list'] = 'Danh sách trường tùy biến';
$lang_module['field_add'] = 'Thêm trường mới';
$lang_module['field_edit'] = 'Sửa trường';
$lang_module['field_name'] = 'Tên trường (code)';
$lang_module['field_label'] = 'Nhãn hiển thị';
$lang_module['field_type'] = 'Loại dữ liệu';
$lang_module['field_type_text'] = 'Văn bản ngắn';
$lang_module['field_type_textarea'] = 'Văn bản dài';
$lang_module['field_type_select'] = 'Hộp chọn';
$lang_module['field_type_date'] = 'Ngày tháng';
$lang_module['field_type_number'] = 'Số';
$lang_module['field_options'] = 'Tùy chọn (mỗi dòng một giá trị)';
$lang_module['field_required'] = 'Bắt buộc';
$lang_module['field_status'] = 'Trạng thái';
$lang_module['field_active'] = 'Kích hoạt';
$lang_module['field_inactive'] = 'Không kích hoạt';
$lang_module['field_created'] = 'Trường đã được thêm';
$lang_module['field_updated'] = 'Trường đã được cập nhật';
$lang_module['field_deleted'] = 'Trường đã được xóa';
$lang_module['field_name_exists'] = 'Tên trường đã tồn tại';

// Permissions
$lang_module['perm_project_managers'] = 'Nhóm Quản lý dự án';
$lang_module['perm_project_managers_note'] = 'Các nhóm này có quyền quản lý tất cả dự án';
$lang_module['perm_task_managers'] = 'Nhóm Quản lý công việc';
$lang_module['perm_task_managers_note'] = 'Các nhóm này có quyền quản lý tất cả công việc';
$lang_module['perm_saved'] = 'Phân quyền đã được lưu';

// Common
$lang_module['save'] = 'Lưu lại';
$lang_module['cancel'] = 'Hủy bỏ';
$lang_module['delete'] = 'Xóa';
$lang_module['edit'] = 'Sửa';
$lang_module['add'] = 'Thêm';
$lang_module['back'] = 'Quay lại';
$lang_module['confirm_delete'] = 'Bạn có chắc chắn muốn xóa?';
$lang_module['success'] = 'Thành công';
$lang_module['error'] = 'Lỗi';
$lang_module['no_data'] = 'Không có dữ liệu';
$lang_module['search'] = 'Tìm kiếm';
$lang_module['filter'] = 'Lọc';
