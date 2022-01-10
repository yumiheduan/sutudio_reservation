<?php

require_once('../../classes/util/SessionUtil.php');

// セッションスタート
SessionUtil::sessionStart();

if (empty($_SESSION['admin'])) {
    // 未ログインのとき
    header('Location: ../login/login.php');
} else {
    // ログイン済みのとき
    $admin = $_SESSION['admin'];
}

// セッションに保存したPOST、GETデータを削除
unset($_SESSION['post']);
unset($_SESSION['get']);

header('Location: ../studio/timetable.php');
