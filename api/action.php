<?php
/**
 * 对系统内文件进行操作的api
 * Author: 诺天 <s235784@gmail.com>
 * Date  : 2021/7/3
 * Time  : 18:36
 */

header('charset=utf-8');
header('Content-type: application/json');

$code_config = parse_ini_file('../admin/code.ini', true)['status'];
$CODE_ERROR = $code_config['error'];
$CODE_OK = $code_config['ok'];

$input = file_get_contents('php://input');
$userJSON = json_decode($input, true);
$post_id = $userJSON['uid'];
$post_pass = $userJSON['pass'];
$post_action = $userJSON['action'];

if (empty($post_id) || empty($post_pass) || empty($post_action)) returnJSON($CODE_ERROR, '请求参数不能为空！');

$db_config = parse_ini_file('../admin/config.ini', true)['db'];
$link = new mysqli($db_config['host'], $db_config['user'], $db_config['password'], $db_config['db']);
if ($link->connect_error) returnJSON($CODE_ERROR, "连接数据库失败: " . $link->connect_error);

$link->set_charset('utf8');
$sql = "SELECT * FROM ray_users";
$results = $link->query($sql);
if ($results->num_rows == 0) returnJSON($CODE_ERROR, '数据库中没有数据！');

if ($post_id != getSuperAdminID()) returnJSON($CODE_ERROR, '没有权限执行此操作！');

$sql = "SELECT pass, status FROM ray_users WHERE id='$post_id'";
$results = $link->query($sql);
if ($results->num_rows == 0) returnJSON($CODE_ERROR, '登录信息过期，请重新登录！');
while ($result = $results->fetch_assoc()) {
    if (strcasecmp($post_pass, $result['pass']) != 0) returnJSON($CODE_ERROR, '登录信息过期，请重新登录！');
    else action();
}

function returnJSON($status_code, $msg) {
    $arr = array('status' => (int)$status_code, 'msg' => $msg);
    echo json_encode($arr);
    die;
}

// php action.php 操作 密码
function action() {
    global $post_action, $CODE_ERROR, $CODE_OK;
    chdir ('../admin');
    $path = getcwd();
    switch ($post_action) {
        case 'restart':
            $output = shell_exec('bash ' . $path . '/action.sh restart');
            returnJSON($CODE_OK, $output);
            break;
        case 'sync':
            $output = shell_exec('bash ' . $path . '/action.sh sync');
            if (strpos($output, '同步完成') === false) returnJSON($CODE_ERROR, $output);
            else returnJSON($CODE_OK, '同步完成！');
            break;
        case 'init':
            $output = shell_exec('bash ' . $path . '/action.sh init');
            if (strpos($output, '初始化完成') === false) returnJSON($CODE_ERROR, $output);
            else returnJSON($CODE_OK, '初始化完成！');
            break;
        default:
            returnJSON($CODE_ERROR, '参数不合法！');
    }
}

function getSuperAdminID() {
    $id = 0;
    global $link, $CODE_ERROR;
    $admin_config_email = parse_ini_file('../admin/config.ini', true)['admin']['email'];
    $sql = "SELECT id FROM ray_users WHERE email='$admin_config_email'";
    $results = $link->query($sql);
    if ($results->num_rows == 0) returnJSON($CODE_ERROR, '数据库结构受损！');
    while ($result = $results->fetch_assoc()) {
        $id = $result['id'];
    }
    return $id;
}