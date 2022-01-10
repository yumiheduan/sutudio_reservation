<?php
require_once('../../classes/util/SessionUtil.php');

// セッションスタート
SessionUtil::sessionStart();

// ログインユーザー情報をクリアして、ログアウト処理とする
$_SESSION['admin'] = '';
unset($_SESSION['admin']);

// 念のために他のセッション変数もクリアする
$_SESSION['post'] = '';
unset($_SESSION['post']);
$_SESSION['get'] = '';
unset($_SESSION['get']);
$_SESSION['msg'] = '';
unset($_SESSION['msg']);

// トップページへリダイレクト
header('Location: ./login.php');
