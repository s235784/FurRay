<?php
/**
 * 公共方法
 * Author: s235784 <s235784@gmail.com>
 * Date  : 2021/6/17
 * Time  : 17:06
 */

function  uuid(): string {
    $chars = md5(uniqid(mt_rand(), true));
    return substr ( $chars, 0, 8 ) . '-'
        . substr ( $chars, 8, 4 ) . '-'
        . substr ( $chars, 12, 4 ) . '-'
        . substr ( $chars, 16, 4 ) . '-'
        . substr ( $chars, 20, 12 );
}

function getMd5Pass(string $pass): string {
    return md5($pass . '@a1fb9b981');
}