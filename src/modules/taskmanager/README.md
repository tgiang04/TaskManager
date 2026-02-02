# Module TaskManager cho NukeViet 5.x

## Giới thiệu

Module TaskManager là một hệ thống quản lý công việc và dự án hoàn chỉnh cho NukeViet CMS. Module được thiết kế để giúp các tổ chức, doanh nghiệp quản lý công việc hiệu quả với đầy đủ tính năng chuyên nghiệp.

## Tính năng chính

### 1. Phân hệ Quản trị (Admin)

- **Cấu hình Module**: 
  - Thiết lập số lượng công việc hiển thị trên mỗi trang
  - Bật/tắt gửi email thông báo
  - Cấu hình số ngày cảnh báo trước deadline
  - Phân quyền tạo dự án theo nhóm thành viên

- **Quản lý Trạng thái**:
  - Tùy chỉnh các trạng thái công việc (Mới, Đang làm, Chờ duyệt, Hoàn thành, Hủy bỏ)
  - Thiết lập màu sắc cho từng trạng thái
  - Linh hoạt thêm/sửa/xóa trạng thái

- **Quản lý Trường dữ liệu tùy biến**:
  - Thêm các trường dữ liệu động cho công việc
  - Hỗ trợ nhiều loại dữ liệu: Text, Textarea, Select, Date, Number
  - Không cần can thiệp code

### 2. Phân hệ Người dùng

- **Quản lý Dự án**:
  - Tạo mới dự án với đầy đủ thông tin
  - Phân quyền dự án (Public/Private)
  - Thêm/xóa thành viên dự án
  - Thống kê tiến độ dự án

- **Quản lý Công việc**:
  - Tạo/Sửa/Xóa công việc với trình soạn thảo WYSIWYG
  - Giao việc cho thành viên
  - Đặt độ ưu tiên, deadline
  - Theo dõi tiến độ với thanh progress bar
  - Cập nhật trạng thái bằng dropdown

- **Cộng tác và Tương tác**:
  - Hệ thống bình luận cho mỗi công việc
  - Đính kèm tệp tin
  - Lịch sử thay đổi (Audit Log)

- **Thông báo**:
  - Email tự động khi có công việc mới
  - Cảnh báo công việc sắp đến hạn/quá hạn

- **Lịch công việc**:
  - Xem công việc theo lịch tháng
  - Quản lý deadline trực quan

## Cài đặt

1. Copy thư mục `taskmanager` vào `src/modules/`
2. Copy theme templates vào `src/themes/default/modules/taskmanager/`
3. Đăng nhập Admin Panel
4. Vào **Quản lý module** > **Cài đặt module TaskManager**
5. Kích hoạt module

## Cấu trúc Database

Module tạo 10 bảng dữ liệu:

- `projects` - Dự án
- `project_members` - Thành viên dự án
- `tasks` - Công việc
- `task_collaborators` - Người phối hợp công việc
- `comments` - Bình luận
- `attachments` - File đính kèm
- `history` - Lịch sử thay đổi
- `status` - Trạng thái tùy biến
- `custom_fields` - Trường dữ liệu tùy biến
- `custom_values` - Giá trị trường tùy biến

## Sử dụng

### Tạo dự án mới

1. Truy cập menu **Dự án** > **Thêm dự án mới**
2. Điền thông tin: Tên, Mô tả, Ngày bắt đầu/kết thúc
3. Chọn chế độ Public (công khai) hoặc Private (riêng tư)
4. Thêm thành viên vào dự án

### Tạo công việc

1. Vào chi tiết dự án
2. Click **Thêm công việc**
3. Nhập tiêu đề, mô tả (sử dụng CKEditor)
4. Chọn người thực hiện, người phối hợp
5. Đặt độ ưu tiên và deadline
6. Lưu

### Theo dõi công việc

- Xem **Việc của tôi** để theo dõi công việc được giao
- Cập nhật tiến độ bằng thanh progress
- Thay đổi trạng thái khi hoàn thành các bước
- Bình luận để trao đổi với team

## Yêu cầu hệ thống

- NukeViet 5.x
- PHP 7.4+
- MySQL 5.7+

## Hỗ trợ

Mọi thắc mắc vui lòng liên hệ:
- Email: contact@vinades.vn
- Website: https://nukeviet.vn

## Bản quyền

Copyright (C) 2009-2026 VINADES.,JSC. All rights reserved.
License: GNU/GPL version 2 or any later version

## Changelog

### Version 5.0.00 (03/02/2026)
- Phát hành phiên bản đầu tiên
- Đầy đủ tính năng quản lý dự án và công việc
- Hỗ trợ bình luận, đính kèm file
- Hệ thống thông báo email
- Trạng thái và trường dữ liệu tùy biến
