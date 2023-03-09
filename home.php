<?php
/**
 * 主页
 * Author: s235784 <s235784@gmail.com>
 * Date  : 2021/6/17
 * Time  : 20:11
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
    <title>主页 - FurRay</title>
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
                <li id="main-li" class="active">
                    <a style="padding: 0 32px" class="waves-effect" href="?content=home">主页</a>
                </li>
                <li id="account-li">
                    <a style="padding: 0 32px" class="waves-effect" href="?content=account">账户</a>
                </li>
                <li id="about-li">
                    <a style="padding: 0 32px" class="waves-effect" href="?content=about">关于</a>
                </li>
                <li><div class="divider" style="margin-bottom: 8px"></div></li>
                <li id="document-li">
                    <a style="padding: 0 32px" class="waves-effect" href="?content=document">食用方法</a>
                </li>
            </ul>
        </div>
    </nav>
</div>
<main id="main">
    <div class="container" id="main-container">
        <div class="row" style="margin-bottom: 0">
            <div class="col s12 m9 l10">
                <br><br>
                <blockquote class="blockquote-green">
                    <h4 style="margin-top: 5px">你好，<?php echo $user_name ?>！</h4>
                    <p class="caption" style="margin-top: 15px; margin-bottom: 5px;">欢迎来到FurRay，你可以在这里查看你的账户信息。</p>
                </blockquote>

                <br>
                <div id="account-info" class="section scrollspy">
                    <h5>账户信息</h5>
                    <div class="divider"></div>
                    <div style="margin-left: 5px; margin-top: 20px;">
                        <h6>账户类型：</h6>
                        <p style="margin-left:10px"><?php echo $user_role; ?></p>
                        <h6 style="margin-top: 5px">邮箱：</h6>
                        <p style="margin-left:10px"><?php echo $user_email; ?></p>
                        <h6 style="margin-top: 5px">合租状态：</h6>
                        <p style="margin-left:10px"><?php if ($user_ray_status == $code_config['ok']) echo '续租中'; else echo '已停租'; ?></p>
                    </div>
                    <br>
                </div>
                <div id="ray-info" class="section scrollspy">
                    <div class="valign-wrapper">
                        <h5 class="valign">配置信息</h5>
                        <span class="chip chip-green">XRay</span>
                    </div>
                    <div class="divider"></div>
                    <div style="margin-left: 5px; margin-top: 20px;" class="row">
                        <div class="col s12 m6">
                            <h6>协议类型：</h6>
                            <p style="margin-left:10px"><?php echo returnMate('protocol'); ?></p>
                            <h6 style="margin-top: 5px">别名：</h6>
                            <p style="margin-left:10px">*随意填写</p>
                            <div class="valign-wrapper" style="margin-left:5px">
                                <h6 class="valign">地址：</h6>
                                <a style="padding-top: 5px" href="javascript:copyAddress()">
                                    <i class="material-icons" style="margin-left: 2px; font-size: 1rem;">content_copy</i>
                                </a>
                            </div>
                            <p style="margin-left:10px" id="p-address"><?php echo returnMate('address'); ?></p>
                            <h6 style="margin-top: 5px">端口：</h6>
                            <p style="margin-left:10px"><?php echo returnMate('port'); ?></p>
                            <h6 style="margin-top: 5px"></h6>
                            <div class="valign-wrapper" style="margin-left:5px">
                                <h6 class="valign">用户ID：</h6>
                                <a style="padding-top: 5px" href="javascript:copyUUID()">
                                    <i class="material-icons" style="margin-left: 2px; font-size: 1rem;">content_copy</i>
                                </a>
                            </div>
                            <p style="margin-left:10px" id="p-uuid"><?php echo $user_uuid; ?></p>
                            <h6 style="margin-top: 5px">控流：</h6>
                            <p style="margin-left:10px"><?php echo returnMate('flow'); ?></p>
                            <h6 style="margin-top: 5px">加密：</h6>
                            <p style="margin-left:10px"><?php echo returnMate('encryption'); ?></p>
                            <h6 style="margin-top: 5px">传输协议：</h6>
                            <p style="margin-left:10px"><?php echo returnMate('network'); ?></p>
                        </div>
                        <div class="col s12 m6">
                            <h6 style="margin-top: 5px">伪装类型：</h6>
                            <p style="margin-left:10px"><?php echo returnMate('type'); ?></p>
                            <h6 style="margin-top: 5px">伪装域名：</h6>
                            <p style="margin-left:10px"><?php echo returnMate('host'); ?></p>
                            <h6 style="margin-top: 5px">路径：</h6>
                            <p style="margin-left:10px"><?php echo returnMate('path'); ?></p>
                            <h6 style="margin-top: 5px">底层传输安全：</h6>
                            <p style="margin-left:10px"><?php echo returnMate('tls'); ?></p>
                            <h6 style="margin-top: 5px">跳过证书验证：</h6>
                            <p style="margin-left:10px"><?php echo returnMate('allowInsecure'); ?></p>
                            <h6 style="margin-top: 5px">SNI：</h6>
                            <p style="margin-left:10px"><?php echo returnMate('sni'); ?></p>
                        </div>
                        <div class="row">
                            <div class="col s6 m3">
                                <h6 style="margin-top: 5px">示例：</h6>
                                <img class="materialboxed responsive-img lazy" src="img/example.png" alt="example">
                            </div>
                        </div>
                        <p>*以上内容的用法请点击左侧菜单内的“食用方法”</p>
                    </div>
                </div>
            </div>
            <div class="col hide-on-small-only m3 l2">
                <div class="tabs-wrapper">
                    <div style="height: 1px;">
                        <ul class="section table-of-contents">
                            <li><a href="#account-info">账户信息</a></li>
                            <li><a href="#ray-info">配置信息</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
</main>
<?php include_once('footer.html'); ?>
<script>
    $(function()
    {
        setLeftPadding();
        $(window).resize(function ()
        {
            setLeftPadding();
        });
        $("img.lazy").lazyload();
        $(".button-collapse").sideNav();
        $('.collapsible').collapsible();
        $('.parallax').parallax();
        $('.scrollspy').scrollSpy();
        $('.tabs-wrapper').pushpin({
            top: 200,
            bottom: Infinity,
            offset: 0
        });
    });

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

    function copyUUID() {
        copyToClip($('#p-uuid').text());
        Materialize.toast('复制成功!', 2000);
    }

    function copyAddress() {
        copyToClip($('#p-address').text());
        Materialize.toast('复制成功!', 2000);
    }

    function copyToClip(content) {
        const aux = document.createElement("input");
        aux.setAttribute("value", content);
        document.body.appendChild(aux);
        aux.select();
        document.execCommand("copy");
        document.body.removeChild(aux);
    }

    function logout() {
        document.cookie = "uid=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "pass=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        window.location.href = "?content=login";
    }
</script>
</body>
</html>

