<?php
/**
 * 网页框架
 * Author: 诺天 <s235784@gmail.com>
 * Date  : 2021/6/27
 * Time  : 15:58
 */

$code_ini_web = parse_ini_file('admin/code.ini', true)['web'];
$config = parse_ini_file('admin/config.ini', true)['db'];
$link = new mysqli($config['host'], $config['user'], $config['password'], $config['db']);
if ($link->connect_error) echo die("连接数据库失败: " . $link->connect_error);
$link->set_charset('utf8');
$web_status = $code_ini_web['work'];
$key = 'web_status';
$sql = "SELECT * FROM ray_mate WHERE `key`='$key'";
$results = $link->query($sql);
if ($results->num_rows == 0) die('数据库受损！');
while($result = $results->fetch_assoc()) {
    $web_status = $result['value'];
}
if ($web_status == $code_ini_web['maintain']) {
    include 'maintain.php';
    die();
}

$content = 'home';
if (!empty($_GET['content']) && $_GET['content'] != null) {
    $content = $_GET['content'];
}
switch ($content) {
    case 'home':
        include 'home.php';
        break;
    case 'account':
        include 'account.php';
        break;
    case 'document':
        include 'document.php';
        break;
    case 'login':
        include 'login.php';
        break;
    case 'manage':
        include 'manage.php';
        break;
    case 'about':
        include 'about.php';
        break;
    default:
        include '404.html';
}