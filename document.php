<?php
/**
 * 食用教程
 * Author: s235784 <s235784@gmail.com>
 * Date  : 2021/6/18
 * Time  : 21:18
 */

$user_id   = '';
$user_pass = '';
$user_name = '';
$user_email = '';
$user_uuid = '';
$user_ray_status = '';
$user_role = '用户';
$IS_ADMIN = false;

$user_id   = $_COOKIE['uid'];
$user_pass = $_COOKIE['pass'];
if (empty($user_id) || empty($user_pass)) returnLogin();

$code_config = parse_ini_file('admin/code.ini', true)['user'];
$code_admin = $code_config['admin'];
$user_ray_status = $code_config['ok'];

$db_config = parse_ini_file('admin/config.ini', true)['db'];
$link = new mysqli($db_config['host'], $db_config['user'], $db_config['password'], $db_config['db']);
if ($link->connect_error) die("数据库连接失败");

$link->set_charset('utf8');
$sql = "SELECT * FROM ray_users";
$results = $link->query($sql);
if ($results->num_rows == 0) die('数据库结构受损');

$sql = "SELECT name, pass, email, uuid, status, ray_status FROM ray_users WHERE id='$user_id'";
$results = $link->query($sql);
if ($results->num_rows == 0) returnLogin();
while($result = $results->fetch_assoc()) {
    if (strcasecmp($user_pass,$result['pass']) !== 0) returnLogin();
    if ($result['status'] == $code_admin) $IS_ADMIN = true;
    if ($IS_ADMIN) $user_role = '管理员';
    $user_name = $result['name'];
    $user_email = $result['email'];
    $user_uuid = $result['uuid'];
    $user_ray_status = $result['ray_status'];
}

function returnLogin() {
    header("Location: index.php?content=login");
    exit();
}

$ray_config = parse_ini_file('admin/ray.ini', true)['xray'];
function returnMate($key) {
    global $ray_config;
    $value = $ray_config[$key];
    if (empty($value) || $value == 'null') $value = '*留空不填';
    return $value;
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
<html lang="zh-CN">
<head>
    <title>教程 - FurRay</title>
    <meta charset="utf-8">
    <meta name="description" content=""/>
    <meta name="theme-color" content="#43a047">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="shortcut icon" href="https://cafe.furcode.cn/favicon_32.ico">
    <link href="css/styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="https://res.furcode.cn/materialize/0.97.8/css/materialize.css"  media="screen,projection"/>
    <script type="text/javascript" src="https://res.furcode.cn/jquery/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://res.furcode.cn/materialize/0.97.8/js/materialize.min.js"></script>
    <script type="text/javascript" src="https://res.furcode.cn/jquery/lazyload/1.9.5/lazyload.min.js"></script>
</head>
<body>
<div class="parallax-container">
    <div class="parallax"><img alt="img_index_parallax" src="https://code.furcode.cn/images/parallax_index.jpg" class="lazy"></div>
    <nav class="index_nav">
        <div class="nav-wrapper">
            <a class="brand-logo hide-on-large-only ">FurRay</a>
            <a class="brand-logo hide-on-med-and-down" style="margin-left: 320px">FurRay</a>
            <a href="#" data-activates="mobile-nav" class="button-collapse"><i class="material-icons">menu</i></a>
            <ul class="side-nav fixed" id="mobile-nav" style="padding-top: 8px">
                <li id="logo-li">
                    <div class="center">
                        <img src="img/logo.png" class="circle responsive-img logo-img lazy" alt="logo">
                    </div>
                    <div class="valign-wrapper">
                        <h5 style="color: rgba(0, 0, 0, 0.87); padding-left: 32px; padding-right: 10px"><?php echo $user_name ?></h5>
                        <span class="chip chip-green"><?php echo $user_role; ?></span>
                    </div>
                </li>
                <?php if($IS_ADMIN) { ?>
                    <li id="manage-li">
                        <a style="padding: 0 32px" class="waves-effect" href="?content=manage">管理</a>
                    </li>
                <?php } ?>
                <li id="logout-li">
                    <a style="padding: 0 32px" class="waves-effect" href="javascript:logout()">退出登录</a>
                </li>
                <li><div class="divider" style="margin-bottom: 8px"></div></li>
                <li id="main-li">
                    <a style="padding: 0 32px" class="waves-effect" href="?content=home">主页</a>
                </li>
                <li id="account-li">
                    <a style="padding: 0 32px" class="waves-effect" href="?content=account">账户</a>
                </li>
                <li id="about-li">
                    <a style="padding: 0 32px" class="waves-effect" href="?content=about">关于</a>
                </li>
                <li><div class="divider" style="margin-bottom: 8px"></div></li>
                <li id="document-li" class="active">
                    <a style="padding: 0 32px" class="waves-effect" href="?content=document">食用方法</a>
                </li>
            </ul>
        </div>
    </nav>
</div>
<main id="main">
    <div class="container">
        <div id="div-main-loading">
            <div class="progress" style="margin-top: 10px">
                <div class="indeterminate"></div>
            </div>
        </div>
        <div id="div-main-container"></div>
    </div>
    <br>
</main>
<?php include_once('footer.html'); ?>
<script src="https://res.furcode.cn/scrollreveal/4.0.6/scrollreveal.js"></script>
<script src="https://res.furcode.cn/showdown/1.9.1/showdown.min.js"></script>
<script>
    $(function()
    {
        function setLeftPadding() {
            if ( document.body.clientWidth >= 993)
            {
                $("#main").css("padding-left","300px");
                $("#footer-info").css("padding-left","300px");
                $("#footer-copyright").css("padding-left","300px");
            } else
            {
                $("#main").css("padding-left","0");
                $("#footer-info").css("padding-left","0");
                $("#footer-copyright").css("padding-left","0");
            }
        }
        setLeftPadding();
        $(window).resize(function ()
        {
            setLeftPadding();
        });
        $("img.lazy").lazyload();
        $(".button-collapse").sideNav();
        $('.collapsible').collapsible();
        $('.parallax').parallax();

        const url = 'https://example.com/使用说明.md';
        startAJAXAnimation($('#div-main-container'), url, $('#div-main-loading'))
    });

    function logout() {
        document.cookie = "uid=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "pass=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        window.location.href = "?content=login";
    }

    function startAJAXAnimation(contentJquery, url, loadingJquery) {
        let request;
        try {
            request = new XMLHttpRequest();
        } catch(e) {
            try {
                request = new ActiveXObject('Msxml2.XMLHTTP');
            } catch(e) {
                request = new ActiveXObject('Microsoft.XMLHTTP');
            }
        }
        request.onreadystatechange = function() {
            if (request.readyState === 4) {
                loadingJquery.hide();
                if (request.status === 200) {
                    ScrollReveal().reveal(contentJquery, {
                        duration: 1500,
                        beforeReveal: function () {
                            const converter = new showdown.Converter();
                            contentJquery.html(converter.makeHtml(request.responseText));
                        }
                    });
                } else {
                    contentJquery.html('获取内容时发生错误！');
                    Materialize.toast('获取内容失败，错误' + request.status, 4000);
                }
            }
        };
        request.open('GET', url, true);
        request.send();
    }
</script>
</body>
</html>