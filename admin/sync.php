<?php
/**
 * 同步数据库与config.json内容
 * Author: s235784 <s235784@gmail.com>
 * Date  : 2021/6/22
 * Time  : 0:29
 */

$config = parse_ini_file('config.ini', true)['db'];
$link = new mysqli($config['host'], $config['user'], $config['password'], $config['db']);
if ($link->connect_error) echo die("连接数据库失败: " . $link->connect_error);
$link->set_charset('utf8');

$code_ini_web = parse_ini_file('code.ini', true)['web'];
$key = 'web_status';
$value = $code_ini_web['maintain'];
$sql = "UPDATE ray_mate SET value='$value' WHERE `key`='$key'";
$results = $link->query($sql);
if($link->query($sql) === false) die('数据库数据结构受损！');
$key = 'maintain_time';
$value = gmdate("Y-m-d H:i:s", time() + 8 * 3600);
$sql = "UPDATE ray_mate SET value='$value' WHERE `key`='$key'";
$results = $link->query($sql);
if($link->query($sql) === false) die('数据库数据结构受损！');
echo '维护模式已开启<br>';

$sql="select id, uuid, ray_status from ray_users";
$res=$link->query($sql);
$uuid_arr = array();
$status_arr = array();
while($row=$res->fetch_row()){
    $user_id = $row[0];
    $user_uuid = $row[1];
    $user_status = $row[2];
    $sql="select email from ray_emails where uid='$user_id'";
    $results = $link->query($sql);
    if ($results->num_rows == 0) die('数据库数据结构受损！');
    while($result = $results->fetch_assoc()) {
        $user_email = $result['email'];
        $uuid_arr[$user_email] = $user_uuid;
        $status_arr[$user_email] = $user_status;
    }
}
$res->free();

$config_ini = parse_ini_file('ray.ini', true)['xray'];
$config_string = file_get_contents('test/config.json');
$config_json = json_decode($config_string);
$ray_config_json_inbounds = $config_json->inbounds;
$ray_config_json_clients = null;
$inbound_id = 0;
foreach ($ray_config_json_inbounds as $ray_config_json_inbound) {
    if (strcasecmp($ray_config_json_inbound->protocol, $config_ini['protocol']) == 0) {
        break;
    } else $inbound_id ++;
}

$code_config = parse_ini_file('code.ini', true)['user'];
$code_config_ok = $code_config['ok'];
$config_json_clients = null;
$i = 0;
foreach ($uuid_arr as $key_email => $value_uuid) {
    if ($status_arr[$key_email] != $code_config_ok) continue;
    $config_json_client['email'] = $key_email;
    $config_json_client['id'] = $value_uuid;
    $config_json_client['flow'] = $config_ini['flow'];
    $config_json_client['level'] = 0;
    $config_json_clients[$i] = $config_json_client;
    $i ++;
}

if ($config_json_clients == null) die("写入数据至config失败：clients为空<br>");

$config_json->inbounds[$inbound_id]->settings->clients = $config_json_clients;
$config_string_new = json_encode($config_json, JSON_PRETTY_PRINT);
file_put_contents("test/config.json",$config_string_new);
echo '数据已写入文件<br>';

$key = 'web_status';
$value = $code_ini_web['work'];
$sql = "UPDATE ray_mate SET value='$value' WHERE `key`='$key'";
$results = $link->query($sql);
if($link->query($sql) === false) die('数据库数据结构受损！');
echo '维护模式已关闭<br>';
$key = 'sync_time';
$value = gmdate("Y-m-d H:i:s", time() + 8 * 3600);
$sql = "UPDATE ray_mate SET value='$value' WHERE `key`='$key'";
$results = $link->query($sql);
if($link->query($sql) === false) die('数据库数据结构受损！');
echo '同步时间已更新<br>';
echo '完成';