<?php
/**
 * ajax front controller
 *
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * @copyright Copyright 2025 mc12345678.com McNumbers Ware
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: mc12345678 2025 Jun 25 Modified after v2.1.0 $
 */
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    http_response_code(400); // "Bad Request"
    exit();
}

if (empty($zc_ajax_base_dir)) {
    $zc_ajax_base_dir = '';
}
require $zc_ajax_base_dir . 'includes/application_top.php';

// deny ajax requests from spiders
if (isset($spider_flag) && $spider_flag === true) ajaxAbort();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header("Access-Control-Allow-Headers: X-Requested-With");


// --- support functions ------------------
function ajaxAbort($status = 400, $msg = null)
{
    global $zc_ajax_base_dir;
    http_response_code($status); // 400 = "Bad Request"
    if ($msg) {
        echo $msg;
    }
    require $zc_ajax_base_dir . 'includes/application_bottom.php';
    exit();
}
// --- support functions ------------------



if (!isset($_GET['act'], $_GET['method']) || !preg_match('/^[a-zA-Z0-9_]+$/', $_GET['act']) || !preg_match('/^[a-zA-Z0-9_]+$/', $_GET['method'])) {
    ajaxAbort();
}

$language_page_directory = DIR_WS_LANGUAGES . $_SESSION['language'] . '/';

$className = 'zc' . ucfirst($_GET['act']);
$classFile = basename($className . '.php');
$classPath = DIR_WS_CLASSES . 'ajax/';
$basePath  = DIR_FS_CATALOG;
$file = realpath($basePath . $classPath . $classFile);
if (!empty($file) && file_exists($file)) {
    require $file;
} else {
    $fs->loadFilesFromPluginsDirectory($installedPlugins, 'catalog/' . $classPath, '~^' . $classFile . '$~');
    if (!class_exists($className)) {
        ajaxAbort();
    }
}

$class = new $className();
if (!method_exists($class, $_GET['method'])) {
    ajaxAbort(400, 'class method error');
}

$result = call_user_func(array($class, $_GET['method']));
echo json_encode($result);
require $zc_ajax_base_dir . 'includes/application_bottom.php';
