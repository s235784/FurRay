<?php
/**
 * 用户登录界面
 * Author: s235784 <s235784@gmail.com>
 * Date  : 2021/6/17
 * Time  : 12:21
 */

$allow_register = false;
$allow_findPass = false;

$code_config = parse_ini_file('admin/code.ini', true)['status'];
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
    <title>登录 - FurRay</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="shortcut icon" href="https://cafe.furcode.cn/favicon_32.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body style="height: 100%; min-width: 400px">
<main style="height: 100%">
    <div style="position: relative; height: 100%; width: 100%;">
        <div style="position: absolute; width:100%; height: 100%;">
            <div style="height: 100%; width: 100%">
                <div class="card" style="opacity:0.8; margin: auto; position: absolute; width: 400px; height: 310px; top: 0; left: 0; right: 0; bottom: 0;">
                    <div class="card-body">
                        <h3 style="text-align:center;">FurRay</h3>
                        <h5 class="card-title" style="text-align:center;">登录</h5>
                        <form role="search" method="get" action="">
                            <h6>邮箱</h6>
                            <label style="width: 100%">
                                <input class="form-control" type="text" id="input-email"/>
                            </label>
                            <h6 style="margin-top: 8px">密码</h6>
                            <div class="input-group mb-3">
                                <input type="password" class="form-control" aria-describedby="btn-find-pass" id="input-pass">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" id="btn-find-pass">忘记密码?</button>
                                </div>
                            </div>
                            <div style="margin-top: 8px">
                                <button type="button" class="btn btn-link mx-auto" id="btn-register">注册</button>
                                <button style="float: right;" type="submit" class="btn btn-outline-success" id="btn-submit">登录</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div aria-live="polite" aria-atomic="true" style="position: relative; min-height: 200px;">
            <div class="toast" style="position: absolute; top: 0; right: 0; margin-top: 8px; margin-right: 8px" data-delay="2000">
                <div class="toast-header">
                    <strong class="mr-auto">错误提示</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body" id="toast-msg">
                    这是错误提示
                </div>
            </div>
        </div>
        <div style="position: absolute; bottom: 0; opacity:0.5; border: none !important; margin-left: 8px; margin-bottom: 8px;" class="card">
            <div style="text-align: center" class="card-body bg-dark">
                <h6 class="text-muted copyright-div">Copyright © 2018 FurRay</h6>
                <h6 class="text-muted background-div">Background By 猫舌かぐ</h6>
            </div>
        </div>
    </div>
</main>
</body>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="js/jquery.backstretch.min.js"></script>
<script>
    const images = {
        url: [
            "https://cafe.furcode.cn/nuotian/background-猫舌かぐ.png",
            "https://cafe.furcode.cn/nuotian/background-うまに.jpg",
            "https://cafe.furcode.cn/nuotian/background-外.jpg",
            "https://cafe.furcode.cn/nuotian/background-うまに2.png"
        ],
        painter: [
            "猫舌かぐ",
            "うまに",
            "外",
            "うまに"
        ],
        link: [
            "https://twitter.com/nekojitaaaaaaaa",
            "https://twitter.com/umani",
            "https://twitter.com/totatetta_",
            "https://twitter.com/umani"
        ]
    };

    $(window).on("backstretch.before", function (e, instance, index) {
        const background_info = "Background By <a class='text-muted' target='_blank' href='"+ images.link[index] +"'>"+ images.painter[index] +"</a>";
        $(".background-div").html(background_info);
    });
    $.backstretch(images.url, {blurRadius: 3, duration:8000, fade:1000,});
    const div_backstretch = $('.backstretch');
    div_backstretch.css("transform", "scale(1.01)");
    div_backstretch.css("pointer-events", "none");

    const year = new Date().getFullYear();
    $(".copyright-div").html("Copyright © " + year + " <a class='text-muted' target='_blank' href='https://nuotian.furry.pro/'>FurRay</a>");

    $('#btn-submit').click(function () {
        $(this).attr('disabled', true);
        $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> 登录');
        const email = $('#input-email').val();
        const pass = $('#input-pass').val();
        const userObject = {};
        userObject.email = email;
        userObject.pass = pass;
        const userJSON = JSON.stringify(userObject);
        $.ajax({
            url:"api/login.php",
            type:"post",
            data: userJSON,
            dataType:"json",
            success: function(data) {
                console.log(data);
                if (data['status'] === <?php echo $code_config['ok'] ?>) {
                    document.cookie = 'uid=' + data['uid'];
                    document.cookie = 'pass=' + data['pass'];
                    window.location.href = "?content=home";
                }
                else onFailure(data['msg']);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                onFailure(errorThrown);
            },
        });
    })

    $('#btn-find-pass').click(function () {
        $('.toast').toast('show');
        $('#toast-msg').html('功能还未完成');
    });

    $('#btn-register').click(function () {
        $('.toast').toast('show');
        $('#toast-msg').html('不开放注册');
    });

    function onFailure(msg) {
        $('.toast').toast('show');
        $('#toast-msg').html(msg);
        $('#btn-submit').attr('disabled', false)
            .html('登录');
    }
</script>
</html>

