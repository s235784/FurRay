<?php
/**
 * 更新用户信息的api
 * Author: s235784 <s235784@gmail.com>
 * Date  : 2021/6/19
 * Time  : 20:57
 */

include 'func.php';

header('charset=utf-8');
header('Content-type: application/json');

$code_config = parse_ini_file('../admin/code.ini', true)['status'];
$CODE_ERROR = $code_config['error'];
$CODE_OK    = $code_config['ok'];

$input = file_get_contents('php://input');
$userJSON = json_decode($input, true);
$post_id = $userJSON['uid'];
$post_pass  = $userJSON['pass'];
$post_key  = $userJSON['key'];
$post_value  = $userJSON['value'];

if (empty($post_id) || empty($post_pass) || empty($post_key) || empty($post_value))
    returnJSON($CODE_ERROR, '请求不能为空！');

$db_config = parse_ini_file('../admin/config.ini', true)['db'];
$link = new mysqli($db_config['host'], $db_config['user'], $db_config['password'], $db_config['db']);
if ($link->connect_error) returnJSON($CODE_ERROR, "连接数据库失败: " . $link->connect_error);

$link->set_charset('utf8');
$sql = "SELECT * FROM ray_users";
$results = $link->query($sql);
if ($results->num_rows == 0) returnJSON($CODE_ERROR, '数据库中没有数据！');

if ($post_key == 'password') { // 对密码和value进行编码
    $post_value_pass = $post_value;
    $post_value = getMd5Pass($post_value);
    $post_pass = getMd5Pass($post_pass);
}

$sql = "SELECT pass FROM ray_users WHERE id='$post_id'";
$results = $link->query($sql);
if ($results->num_rows == 0) returnJSON($CODE_ERROR, '登录信息过期，请重新登录！');
while($result = $results->fetch_assoc()) {
    if (strcasecmp($post_pass,$result['pass']) == 0) update();
    else if ($post_key == 'password') returnJSON($CODE_ERROR, '当前密码错误，请重新输入！');
    else returnJSON($CODE_ERROR, '登录信息过期，请重新登录！');
}

function returnJSON($status_code, $msg, $pass = null) {
    $arr = array('status' => (int)$status_code, 'msg' => $msg, 'pass' => $pass);
    echo json_encode($arr);
    die;
}

function update() {
    global $link, $post_key, $post_value, $post_value_pass, $post_id, $CODE_ERROR, $CODE_OK;
    $sql = '';
    $pass = null;
    switch ($post_key) {
        case 'name':
            $sql = "UPDATE ray_users SET name='{$post_value}' WHERE id='{$post_id}'";
            break;
        case 'email':
            $sql = "SELECT id FROM ray_users WHERE email='{$post_value}'";
            if ($link->query($sql)->num_rows != 0) returnJSON($CODE_ERROR, '邮箱地址已存在！');
            if (filter_var($post_value, FILTER_VALIDATE_EMAIL))
                $sql = "UPDATE ray_users SET email='{$post_value}' WHERE id='{$post_id}'";
            else returnJSON($CODE_ERROR, '邮箱格式不合法！');
            break;
        case 'uuid':
            $sql = "SELECT id FROM ray_users WHERE uuid='{$post_value}'";
            if ($link->query($sql)->num_rows != 0) returnJSON($CODE_ERROR, 'UUID已存在！');
            $reg = '/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/';
            if (preg_match($reg, $post_value))
                $sql = "UPDATE ray_users SET uuid='{$post_value}' WHERE id='{$post_id}'";
            else returnJSON($CODE_ERROR, 'UUID格式不合法！'.$post_value);
            break;
        case 'password':
            $reg = '/\s/';
            if (strlen($post_value_pass) >= 8 && strlen($post_value_pass) <= 16 && !preg_match($reg, $post_value_pass)) {
                $sql = "UPDATE ray_users SET pass='{$post_value}' WHERE id='{$post_id}'";
                $pass = $post_value;
            }
            else returnJSON($CODE_ERROR, '密码不合法！');
            break;
        default:
            returnJSON($CODE_ERROR, '参数不合法！');
    }
    if($link->query($sql) === false) returnJSON($CODE_ERROR, '更新数据时发生错误：' . $link -> error);
    else returnJSON($CODE_OK, '数据已更新', $pass);
}