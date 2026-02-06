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

/**
 * Tạo URL thân thiện cho projects
 */
nv_add_hook($module_name, 'get_rewrite_url_project', $priority, function ($project_id, $project_alias) use ($module_name, $global_config) {
    if ($global_config['rewrite_enable']) {
        return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=project/' . $project_alias . '-' . $project_id . $global_config['rewrite_exturl'];
    }
    return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=project-detail&amp;id=' . $project_id;
});

/**
 * Tạo URL thân thiện cho tasks
 */
nv_add_hook($module_name, 'get_rewrite_url_task', $priority, function ($task_id, $task_alias, $project_alias = '') use ($module_name, $global_config) {
    if ($global_config['rewrite_enable']) {
        if (!empty($project_alias)) {
            return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=task/' . $project_alias . '/' . $task_alias . '-' . $task_id . $global_config['rewrite_exturl'];
        }
        return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=task/' . $task_alias . '-' . $task_id . $global_config['rewrite_exturl'];
    }
    return NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=task-detail&amp;id=' . $task_id;
});

/**
 * Xử lý rewrite cho project-detail
 */
$regex = '/^' . nv_preg_quote($module_name) . '\/project\/([a-zA-Z0-9\-]+)\-([0-9]+)' . nv_preg_quote($global_config['rewrite_exturl']) . '$/';
nv_add_rewrite_rule($regex, $module_name . '/project-detail&id=$2');

/**
 * Xử lý rewrite cho task-detail (với project)
 */
$regex = '/^' . nv_preg_quote($module_name) . '\/task\/([a-zA-Z0-9\-]+)\/([a-zA-Z0-9\-]+)\-([0-9]+)' . nv_preg_quote($global_config['rewrite_exturl']) . '$/';
nv_add_rewrite_rule($regex, $module_name . '/task-detail&id=$3');

/**
 * Xử lý rewrite cho task-detail (không có project)
 */
$regex = '/^' . nv_preg_quote($module_name) . '\/task\/([a-zA-Z0-9\-]+)\-([0-9]+)' . nv_preg_quote($global_config['rewrite_exturl']) . '$/';
nv_add_rewrite_rule($regex, $module_name . '/task-detail&id=$2');

/**
 * Xử lý rewrite cho các trang list
 */
$regex = '/^' . nv_preg_quote($module_name) . '\/(projects|tasks|my-tasks|calendar|dashboard|kanban|gantt|templates|reports)' . nv_preg_quote($global_config['rewrite_exturl']) . '$/';
nv_add_rewrite_rule($regex, $module_name . '/$1');

/**
 * Xử lý rewrite cho phân trang
 */
$regex = '/^' . nv_preg_quote($module_name) . '\/(projects|tasks|my-tasks)\/page\-([0-9]+)' . nv_preg_quote($global_config['rewrite_exturl']) . '$/';
nv_add_rewrite_rule($regex, $module_name . '/$1&page=$2');
