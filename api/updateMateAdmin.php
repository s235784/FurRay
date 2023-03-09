<?php
/**
 * 管理操作的相关api
 * Author: 诺天 <s235784@gmail.com>
 * Date  : 2021/7/3
 * Time  : 15:10
 */

include 'func.php';

header('charset=utf-8');
header('Content-type: application/json');

$code_config = parse_ini_file('../admin/code.ini', true)['status'];
$CODE_ERROR = $code_config['error'];
$CODE_OK    = $code_config['ok'];

$code_config_user = parse_ini_file('../admin/code.ini', true)['user'];
$CODE_ADMIN = $code_config_user['admin'];
$CODE_USER  = $code_config_user['user'];
$CODE_RENT_OK = $code_config_user['ok'];
$CODE_RENT_STOP = $code_config_user['stop'];

$input = file_get_contents('php://input');
$userJSON = json_decode($input, true);
$post_id = $userJSON['uid'];
$post_pass  = $userJSON['pass'];
$post_key  = $userJSON['key'];
$post_value  = $userJSON['value'];
$post_target = $userJSON['target'];

if (empty($post_id) || empty($post_pass) || empty($post_key)) returnJSON($CODE_ERROR, '请求参数不能为空！');

$db_config = parse_ini_file('../admin/config.ini', true)['db'];
$link = new mysqli($db_config['host'], $db_config['user'], $db_config['password'], $db_config['db']);
if ($link->connect_error) returnJSON($CODE_ERROR, "连接数据库失败: " . $link->connect_error);

$link->set_charset('utf8');
$sql = "SELECT * FROM ray_users";
$results = $link->query($sql);
if ($results->num_rows == 0) returnJSON($CODE_ERROR, '数据库中没有数据！');

$sql = "SELECT pass, status FROM ray_users WHERE id='$post_id'";
$results = $link->query($sql);
if ($results->num_rows == 0) returnJSON($CODE_ERROR, '登录信息过期，请重新登录！');
while($result = $results->fetch_assoc()) {
    if (strcasecmp($post_pass,$result['pass']) != 0) returnJSON($CODE_ERROR, '登录信息过期，请重新登录！');
    else if ($result['status'] != $CODE_ADMIN) returnJSON($CODE_ERROR, '没有权限执行此操作！');
    else update();
}

function returnJSON($status_code, $msg) {
    $arr = array('status' => (int)$status_code, 'msg' => $msg);
    echo json_encode($arr);
    die;
}

function update() {
    global $link, $post_key, $post_value, $post_target, $CODE_ERROR, $CODE_OK,
           $CODE_ADMIN, $CODE_USER, $CODE_RENT_OK, $CODE_RENT_STOP;
    $sql = '';
    switch ($post_key) {
        case 'authority':
            if ($post_target == getSuperAdminID()) returnJSON($CODE_ERROR, '不能修改超级管理员的权限！');
            if ($post_value == 'user') $post_value = $CODE_USER;
            else if ($post_value == 'admin') $post_value = $CODE_ADMIN;
            else returnJSON($CODE_ERROR, '参数不合法！');
            $sql = "UPDATE ray_users SET status='$post_value' WHERE id='$post_target'";
            break;
        case 'email':
            $sql = "SELECT id FROM ray_users WHERE email='$post_value'";
            if ($link->query($sql)->num_rows != 0) returnJSON($CODE_ERROR, '邮箱地址已存在！');
            if (filter_var($post_value, FILTER_VALIDATE_EMAIL))
                $sql = "UPDATE ray_users SET email='$post_value' WHERE id='$post_target'";
            else returnJSON($CODE_ERROR, '邮箱格式不合法！');
            break;
        case 'uuid':
            $sql = "SELECT id FROM ray_users WHERE uuid='$post_value'";
            if ($link->query($sql)->num_rows != 0) returnJSON($CODE_ERROR, 'UUID已存在！');
            $reg = '/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/';
            if (preg_match($reg, $post_value))
                $sql = "UPDATE ray_users SET uuid='$post_value' WHERE id='$post_target'";
            else returnJSON($CODE_ERROR, 'UUID格式不合法！'.$post_value);
            break;
        case 'rent':
            if ($post_value == 'ok') $post_value = $CODE_RENT_OK;
            else if ($post_value == 'stop') $post_value = $CODE_RENT_STOP;
            else returnJSON($CODE_ERROR, '参数不合法！');
            $sql = "UPDATE ray_users SET ray_status='$post_value' WHERE id='$post_target'";
            break;
        case 'password':
            $reg = '/\s/';
            if (strlen($post_value) >= 8 && strlen($post_value) <= 16 && !preg_match($reg, $post_value)) {
                $post_value = getMd5Pass($post_value);
                $sql = "UPDATE ray_users SET pass='$post_value' WHERE id='$post_target'";
            }
            else returnJSON($CODE_ERROR, '密码不合法！');
            break;
        case 'delete':
            deleteUser();
            die();
        case 'add':
            addUser();
            die();
        default:
            returnJSON($CODE_ERROR, '参数不合法！');
    }
    if($link->query($sql) === false) returnJSON($CODE_ERROR, '更新数据时发生错误：' . $link -> error);
    else returnJSON($CODE_OK, '数据已更新');
}

