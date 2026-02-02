-- --------------------------------------------------------
-- SQL Script for TaskManager Module
-- NukeViet Content Management System
-- Version: 5.0.00
-- Date: 2026-02-03
-- --------------------------------------------------------

-- Bảng dự án
CREATE TABLE IF NOT EXISTS `nv5_vi_taskmanager_projects` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `start_date` int(11) unsigned NOT NULL DEFAULT '0',
  `end_date` int(11) unsigned NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `owner_id` int(11) unsigned NOT NULL DEFAULT '0',
  `created_time` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_time` int(11) unsigned NOT NULL DEFAULT '0',
  `weight` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Bảng thành viên dự án
CREATE TABLE IF NOT EXISTS `nv5_vi_taskmanager_project_members` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'member',
  `added_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_user` (`project_id`,`user_id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Bảng công việc
CREATE TABLE IF NOT EXISTS `nv5_vi_taskmanager_tasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `description` text,
  `status` varchar(50) NOT NULL DEFAULT 'new',
  `priority` varchar(20) NOT NULL DEFAULT 'medium',
  `progress` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `assigned_to` int(11) unsigned NOT NULL DEFAULT '0',
  `creator_id` int(11) unsigned NOT NULL DEFAULT '0',
  `deadline` int(11) unsigned NOT NULL DEFAULT '0',
  `created_time` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_time` int(11) unsigned NOT NULL DEFAULT '0',
  `weight` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `creator_id` (`creator_id`),
  KEY `status` (`status`),
  KEY `deadline` (`deadline`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Bảng người phối hợp công việc
CREATE TABLE IF NOT EXISTS `nv5_vi_taskmanager_task_collaborators` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `added_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_user` (`task_id`,`user_id`),
  KEY `task_id` (`task_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Bảng bình luận
CREATE TABLE IF NOT EXISTS `nv5_vi_taskmanager_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `created_time` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Bảng đính kèm file
CREATE TABLE IF NOT EXISTS `nv5_vi_taskmanager_attachments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filesize` int(11) unsigned NOT NULL DEFAULT '0',
  `filepath` varchar(255) NOT NULL,
  `mimetype` varchar(100) NOT NULL,
  `uploaded_by` int(11) unsigned NOT NULL,
  `uploaded_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `uploaded_by` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Bảng lịch sử thay đổi (Audit Log)
CREATE TABLE IF NOT EXISTS `nv5_vi_taskmanager_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `action` varchar(100) NOT NULL,
  `old_value` text,
  `new_value` text,
  `created_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `user_id` (`user_id`),
  KEY `created_time` (`created_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Bảng trạng thái tùy biến
CREATE TABLE IF NOT EXISTS `nv5_vi_taskmanager_status` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status_key` varchar(50) NOT NULL,
  `status_name` varchar(100) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#6c757d',
  `weight` int(11) unsigned NOT NULL DEFAULT '0',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `status_key` (`status_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Bảng trường dữ liệu tùy biến
CREATE TABLE IF NOT EXISTS `nv5_vi_taskmanager_custom_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_name` varchar(100) NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_type` varchar(50) NOT NULL,
  `field_options` text,
  `is_required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `weight` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `field_name` (`field_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Bảng giá trị trường tùy biến
CREATE TABLE IF NOT EXISTS `nv5_vi_taskmanager_custom_values` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) unsigned NOT NULL,
  `field_id` int(11) unsigned NOT NULL,
  `field_value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_field` (`task_id`,`field_id`),
  KEY `task_id` (`task_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Dữ liệu mẫu
-- --------------------------------------------------------

-- Thêm trạng thái mặc định
INSERT INTO `nv5_vi_taskmanager_status` (`status_key`, `status_name`, `color`, `weight`, `is_default`) VALUES
('new', 'Mới', '#17a2b8', 1, 1),
('in_progress', 'Đang làm', '#ffc107', 2, 0),
('pending', 'Chờ duyệt', '#fd7e14', 3, 0),
('completed', 'Hoàn thành', '#28a745', 4, 0),
('cancelled', 'Hủy bỏ', '#dc3545', 5, 0);

-- --------------------------------------------------------

-- Cấu hình module trong bảng nv5_config
INSERT INTO `nv5_config` (`lang`, `module`, `config_name`, `config_value`) VALUES
('vi', 'taskmanager', 'per_page', '20'),
('vi', 'taskmanager', 'enable_email', '1'),
('vi', 'taskmanager', 'allow_create_project_groups', ''),
('vi', 'taskmanager', 'deadline_warning_days', '3'),
('vi', 'taskmanager', 'auto_assign_creator', '0');

-- --------------------------------------------------------

-- Dữ liệu demo (có thể xóa nếu không cần)

-- Demo: Thêm 1 dự án mẫu
INSERT INTO `nv5_vi_taskmanager_projects` (`id`, `title`, `description`, `start_date`, `end_date`, `status`, `is_public`, `owner_id`, `created_time`, `updated_time`, `weight`) VALUES
(1, 'Dự án Website Công ty', 'Xây dựng website giới thiệu công ty với đầy đủ các chức năng cơ bản', UNIX_TIMESTAMP('2026-02-01'), UNIX_TIMESTAMP('2026-03-31'), 'active', 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 1);

-- Demo: Thêm thành viên vào dự án
INSERT INTO `nv5_vi_taskmanager_project_members` (`project_id`, `user_id`, `role`, `added_time`) VALUES
(1, 1, 'owner', UNIX_TIMESTAMP());

-- Demo: Thêm công việc mẫu
INSERT INTO `nv5_vi_taskmanager_tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `progress`, `assigned_to`, `creator_id`, `deadline`, `created_time`, `updated_time`, `weight`) VALUES
(1, 1, 'Thiết kế giao diện trang chủ', '<p>Thiết kế giao diện trang chủ với các yêu cầu:</p><ul><li>Header với logo và menu</li><li>Slider giới thiệu</li><li>Các section nội dung</li><li>Footer</li></ul>', 'in_progress', 'high', 50, 1, 1, UNIX_TIMESTAMP('2026-02-15'), UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 1),
(2, 1, 'Lập trình chức năng liên hệ', '<p>Xây dựng form liên hệ với các trường:</p><ul><li>Họ tên</li><li>Email</li><li>Số điện thoại</li><li>Nội dung</li></ul>', 'new', 'medium', 0, 1, 1, UNIX_TIMESTAMP('2026-02-20'), UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 2);

-- Demo: Thêm bình luận mẫu
INSERT INTO `nv5_vi_taskmanager_comments` (`task_id`, `user_id`, `parent_id`, `content`, `created_time`, `updated_time`) VALUES
(1, 1, 0, '<p>Đã hoàn thành phần header và menu, đang làm slider.</p>', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- --------------------------------------------------------
-- LƯU Ý:
-- 1. Thay đổi prefix 'nv5_' theo cấu hình của bạn
-- 2. Thay đổi ngôn ngữ 'vi' nếu sử dụng ngôn ngữ khác
-- 3. Có thể xóa phần dữ liệu demo nếu không cần
-- --------------------------------------------------------
