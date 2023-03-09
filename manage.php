<?php
/**
 * 管理
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
$code_ok = $code_config['ok'];
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

if (!$IS_ADMIN) {
    header("Location: index.php?content=home");
    die();
}

$sql = "SELECT * FROM ray_users";
$user_results = $link->query($sql);
$user_num = $user_results->num_rows;

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
    <title>管理 - FurRay</title>
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
                    <li id="manage-li" class="active">
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
                <li id="document-li">
                    <a style="padding: 0 32px" class="waves-effect" href="?content=document">食用方法</a>
                </li>
            </ul>
        </div>
    </nav>
</div>
<main id="main">
    <div class="container" id="main-container">
        <br>
        <div id="div-user-action">
            <h5>管理</h5>
            <div class="divider"></div>
            <br>
            <a class="waves-effect waves-light btn btn-green" onclick="openModalAdd()">创建新用户</a>
            <br>
            <a class="waves-effect waves-light btn btn-green" style="margin-top: 20px" onclick="openModalRestart()">重启XRay</a>
            <a class="waves-effect waves-light btn btn-green" style="margin-top: 20px" onclick="openModalSync()">同步配置</a>
            <a class="waves-effect waves-light btn btn-green" style="margin-top: 20px" onclick="openModalInit()">初始化配置</a>
        </div>
        <br><br>
        <!-- 新建用户的弹窗 -->
        <div id="modal-new" class="modal modal-fixed-footer">
            <div class="modal-content">
                <h4>创建新用户</h4>
                <div class="input-field" style="margin-top: 20px">
                    <input id="input-new-name" type="text" class="validate disabled-input-dark" style="margin-bottom: 0">
                    <label for="input-new-name" class="disabled-label-dark">昵称</label>
                </div>
                <div class="input-field" style="margin-top: 20px">
                    <input id="input-new-email" type="text" class="validate disabled-input-dark" style="margin-bottom: 0">
                    <label for="input-new-email" class="disabled-label-dark">邮箱地址</label>
                </div>
                <div class="input-field" style="margin-top: 20px">
                    <input id="input-new-pass" type="text" class="validate disabled-input-dark" style="margin-bottom: 0">
                    <label for="input-new-pass" class="disabled-label-dark">密码</label>
                </div>
                <div class="input-field" style="margin-top: 20px">
                    <input id="input-new-uuid" type="text" class="validate disabled-input-dark" style="margin-bottom: 0">
                    <label for="input-new-uuid" class="disabled-label-dark">UUID</label>
                </div>
                <a class="waves-effect waves-light btn btn-green" id="btn-new-uuid-generate" style="margin-top: 10px">生成UUID</a>
            </div>
            <div class="modal-footer">
                <a class="modal-action waves-effect waves-green btn-flat" id="btn-new-submit">确定</a>
                <a class="modal-action waves-effect waves-green btn-flat" id="btn-new-cancel">取消</a>
            </div>
        </div>
        <!-- 确认重启的弹窗 -->
        <div id="modal-action-restart" class="modal">
            <div class="modal-content">
                <h4>重启Xray</h4>
                <p>确认重启吗？这可能导致服务下线几分钟。</p>
            </div>
            <div class="modal-footer">
                <a class="modal-action waves-effect waves-green btn-flat" id="btn-action-restart-submit">确定</a>
                <a class="modal-action waves-effect waves-green btn-flat" id="btn-action-restart-cancel">取消</a>
            </div>
        </div>
        <!-- 确认同步的弹窗 -->
        <div id="modal-action-sync" class="modal">
            <div class="modal-content">
                <h4>同步配置</h4>
                <p>你的用户信息将会立即同步，此操作并将重启XRAY。</p>
            </div>
            <div class="modal-footer">
                <a class="modal-action waves-effect waves-green btn-flat" id="btn-action-sync-submit">确定</a>
                <a class="modal-action waves-effect waves-green btn-flat" id="btn-action-sync-cancel">取消</a>
            </div>
        </div>
        <!-- 确认初始化的弹窗 -->
        <div id="modal-action-init" class="modal">
            <div class="modal-content">
                <h4>初始化配置</h4>
                <p>确认初始化吗？这将更改数据库数据。</p>
            </div>
            <div class="modal-footer">
                <a class="modal-action waves-effect waves-green btn-flat" id="btn-action-init-submit">确定</a>
                <a class="modal-action waves-effect waves-green btn-flat" id="btn-action-init-cancel">取消</a>
            </div>
        </div>
        <div id="div-user-list">
            <h5>用户列表</h5>
            <div class="divider"></div>
            <p style="color: #757575">共有用户<?php echo $user_num; ?>人</p>
            <ul class="collapsible" data-collapsible="accordion" style="margin-top: 20px">
                <?php
                $i = 1;
                while($result = $user_results->fetch_assoc()) {
                    $list_id = $result['id'];
                    $list_name = $result['name'];
                    $list_email = $result['email'];
                    $list_uuid = $result['uuid'];
                    $list_authority = $result['status'];
                    $list_status = $result['ray_status'];
                    $list_time = $result['registered'];
                    ?>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">person</i><?php echo $i.'. '.$list_name.'（'.$list_email.'）'; ?></div>
                        <div class="collapsible-body">
                            <div style="padding: 2rem;">
                                <p style="padding: 0">ID：<?php echo $list_id; ?></p>
                                <p style="padding: 0">类型：<?php echo $list_authority == $code_admin ? '管理员' : '用户'; ?></p>
                                <p style="padding: 0" id="p-uuid" data-id="<?php echo $list_id; ?>" data-uuid="<?php echo $list_uuid; ?>">UUID：<?php echo $list_uuid; ?></p>
                                <p style="padding: 0">合租状态：<?php echo $list_status == $code_ok ? '续租中' : '已停租'; ?></p>
                                <p style="padding: 0;">注册时间：<?php echo $list_time; ?></p>
                                <a class="waves-effect waves-light btn btn-green" style="margin-top: 10px" onclick="openModalEmail(this)" data-id="<?php echo $list_id; ?>">修改邮箱</a>
                                <a class="waves-effect waves-light btn btn-green" style="margin-top: 10px" onclick="openModalAuthority(this)" data-id="<?php echo $list_id; ?>">更改类型</a>
                                <a class="waves-effect waves-light btn btn-green" style="margin-top: 10px" onclick="openModalUUID(this)" data-id="<?php echo $list_id; ?>">修改UUID</a>
                                <a class="waves-effect waves-light btn btn-green" style="margin-top: 10px" onclick="openModalRent(this)" data-id="<?php echo $list_id; ?>">修改状态</a>
                                <a class="waves-effect waves-light btn btn-green" style="margin-top: 10px" onclick="openModalPass(this)" data-id="<?php echo $list_id; ?>">修改密码</a>
                                <a class="waves-effect waves-light btn btn-red" style="margin-top: 10px" onclick="openModalDelete(this)" data-id="<?php echo $list_id; ?>">删除账户</a>
                            </div>
                        </div>
                    </li>
                <?php
                $i++;}
                ?>
            </ul>
            <!-- 修改邮箱的弹窗 -->
            <div id="modal-edit-email" class="modal modal-fixed-footer">
                <div class="modal-content">
                    <h4>修改邮箱</h4>
                    <div class="input-field" style="margin-top: 20px">
                        <input id="input-email" type="text" class="validate disabled-input-dark" style="margin-bottom: 0">
                        <label for="input-email" class="disabled-label-dark">邮箱地址</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="modal-action waves-effect waves-green btn-flat" id="btn-edit-email-submit">确定</a>
                    <a class="modal-action waves-effect waves-green btn-flat" id="btn-edit-email-cancel">取消</a>
                </div>
            </div>
            <!-- 更改类型的弹窗 -->
            <div id="modal-edit-authority" class="modal modal-fixed-footer">
                <div class="modal-content">
                    <h4>更改账户类型</h4>
                    <form action="#">
                        <p>
                            <input name="group-authority" type="radio" id="input-authority-admin" value="admin"/>
                            <label for="input-authority-admin">管理员</label>
                        </p>
                        <p>
                            <input name="group-authority" type="radio" id="input-authority-user" value="user"/>
                            <label for="input-authority-user">用户</label>
                        </p>
                    </form>
                </div>
                <div class="modal-footer">
                    <a class="modal-action waves-effect waves-green btn-flat" id="btn-edit-authority-submit">确定</a>
                    <a class="modal-action waves-effect waves-green btn-flat" id="btn-edit-authority-cancel">取消</a>
                </div>
            </div>
            <!-- 修改UUID的弹窗 -->
            <div id="modal-edit-uuid" class="modal modal-fixed-footer">
                <div class="modal-content">
                    <h4>修改UUID</h4>
                    <div class="input-field" style="margin-top: 20px">
                        <input id="input-uuid" type="text" class="validate disabled-input-dark" style="margin-bottom: 0">
                        <label for="input-uuid" class="disabled-label-dark">用户ID（UUID）</label>
                    </div>
                    <a class="waves-effect waves-light btn btn-green" id="btn-edit-uuid-generate" style="margin-top: 10px">生成</a>
                </div>
                <div class="modal-footer">
                    <a class="modal-action waves-effect waves-green btn-flat" id="btn-edit-uuid-submit">确定</a>
                    <a class="modal-action waves-effect waves-green btn-flat" id="btn-edit-uuid-cancel">取消</a>
                </div>
            </div>
            <!-- 修改合租状态的弹窗 -->
            <div id="modal-edit-rent" class="modal modal-fixed-footer">
                <div class="modal-content">
                    <h4>修改合租状态</h4>
                    <form action="#">
                        <p>
                            <input name="group-rent" type="radio" id="input-rent-ok" value="ok"/>
                            <label for="input-rent-ok">续租</label>
                        </p>
                        <p>
                            <input name="group-rent" type="radio" id="input-rent-stop" value="stop"/>
                            <label for="input-rent-stop">停租</label>
                        </p>
                    </form>
                </div>
                <div class="modal-footer">
                    <a class="modal-action waves-effect waves-green btn-flat" id="btn-edit-rent-submit">确定</a>
                    <a class="modal-action waves-effect waves-green btn-flat" id="btn-edit-rent-cancel">取消</a>
                </div>
            </div>
            <!-- 修改密码的弹窗 -->
            <div id="modal-edit-pass" class="modal modal-fixed-footer">
                <div class="modal-content">
                    <h4>修改密码</h4>
                    <div class="input-field" style="margin-top: 20px">
                        <input id="input-pass" type="text" class="validate disabled-input-dark" style="margin-bottom: 0">
                        <label for="input-pass" class="disabled-label-dark">密码</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="modal-action waves-effect waves-green btn-flat" id="btn-edit-pass-submit">确定</a>
                    <a class="modal-action waves-effect waves-green btn-flat" id="btn-edit-pass-cancel">取消</a>
                </div>
            </div>
            <!-- 删除账户的弹窗 -->
            <div id="modal-edit-delete" class="modal modal-fixed-footer">
                <div class="modal-content">
                    <h4>删除账户</h4>
                    <p>确定要删除此账户吗？此操作不可撤销。</p>
                </div>
                <div class="modal-footer">
                    <a class="modal-action waves-effect waves-red btn-flat" id="btn-edit-delete-submit">确定</a>
                    <a class="modal-action waves-effect waves-green btn-flat" id="btn-edit-delete-cancel">取消</a>
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
    });

    function logout() {
        document.cookie = "uid=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "pass=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        window.location.href = "?content=login";
    }

    function openModalAdd() {
        $('#modal-new').modal({
                dismissible: false,
                ready: function(modal) {
                    $('#btn-new-cancel').off('click').click(function () {
                        modal.modal('close');
                    });
                    $('#btn-new-submit').off('click').click(function () {
                        const name = $('#input-new-name').val();
                        const pass = $('#input-new-pass').val();
                        const email = $('#input-new-email').val();
                        const uuid = $('#input-new-uuid').val();
                        const reg_email = /^([a-zA-Z0-9_.-]+)@([da-z.-]+).([a-z.]{2,6})$/;
                        const reg_uuid = /^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/;

                        if (name == null || name === '') {
                            Materialize.toast('昵称不能为空！', 2000);
                            return;
                        }
                        if (pass == null || pass === '') {
                            Materialize.toast('密码不能为空！', 2000);
                            return;
                        }
                        if (email == null || email === '') {
                            Materialize.toast('邮箱不能为空！', 2000);
                            return;
                        }
                        if (uuid == null || uuid === '') {
                            Materialize.toast('UUID不能为空！', 2000);
                            return;
                        }
                        if (!reg_email.test(email)) {
                            Materialize.toast('邮箱格式不合法！', 2000);
                            return;
                        }
                        if (!reg_uuid.test(uuid)) {
                            Materialize.toast('UUID格式不合法！', 2000);
                            return;
                        }

                        const jsonObject = {'name':name, 'password': pass, 'email':email, 'uuid':uuid};
                        const value =  JSON.stringify(jsonObject);
                        submitMate('add', value, '', modal);
                    });
                    $('#btn-new-uuid-generate').off('click').click(function () {
                        $('#input-new-uuid').val(generateUUID());
                        Materialize.updateTextFields();
                    });
                },
                complete: function () {
                    $('#input-new-email').val('');
                    $('#input-new-name').val('');
                    $('#input-new-uuid').val('');
                    $('#input-new-pass').val('');
                }
            }
        ).modal('open');
    }

    function openModalRestart() {
        $('#modal-action-restart').modal({
                dismissible: false,
                ready: function(modal) {
                    $('#btn-action-restart-cancel').off('click').click(function () {
                        modal.modal('close');
                    });
                    $('#btn-action-restart-submit').off('click').click(function () {
                        submitAction('restart', modal);
                    })
                }
            }
        ).modal('open');
    }

    function openModalSync() {
        $('#modal-action-sync').modal({
                dismissible: false,
                ready: function(modal) {
                    $('#btn-action-sync-cancel').off('click').click(function () {
                        modal.modal('close');
                    });
                    $('#btn-action-sync-submit').off('click').click(function () {
                        submitAction('sync', modal);
                    })
                }
            }
        ).modal('open');
    }

    function openModalInit() {
        $('#modal-action-init').modal({
                dismissible: false,
                ready: function(modal) {
                    $('#btn-action-init-cancel').off('click').click(function () {
                        modal.modal('close');
                    });
                    $('#btn-action-init-submit').off('click').click(function () {
                        submitAction('init', modal);
                    })
                }
            }
        ).modal('open');
    }

    function submitAction(action, modal) {
        console.log(action);
        const mateObject = {};
        mateObject['uid'] = getCookie('uid');
        mateObject['pass'] = getCookie('pass');
        mateObject['action'] = action;
        let mateJSON = JSON.stringify(mateObject);
        $.ajax({
            url:"api/action.php",
            type:"post",
            data: mateJSON,
            dataType:"json",
            success: function (data) {
                Materialize.toast(data['msg'], 2000);
                if (data['status'] === 200) modal.modal('close');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                Materialize.toast(errorThrown, 2000);
            }
        });
    }

    function openModalEmail(btn) {
        const uid = $(btn).attr('data-id');
        $('#modal-edit-email').modal({
                dismissible: false,
                ready: function(modal) {
                    $('#btn-edit-email-cancel').off('click').click(function () {
                        modal.modal('close');
                    });
                    $('#btn-edit-email-submit').off('click').click(function () {
                        const value = $('#input-email').val();
                        console.log(value);
                        const reg = /^([a-zA-Z0-9_.-]+)@([da-z.-]+).([a-z.]{2,6})$/;
                        if (value == null || value === '') Materialize.toast('不能为空！', 2000);
                        else if (!reg.test(value)) Materialize.toast('邮箱格式不合法！', 2000);
                        else submitMate('email', value, uid, modal);
                    });
                },
                complete: function () {
                    $('#input-email').val('');
                }
            }
        ).modal('open');
    }

    function openModalAuthority(btn) {
        const uid = $(btn).attr('data-id');
        $('#modal-edit-authority').modal({
                dismissible: false,
                ready: function(modal) {
                    $('#btn-edit-authority-cancel').off('click').click(function () {
                        modal.modal('close');
                    });
                    $('#btn-edit-authority-submit').off('click').click(function () {
                        const value = $("input[name='group-authority']:checked").val();
                        console.log(value);
                        if (value == null) Materialize.toast('请选择一项！', 2000);
                        else submitMate('authority', value, uid, modal);
                    })
                }
            }
        ).modal('open');
    }

    function openModalUUID(btn) {
        const uid = $(btn).attr('data-id');
        const uuid = $("#p-uuid[data-id='"+uid+"']").attr('data-uuid');
        console.log(uuid);
        $('#modal-edit-uuid').modal({
                dismissible: false,
                ready: function(modal) {
                    $('#input-uuid').val(uuid);
                    Materialize.updateTextFields();
                    $('#btn-edit-uuid-cancel').off('click').click(function () {
                        modal.modal('close');
                    });
                    $('#btn-edit-uuid-submit').off('click').click(function () {
                        const value = $('#input-uuid').val();
                        console.log(value);
                        const reg = /^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/;
                        if (value == null || value === '') Materialize.toast('不能为空！', 2000);
                        else if (!reg.test(value)) Materialize.toast('UUID格式不合法！', 2000);
                        else submitMate('uuid', value, uid, modal);
                    });
                    $('#btn-edit-uuid-generate').off('click').click(function () {
                        $('#input-uuid').val(generateUUID());
                    });
                },
                complete: function () {
                    $('#input-uuid').val('');
                }
            }
        ).modal('open');
    }

    function openModalRent(btn) {
        const uid = $(btn).attr('data-id');
        $('#modal-edit-rent').modal({
                dismissible: false,
                ready: function(modal) {
                    $('#btn-edit-rent-cancel').off('click').click(function () {
                        modal.modal('close');
                    });
                    $('#btn-edit-rent-submit').off('click').click(function () {
                        const value = $("input[name='group-rent']:checked").val();
                        console.log(value);
                        if (value == null) Materialize.toast('请选择一项！', 2000);
                        else submitMate('rent', value, uid, modal);
                    })
                }
            }
        ).modal('open');
    }

    function openModalPass(btn) {
        const uid = $(btn).attr('data-id');
        $('#modal-edit-pass').modal({
                dismissible: false,
                ready: function(modal) {
                    $('#btn-edit-pass-cancel').off('click').click(function () {
                        modal.modal('close');
                    });
                    $('#btn-edit-pass-submit').off('click').click(function () {
                        const value = $('#input-pass').val();
                        console.log(value);
                        if (value == null || value === '') Materialize.toast('不能为空！', 2000);
                        else submitMate('password', value, uid, modal);
                    });
                },
                complete: function () {
                    $('#input-pass').val('');
                }
            }
        ).modal('open');
    }

    function openModalDelete(btn) {
        const uid = $(btn).attr('data-id');
        $('#modal-edit-delete').modal({
                dismissible: false,
                ready: function(modal) {
                    $('#btn-edit-delete-cancel').off('click').click(function () {
                        modal.modal('close');
                    });
                    $('#btn-edit-delete-submit').off('click').click(function () {
                        submitMate('delete', '', uid, modal);
                    });
                }
            }
        ).modal('open');
    }

    function submitMate(key, value, target, modal) {
        console.log(target);
        const mateObject = {};
        mateObject['uid'] = getCookie('uid');
        mateObject['pass'] = getCookie('pass');
        mateObject['key'] = key;
        mateObject['value'] = value;
        mateObject['target'] = target;
        let mateJSON = JSON.stringify(mateObject);
        $.ajax({
            url:"api/updateMateAdmin.php",
            type:"post",
            data: mateJSON,
            dataType:"json",
            success: function (data) {
                Materialize.toast(data['msg'], 2000);
                if (data['status'] === 200) modal.modal('close');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                Materialize.toast(errorThrown, 2000);
            }
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

    function generateUUID() {
        return 'xxxxxxxx-xxxx-xxxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random()*16|0, v = c === 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        });
    }
</script>
</body>
</html>