function getSuperAdminID() {
    $id = 0;
    global $link, $CODE_ERROR;
    $admin_config_email = parse_ini_file('../admin/config.ini', true)['admin']['email'];
    $sql = "SELECT id FROM ray_users WHERE email='$admin_config_email'";
    $results = $link->query($sql);
    if ($results->num_rows == 0) returnJSON($CODE_ERROR, '数据库结构受损！');
    while($result = $results->fetch_assoc()) {
        $id = $result['id'];
    }
    return $id;
}

function addUser() {
    global $link, $CODE_ERROR, $CODE_OK, $post_value, $CODE_USER, $CODE_RENT_OK;
    $user_json = json_decode($post_value, true);
    $user_name = $user_json['name'];
    $user_pass = getMd5Pass($user_json['password']);
    $user_email = $user_json['email'];
    $user_uuid = $user_json['uuid'];
    $user_status = $CODE_USER;
    $user_ray_status = $CODE_RENT_OK;
    $sql = "SELECT id FROM ray_users WHERE email='$user_email'";
    if ($link->query($sql)->num_rows != 0) returnJSON($CODE_ERROR, '邮箱地址已存在！');
    $sql = "SELECT id FROM ray_users WHERE uuid='$user_uuid'";
    if ($link->query($sql)->num_rows != 0) returnJSON($CODE_ERROR, 'UUID已存在！');
    $sql = "SELECT * FROM ray_emails WHERE email='$user_email'";
    if ($link->query($sql)->num_rows != 0) returnJSON($CODE_ERROR, '邮箱地址已存在于email中！');
    $sql = "INSERT INTO ray_users (name, pass, email, uuid, status, ray_status) VALUES ('$user_name', '$user_pass', '$user_email', '$user_uuid', '$user_status', '$user_ray_status')";
    if($link->query($sql) === false) returnJSON($CODE_ERROR, '更新数据时发生错误：' . $link -> error);
    $user_id = $link->insert_id;
    $sql = "INSERT INTO ray_emails (uid, email) VALUES ('$user_id', '$user_email')";
    if ($link->query($sql) === false) echo '添加用户至ray_users时发生错误：' . $link -> error;
    returnJSON($CODE_OK, '数据已更新');
}

function deleteUser() {
    global $link, $CODE_ERROR, $CODE_OK, $post_target, $post_id;
    if ($post_id == $post_target) returnJSON($CODE_ERROR, '不能对自己执行删除操作！');
    else if ($post_target == getSuperAdminID()) returnJSON($CODE_ERROR, '不能删除超级管理员！');
    $sql = "SELECT id FROM ray_users WHERE id='$post_target'";
    if ($link->query($sql)->num_rows != 0) {
        $sql = "DELETE FROM ray_users WHERE id='$post_target'";
        if($link->query($sql) === false) returnJSON($CODE_ERROR, '更新数据时发生错误：' . $link -> error);
    }
    $sql = "SELECT id FROM ray_emails WHERE uid='$post_target'";
    if ($link->query($sql)->num_rows != 0) {
        $sql = "DELETE FROM ray_emails WHERE uid='$post_target'";
        if($link->query($sql) === false) returnJSON($CODE_ERROR, '更新数据时发生错误：' . $link -> error);
    }
    returnJSON($CODE_OK, '数据已更新');
}