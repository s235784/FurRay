<?php
/**
 * 账户
 * Author: s235784 <s235784@gmail.com>
 * Date  : 2021/6/18
 * Time  : 21:17
 */

$user_id   = '';
$user_pass = '';
$user_name = '';
$user_email = '';
$user_uuid = '';
$user_registered = '';
$user_ray_status = '';
$user_role = '用户';
$IS_ADMIN = false;

$user_id   = $_COOKIE['uid'];
$user_pass = $_COOKIE['pass'];
if (empty($user_id) || empty($user_pass)) returnLogin();

$code_config_user = parse_ini_file('admin/code.ini', true)['user'];
$code_admin = $code_config_user['admin'];
$user_ray_status = $code_config_user['ok'];

$code_config_status = parse_ini_file('admin/code.ini', true)['status'];
$CODE_OK = $code_config_status['ok'];

$db_config = parse_ini_file('admin/config.ini', true)['db'];
$link = new mysqli($db_config['host'], $db_config['user'], $db_config['password'], $db_config['db']);
if ($link->connect_error) die("数据库连接失败");

$link->set_charset('utf8');
$sql = "SELECT * FROM ray_users";
$results = $link->query($sql);
if ($results->num_rows == 0) die('数据库结构受损');

$sql = "SELECT name, pass, email, uuid, status, ray_status, registered FROM ray_users WHERE id='$user_id'";
$results = $link->query($sql);
if ($results->num_rows == 0) returnLogin();
while($result = $results->fetch_assoc()) {
    if (strcasecmp($user_pass,$result['pass']) !== 0) returnLogin();
    if ($result['status'] == $code_admin) $IS_ADMIN = true;
    if ($IS_ADMIN) $user_role = '管理员';
    $user_registered = $result['registered'];
    $user_ray_status = $result['ray_status'];
    $user_name = $result['name'];
    $user_email = $result['email'];
    $user_uuid = $result['uuid'];
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
    <title>账户 - FurRay</title>
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
                        <h5 style="color: rgba(0, 0, 0, 0.87); padding-left: 32px; padding-right: 10px" id="logo-user-name"><?php echo $user_name ?></h5>
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
                <li id="account-li" class="active">
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
        <br><br>
        <div class="row">
            <div class="input-field col s12 m6 l3">
                <input disabled value="<?php echo $user_id ?>" id="input-id" type="text" class="validate disabled-input-dark">
                <label for="input-id" class="disabled-label-dark">ID</label>
            </div>
            <div class="input-field col s12 m6 l3">
                <input disabled value="<?php echo $user_registered ?>" id="input-registered" type="text" class="validate disabled-input-dark">
                <label for="input-registered" class="disabled-label-dark">注册时间</label>
            </div>
            <div class="col s12 m6 l3">
                <a class="waves-effect waves-light btn btn-green" id="btn-edit-pass" href="#modal-edit-pass">修改密码</a>
            </div>
        </div>
        <!-- 修改密码的弹窗 -->
        <div id="modal-edit-pass" class="modal modal-fixed-footer">
            <div class="modal-content">
                <h4>修改密码</h4>
                <p>密码应在8到16位之间，由数字 字母 符号组成。</p>
                <div>
                    <div class="input-field">
                        <input id="input-pass-odd" type="password" class="validate">
                        <label for="input-pass-odd">当前密码</label>
                    </div>
                    <div class="input-field">
                        <input id="input-pass-now" type="password" class="validate">
                        <label for="input-pass-now">修改密码</label>
                    </div>
                    <div class="input-field">
                        <input id="input-pass-now-again" type="password" class="validate">
                        <label for="input-pass-now-again">再次输入</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a class="modal-action waves-effect waves-green btn-flat" id="btn-edit-pass-submit">确定</a>
                <a class="modal-action waves-effect waves-green btn-flat" id="btn-edit-pass-cancel">取消</a>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12 m6 l6">
                <input disabled value="<?php echo $user_name ?>" id="input-name" type="text" class="validate disabled-input-dark account-input-blur">
                <label for="input-name" class="disabled-label-dark">昵称</label>
            </div>
            <div class="col s12 m6 l6" style="margin-top: 10px">
                <a class="waves-effect waves-light btn btn-green" id="btn-edit-name"><i class="material-icons right">edit</i>更改</a>
                <div id="div-edit-name" style="display: none">
                    <a class="waves-effect waves-light btn btn-green" id="btn-edit-name-cancel">取消</a>
                    <a class="waves-effect waves-light btn btn-green" id="btn-edit-name-submit">完成</a>
                </div>
            </div>
            <div class="col s12">
                <p>网页上显示的称呼为你的昵称。</p>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12 m6 l6">
                <input disabled value="<?php echo $user_email ?>" id="input-email" type="email" class="validate disabled-input-dark" style="margin-bottom: 0">
                <label for="input-email" class="disabled-label-dark">邮箱</label>
            </div>
            <div class="col s12 m6 l6" style="margin-top: 10px">
                <a class="waves-effect waves-light btn btn-green" id="btn-edit-email"><i class="material-icons right">edit</i>更改</a>
                <div id="div-edit-email" style="display: none">
                    <a class="waves-effect waves-light btn btn-green" id="btn-edit-email-cancel">取消</a>
                    <a class="waves-effect waves-light btn btn-green" id="btn-edit-email-submit">完成</a>
                </div>
            </div>
            <div class="col s12">
                <p>登录网站和接收网站信息的邮箱。</p>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12 m6 l6">
                <input disabled value="<?php echo $user_uuid ?>" id="input-uuid" type="text" class="validate disabled-input-dark" style="margin-bottom: 0">
                <label for="input-uuid" class="disabled-label-dark">用户ID（UUID）</label>
            </div>
            <div class="col s12 m6 l6" style="margin-top: 10px">
                <div id="div-edit-uuid-first">
                    <a class="waves-effect waves-light btn btn-green" id="btn-edit-uuid-copy" href="javascript:copyUUID()"><i class="material-icons right">content_copy</i>复制</a>
                    <a class="waves-effect waves-light btn btn-green" id="btn-edit-uuid"><i class="material-icons right">edit</i>更改</a>
                </div>
                <div id="div-edit-uuid-second" style="display: none">
                    <a class="waves-effect waves-light btn btn-green" id="btn-edit-uuid-generate">生成</a>
                    <a class="waves-effect waves-light btn btn-green" id="btn-edit-uuid-cancel">取消</a>
                    <a class="waves-effect waves-light btn btn-green" id="btn-edit-uuid-submit">完成</a>
                </div>
            </div>
            <div class="col s12">
                <p>用户ID是你使用服务的唯一凭证，请妥善保管。</p>
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
        $('#modal-edit-pass').modal({
            dismissible: false,
            ready: function() {
                onEditPassModal();
            },
            complete: function() {
                $('#btn-edit-pass-submit').off('click');
                $('#btn-edit-pass-cancel').off('click');
            }
        });
    });

    function setLeftPadding() {
        if ( document.body.clientWidth >= 992)
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

    function logout() {
        document.cookie = "uid=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "pass=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        window.location.href = "?content=login";
    }

    function onEditPassModal() {
        let input_pass_new = $('#input-pass-now');
        let input_pass_odd = $('#input-pass-odd');
        let input_pass_new_again = $('#input-pass-now-again');
        $('#btn-edit-pass-submit').click(function () {
            const pass_odd = input_pass_odd.val();
            const pass_new = input_pass_new.val();
            const pass_new_again = input_pass_new_again.val();
            const reg = /\s/;
            if (pass_odd === '' || pass_new === '' || pass_new_again === '') Materialize.toast('还有信息你没有输入！', 2000);
            else if (pass_new.length <=7 || pass_new.length >= 17) Materialize.toast('新密码不符合规范！', 2000);
            else if (reg.test(pass_new)) Materialize.toast('密码中不能含有空格！', 2000);
            else if (pass_new === pass_odd) Materialize.toast('新旧密码不能相同！', 2000);
            else if (pass_new !== pass_new_again) Materialize.toast('确认密码与新密码不相同！', 2000);
            else submitPass(pass_odd, pass_new);
        });
        $('#btn-edit-pass-cancel').click(function () {
            destroyPassModal();
        });
    }

    function submitPass(pass_odd, pass_new) {
        const mateObject = {};
        mateObject['uid'] = getCookie('uid');
        mateObject['pass'] = pass_odd;
        mateObject['key'] = 'password';
        mateObject['value'] = pass_new;
        let mateJSON = JSON.stringify(mateObject);
        $.ajax({
            url:"api/updateMate.php",
            type:"post",
            data: mateJSON,
            dataType:"json",
            success: function (data) {
                Materialize.toast(data['msg'], 2000);
                if (data['status'] === <?php echo $CODE_OK ?>) {
                    document.cookie = 'pass=' + data['pass'];
                    destroyPassModal();
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                Materialize.toast(errorThrown, 2000);
            },
        });
    }

    function destroyPassModal() {
        $('#input-pass-now').val('');
        $('#input-pass-odd').val('');
        $('#input-pass-now-again').val('');
        $('#modal-edit-pass').modal('close');
    }

    let user_name_odd = '';
    let input_name = $('#input-name');
    let div_edit_name = $('#div-edit-name');
    let btn_edit_name = $('#btn-edit-name');
    btn_edit_name.click(function () {
        $('#btn-edit-name-submit').attr('disabled', true);
        btn_edit_name.hide();
        div_edit_name.show();
        input_name
            .removeAttr('disabled')
            .removeClass('account-input-blur')
            .addClass('account-input-focus')
            .focus().select()
        user_name_odd = input_name.val();
        input_name.bind("input propertychange",function () {
            if (input_name.val() !== user_name_odd && input_name.val() !== '')
                $('#btn-edit-name-submit').removeAttr('disabled');
            else $('#btn-edit-name-submit').attr('disabled', true);
        });
    });

    $('#btn-edit-name-cancel').click(function () {
        div_edit_name.hide();
        btn_edit_name.show();
        input_name
            .attr('disabled', true)
            .removeClass('account-input-focus')
            .addClass('account-input-blur')
            .val(user_name_odd);
        Materialize.updateTextFields();
    });

    $('#btn-edit-name-submit').click(function () {
        const callbackObject = {};
        callbackObject['success'] = function(data) {
            Materialize.toast(data['msg'], 2000);
            btn_edit_name.removeAttr('disabled');
            $('#logo-user-name').html(input_name.val());
        };
        callbackObject['error'] = function(XMLHttpRequest, textStatus, errorThrown) {
            Materialize.toast(errorThrown, 2000);
            btn_edit_name.removeAttr('disabled');
        };
        submitMate('name', input_name.val(), callbackObject);
        btn_edit_name.attr('disabled', true);
        div_edit_name.hide();
        btn_edit_name.show();
        input_name
            .attr('disabled', true)
            .removeClass('account-input-focus')
            .addClass('account-input-blur');
    });

    let user_email_odd = '';
    let input_email = $('#input-email');
    let div_edit_email = $('#div-edit-email');
    let btn_edit_email = $('#btn-edit-email');
    btn_edit_email.click(function () {
        $('#btn-edit-email-submit').attr('disabled', true);
        btn_edit_email.hide();
        div_edit_email.show();
        input_email
            .removeAttr('disabled')
            .removeClass('account-input-blur')
            .addClass('account-input-focus')
            .focus().select();
        user_email_odd = input_email.val();
        input_email.bind("input propertychange",function () {
            const reg = /^([a-zA-Z0-9_.-]+)@([da-z.-]+).([a-z.]{2,6})$/;
            if (reg.test(input_email.val()) && input_email.val() !== user_email_odd)
                $('#btn-edit-email-submit').removeAttr('disabled');
            else $('#btn-edit-email-submit').attr('disabled', true);
        });
    });

    $('#btn-edit-email-cancel').click(function () {
        div_edit_email.hide();
        btn_edit_email.show();
        input_email
            .attr('disabled', true)
            .removeClass('account-input-focus')
            .addClass('account-input-blur')
            .val(user_email_odd);
        Materialize.updateTextFields();
        input_email.off('change');
    });

    $('#btn-edit-email-submit').click(function () {
        const callbackObject = {};
        callbackObject['success'] = function(data) {
            Materialize.toast(data['msg'], 2000);
            btn_edit_email.removeAttr('disabled');
        };
        callbackObject['error'] = function(XMLHttpRequest, textStatus, errorThrown) {
            Materialize.toast(errorThrown, 2000);
            btn_edit_email.removeAttr('disabled');
        };
        submitMate('email', input_email.val(), callbackObject);
        btn_edit_email.attr('disabled', true);
        div_edit_email.hide();
        btn_edit_email.show();
        input_email
            .attr('disabled', true)
            .removeClass('account-input-focus')
            .addClass('account-input-blur');
    });

    let user_uuid_odd = '';
    let input_uuid = $('#input-uuid');
    let div_edit_uuid_first = $('#div-edit-uuid-first');
    let div_edit_uuid_second = $('#div-edit-uuid-second');
    $('#btn-edit-uuid').click(function () {
        $('#btn-edit-uuid-submit').attr('disabled', true);
        div_edit_uuid_first.hide();
        div_edit_uuid_second.show();
        input_uuid
            .removeAttr('disabled')
            .removeClass('account-input-blur')
            .addClass('account-input-focus')
            .focus().select();
        user_uuid_odd = input_uuid.val();
        input_uuid.bind("input propertychange",function () {
            const reg = /^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/;
            if (reg.test(input_uuid.val()) && input_uuid.val() !== user_uuid_odd)
                $('#btn-edit-uuid-submit').removeAttr('disabled');
            else $('#btn-edit-uuid-submit').attr('disabled', true);
        });
    });

    $('#btn-edit-uuid-cancel').click(function () {
        div_edit_uuid_second.hide();
        div_edit_uuid_first.show();
        input_uuid
            .attr('disabled', true)
            .removeClass('account-input-focus')
            .addClass('account-input-blur')
            .val(user_uuid_odd);
        Materialize.updateTextFields();
        input_uuid.off('change');
    });
    
    $('#btn-edit-uuid-generate').click(function () {
        input_uuid.val(generateUUID());
        $('#btn-edit-uuid-submit').removeAttr('disabled');
    });

    $('#btn-edit-uuid-submit').click(function () {
        const callbackObject = {};
        callbackObject['success'] = function(data) {
            Materialize.toast(data['msg'], 2000);
            $('#btn-edit-uuid').removeAttr('disabled');
        };
        callbackObject['error'] = function(XMLHttpRequest, textStatus, errorThrown) {
            Materialize.toast(errorThrown, 2000);
            $('#btn-edit-uuid').removeAttr('disabled');
        };
        submitMate('uuid', input_uuid.val(), callbackObject);
        $('#btn-edit-uuid').attr('disabled', true);
        div_edit_uuid_second.hide();
        div_edit_uuid_first.show();
        input_uuid
            .attr('disabled', true)
            .removeClass('account-input-focus')
            .addClass('account-input-blur');
    });

    function generateUUID() {
        return 'xxxxxxxx-xxxx-xxxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random()*16|0, v = c === 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        });
    }

    function copyUUID() {
        const aux = document.createElement("input");
        aux.setAttribute("value", input_uuid.val());
        document.body.appendChild(aux);
        aux.select();
        document.execCommand("copy");
        document.body.removeChild(aux);
        Materialize.toast('复制成功!', 2000);
    }

    function submitMate(key, value, callBack) {
        const mateObject = {};
        mateObject['uid'] = getCookie('uid');
        mateObject['pass'] = getCookie('pass');
        mateObject['key'] = key;
        mateObject['value'] = value;
        let mateJSON = JSON.stringify(mateObject);
        $.ajax({
            url:"api/updateMate.php",
            type:"post",
            data: mateJSON,
            dataType:"json",
            success: callBack['success'],
            error: callBack['error'],
        });
    }

    function getCookie(name) {
        const strCookie = document.cookie;
        const arrCookie = strCookie.split("; ");
        for (let i = 0; i < arrCookie.length; i++) {
            const arr = arrCookie[i].split("=");
            if (arr[0] === name){
                return arr[1];
            }
        }
        return "";
    }
</script>
</body>
</html>