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
    'modfuncs' => 'main,projects,project-detail,tasks,task-detail,my-tasks,calendar',
    'submenu' => 'main,projects,my-tasks,calendar',
    'is_sysmod' => 0,
    'virtual' => 1,
    'version' => '5.0.00',
    'date' => 'Mon, 03 Feb 2026 07:00:00 GMT',
    'author' => 'VINADES (contact@vinades.vn)',
    'note' => 'Module quản lý công việc và dự án',
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
