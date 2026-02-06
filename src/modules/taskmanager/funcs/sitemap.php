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

$url = [];
$cacheFile = 'sitemap_' . NV_CACHE_PREFIX . '.cache';
$cacheTTL = 7200; // 2 hours

// Kiểm tra cache
if (($cache = $nv_Cache->getItem($module_name, $cacheFile, ttl: $cacheTTL)) != false) {
    $url = unserialize($cache);
} else {
    // Lấy danh sách projects công khai
    $sql = 'SELECT id, title, alias, created_time, updated_time 
            FROM ' . NV_PREFIXLANG . '_' . $module_data . '_projects 
            WHERE is_public = 1 
            ORDER BY updated_time DESC';
    $result = $db_slave->query($sql);
    
    while ($row = $result->fetch()) {
        $alias = !empty($row['alias']) ? $row['alias'] : change_alias($row['title']);
        $publtime = !empty($row['updated_time']) ? $row['updated_time'] : $row['created_time'];
        
        if ($global_config['rewrite_enable']) {
            $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=project/' . $alias . '-' . $row['id'] . $global_config['rewrite_exturl'];
        } else {
            $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=project-detail&amp;id=' . $row['id'];
        }
        
        $url[] = [
            'link' => $link,
            'publtime' => $publtime,
            'priority' => '0.8'
        ];
    }
    
    // Lấy danh sách tasks của projects công khai
    $sql = 'SELECT t.id, t.title, t.alias, t.project_id, t.created_time, t.updated_time,
            p.alias as project_alias
            FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tasks t
            LEFT JOIN ' . NV_PREFIXLANG . '_' . $module_data . '_projects p ON t.project_id = p.id
            WHERE p.is_public = 1 
            ORDER BY t.updated_time DESC';
    $result = $db_slave->query($sql);
    
    while ($row = $result->fetch()) {
        $task_alias = !empty($row['alias']) ? $row['alias'] : change_alias($row['title']);
        $project_alias = !empty($row['project_alias']) ? $row['project_alias'] : 'project';
        $publtime = !empty($row['updated_time']) ? $row['updated_time'] : $row['created_time'];
        
        if ($global_config['rewrite_enable']) {
            $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=task/' . $project_alias . '/' . $task_alias . '-' . $row['id'] . $global_config['rewrite_exturl'];
        } else {
            $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=task-detail&amp;id=' . $row['id'];
        }
        
        $url[] = [
            'link' => $link,
            'publtime' => $publtime,
            'priority' => '0.6'
        ];
    }
    
    // Thêm các trang chính
    $main_pages = ['projects', 'tasks', 'templates'];
    foreach ($main_pages as $page) {
        if ($global_config['rewrite_enable']) {
            $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $page . $global_config['rewrite_exturl'];
        } else {
            $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $page;
        }
        
        $url[] = [
            'link' => $link,
            'publtime' => NV_CURRENTTIME,
            'priority' => '0.7'
        ];
    }
    
    // Lưu cache
    $cache = serialize($url);
    $nv_Cache->setItem($module_name, $cacheFile, $cache, ttl: $cacheTTL);
}

nv_xmlSitemap_generate($url);
exit();
