<?php
/**
 * 维护模式界面
 * Author: 诺天 <s235784@gmail.com>
 * Date  : 2021/6/27
 * Time  : 16:32
 */

$db_config = parse_ini_file('admin/config.ini', true)['db'];
$link = new mysqli($db_config['host'], $db_config['user'], $db_config['password'], $db_config['db']);
if ($link->connect_error) die("数据库连接失败");
$link->set_charset('utf8');

$key = 'maintain_time';
$maintain_time = '';
$sql = "SELECT value FROM ray_mate WHERE `key`='$key'";
$results = $link->query($sql);
if ($results->num_rows == 0) die("数据库结构损坏！");
while($result = $results->fetch_assoc()) {
    $maintain_time = $result['value'];
}

?>

<!--
     _   _             _______  _
    | \ | |           |__   __|(_)
    |  \| | _   _   ___  | |    _   __ _  _ __
    | . ` || | | | / _ \ | |   | | / _` || '_ \
    | |\  || |_| || (_) || |   | || (_| || | | |
    |_| \_| \__,_| \___/ |_|   |_| \__,_||_| |_|

    Author: NuoTian (https://github.com/s235784)

-->

<!DOCTYPE html>
<html lang="zh-CN" style="height: 100%">
<head>
    <title>维护中 - FurRay</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="https://cafe.furcode.cn/favicon_32.ico">
    <link href="https://res.furcode.cn/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .center-div{
            margin: auto;
            position: absolute;
            height: 310px;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        @media screen and (min-width: 451px){
            .center-div{ width: 400px;}
        }
        @media screen and (max-width: 450px){
            .center-div{ width: 80%;}
        }
    </style>
</head>
<body style="height: 100%;">
<main style="height: 100%; width: 100%">
    <div class="center-div">
        <h3>抱歉，FurRay正在维护中</h3>
        <br>
        <h6>这通常不会花很长的时间，请稍后再访问</h6>
        <h6>或者你可以向管理员询问情况。</h6>
        <h6>开始时间：<?php echo $maintain_time; ?> </h6>
        <h6 id="request-address"></h6>
        <br>
        <div>
            <button onclick="window.location.reload()" type="button" class="btn btn-primary">重试</button>
            <button onclick="window.close()" style="margin-left: 10px" type="button" class="btn btn-primary">关闭网页</button>
        </div>
    </div>
</main>
<script>
    document.getElementById("request-address").innerText = "请求地址：" + GetUrlRelativePath();

    /**
     * @return {string}
     */
    function GetUrlRelativePath()
    {
        const url = document.location.toString();
        const arrUrl = url.split("//");
        const start = arrUrl[1].indexOf("/");
        let relUrl = arrUrl[1].substring(start);
        if(relUrl.indexOf("?") !== -1)
        {
            relUrl = relUrl.split("?")[0];
        }
        return relUrl;
    }
</script>
</body>
</html>
