<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2026 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    exit('Stop!!!');
}

define('NV_IS_FILE_ADMIN', true);

$allow_func = ['main', 'config', 'status', 'custom_fields', 'permissions'];

// Menu admin
$submenu['config'] = $lang_module['config'];
$submenu['status'] = $lang_module['status_manage'];
$submenu['custom_fields'] = $lang_module['custom_fields'];
$submenu['permissions'] = $lang_module['permissions'];
