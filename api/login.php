<?php
/**
 * 登录接口的api
 * Author: s235784 <s235784@gmail.com>
 * Date  : 2021/6/17
 * Time  : 12:20
 */

include 'func.php';

header('charset=utf-8');
header('Content-type: application/json');

$code_config = parse_ini_file('../admin/code.ini', true)['status'];
$CODE_ERROR = $code_config['error'];
$CODE_OK    = $code_config['ok'];
$CODE_WRONG = $code_config['wrong'];

$input = file_get_contents('php://input');
$userJSON = json_decode($input, true);
$post_email = $userJSON['email'];
$post_pass  = $userJSON['pass'];

if (empty($post_email) || empty($post_pass)) returnJSON($CODE_ERROR, null, null, '请求不能为空！');

$db_config = parse_ini_file('../admin/config.ini', true)['db'];
$link = new mysqli($db_config['host'], $db_config['user'], $db_config['password'], $db_config['db']);
if ($link->connect_error) returnJSON($CODE_ERROR, null, null,"连接数据库失败: " . $link->connect_error);

$link->set_charset('utf8');
$sql = "SELECT * FROM ray_users";
$results = $link->query($sql);
if ($results->num_rows == 0) returnJSON($CODE_ERROR, null, null, '数据库中没有数据！');

$post_pass = getMd5Pass($post_pass);

$sql = "SELECT id, pass FROM ray_users WHERE email='{$post_email}'";
$results = $link->query($sql);
if ($results->num_rows == 0) returnJSON($CODE_WRONG, null, null, '邮箱或密码错误！');
while($result = $results->fetch_assoc()) {
    if (strcasecmp($post_pass,$result['pass']) == 0) returnJSON($CODE_OK, $result['id'], $result['pass'], null);
    else returnJSON($CODE_WRONG, null, null, '邮箱或密码错误！');
}

function returnJSON($status_code, $uid, $pass, $msg) {
    $arr = array('status' => (int)$status_code, 'uid' => $uid, 'pass' => $pass, 'msg' => $msg);
    echo json_encode($arr);
    die;
}