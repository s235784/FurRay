<?php
/**
 *
 * Author: s235784 <s235784@gmail.com>
 * Date  : 2021/6/17
 * Time  : 15:51
 */

include_once '../api/func.php';

$config = parse_ini_file('config.ini', true)['db'];
$link = new mysqli($config['host'], $config['user'], $config['password'], $config['db']);
if ($link->connect_error) echo die("连接数据库失败: " . $link->connect_error);

$link->set_charset('utf8');

$sql = "CREATE TABLE IF NOT EXISTS `ray_users`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `status` int(10) NOT NULL,
  `ray_status` int(10) NOT NULL,
  `registered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)";
if ($link->query($sql) === false) {
    die("创建数据表失败: " . $link -> error);
}

$sql = "CREATE TABLE IF NOT EXISTS `ray_emails`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
)";
if ($link->query($sql) === false) {
    die("创建数据表失败: " . $link -> error);
}

$sql = "CREATE TABLE IF NOT EXISTS `ray_mate`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
)";
if ($link->query($sql) === false) {
    die("创建数据表失败: " . $link -> error);
}

$code_config = parse_ini_file('code.ini', true)['user'];
$admin_config = parse_ini_file('config.ini', true)['admin'];
$admin_name = $admin_config['name'];
$admin_email= $admin_config['email'];
$admin_pass = $admin_config['pass'];
$admin_uuid = $admin_config['uuid'];
$admin_status = $code_config['admin'];
$admin_ray_status = $code_config['ok'];

$ray_config_ini = parse_ini_file('ray.ini', true)['xray'];
$ray_config_content = file_get_contents('test/config.json');
$ray_config_json = json_decode($ray_config_content, true);
$ray_config_json_inbounds = $ray_config_json['inbounds'];
$ray_config_json_clients = null;
foreach ($ray_config_json_inbounds as $ray_config_json_inbound) {
    if (strcasecmp($ray_config_json_inbound['protocol'], $ray_config_ini['protocol']) == 0) {
        $ray_config_json_clients = $ray_config_json_inbound['settings']['clients'];
        echo '已发现对应的inbound<br>';
        break;
    } else die('找不到对应的inbound<br>');
}

echo '开始分析client<br>';

if ($ray_config_json_clients == null) die("创建数据表失败: 无法找到对应的config文件<br>");
foreach ($ray_config_json_clients as $ray_config_json_client) {
    $is_admin = false;
    $user_email = $ray_config_json_client['email'];
    $user_uuid  = $ray_config_json_client['id'];
    $user_name = $user_email;
    $user_pass = getMd5Pass($user_email);
    $user_status = $code_config['user'];
    $user_ray_status = $code_config['ok'];
    $user_id = 0;
    if ($user_email == $admin_email || $user_uuid == $admin_uuid) {
        $user_name = $admin_name;
        $user_pass = getMd5Pass($admin_pass);
        $user_email = $admin_email;
        $user_uuid = $ray_config_json_client['id'];
        $user_status = $code_config['admin'];
        echo '发现管理员<br>';
    }
    $sql = "SELECT * FROM ray_users WHERE uuid='{$user_uuid}'";
    $results = $link->query($sql);
    if ($results->num_rows == 0) {
        $sql = "INSERT INTO ray_users (name, pass, email, uuid, status, ray_status) VALUES ('$user_name', '$user_pass', '$user_email', '$user_uuid', '$user_status', '$user_ray_status')";
        if ($link->query($sql) === false) echo '添加用户至ray_users时发生错误<br>';
        else $user_id = $link->insert_id;
    } else {
        $sql = "SELECT id FROM ray_users WHERE uuid='{$user_uuid}'";
        $results = $link->query($sql);
        while($result = $results->fetch_assoc()) {
            $user_id = $result['id'];
        }
        echo '用户已存在于ray_users<br>';
    }
    if ($user_id == 0) die('错误<br>');

    $user_email = $ray_config_json_client['email'];
    $sql = "SELECT * FROM ray_emails WHERE email='{$user_email}'";
    $results = $link->query($sql);
    if ($results->num_rows == 0) {
        $sql = "INSERT INTO ray_emails (uid, email) VALUES ('$user_id', '$user_email')";
        if ($link->query($sql) === false) echo '添加用户至ray_users时发生错误';
    } else echo '用户已存在于ray_emails<br>';
    var_dump($user_id);
    echo '<br>';
    var_dump($user_email);
    echo '<br>';
    var_dump($user_uuid);
    echo '<br><br>';
}

$key = 'web_status';
$value = parse_ini_file('code.ini', true)['web']['work'];
$sql = "SELECT * FROM ray_mate WHERE `key`='$key'";
$results = $link->query($sql);
if ($results->num_rows == 0) {
    $sql = "INSERT INTO ray_mate (`key`, `value`) VALUES ('$key', '$value')";
    if ($link->query($sql) === false) echo '写入ray_mate时发生错误<br>';
    else echo '已创建web status<br>';
} else echo '已存在web status<br>';

$key = 'maintain_time';
$value = gmdate("Y-m-d H:i:s", time() + 8 * 3600);
$sql = "SELECT * FROM ray_mate WHERE `key`='$key'";
$results = $link->query($sql);
if ($results->num_rows == 0) {
    $sql = "INSERT INTO ray_mate (`key`, `value`) VALUES ('$key', '$value')";
    if ($link->query($sql) === false) echo '写入ray_mate时发生错误<br>';
    else echo '已创建maintain time<br>';
} else echo '已存在maintain time<br>';

$key = 'sync_time';
$value = gmdate("Y-m-d H:i:s", time() + 8 * 3600);
$sql = "SELECT * FROM ray_mate WHERE `key`='$key'";
$results = $link->query($sql);
if ($results->num_rows == 0) {
    $sql = "INSERT INTO ray_mate (`key`, `value`) VALUES ('$key', '$value')";
    if ($link->query($sql) === false) echo '写入ray_mate时发生错误<br>';
    else echo '已创建sync time<br>';
} else echo '已存在sync time<br>';

//$sql = "SELECT * FROM ray_users WHERE status='{$admin_status}'";
//$results = $link->query($sql);
//if ($results->num_rows !== 0) die('数据库结构完整，无需初始化');

//if (empty($admin_uuid)) $admin_uuid = uuid();
//
//$sql = "INSERT INTO ray_users (name, pass, email, uuid, status, ray_status) VALUES ('$admin_name', '$admin_pass', '$admin_email', '$admin_uuid', '$admin_status', '$admin_ray_status')";
//if ($link->query($sql) === false) {
//    die("创建数据表失败: " . $link -> error);
//}

echo '初始化完成';