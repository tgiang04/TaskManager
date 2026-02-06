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

$module_version = [
    'name' => 'TaskManager',
    'modfuncs' => 'main,projects,project-detail,tasks,task-detail,my-tasks,calendar,dashboard,time-tracking,kanban,gantt,templates,reports,sitemap',
    'submenu' => 'main,projects,my-tasks,calendar,dashboard,kanban,reports',
    'is_sysmod' => 0,
    'virtual' => 1,
    'version' => '5.1.00',
    'date' => 'Tue, 04 Feb 2026 07:00:00 GMT',
    'author' => 'VINADES (contact@vinades.vn)',
    'note' => 'Module quản lý công việc và dự án với tính năng nâng cao',
    'uploads_dir' => [
        $module_name,
        $module_name . '/projects',
        $module_name . '/tasks',
        $module_name . '/attachments'
    ],
    'files_dir' => [
        $module_name
    ]
];
