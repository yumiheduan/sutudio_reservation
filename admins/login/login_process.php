<?php
require_once('../../classes/util/SessionUtil.php');
require_once('../../classes/util/CommonUtil.php');
require_once('../../classes/model/BaseModel.php');
require_once('../../classes/model/AdminsModel.php');

// セッションスタート
SessionUtil::sessionStart();

// サニタイズ
$post = CommonUtil::sanitize($_POST);

// 現在⽇時の DateTime クラスのインスタンスを作成 (ログイン後のタイムテーブル表示の為)
$dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
$date = $dt->format('Y-m-d');

// 日付をセッション変数に保存
$_SESSION['date'] = $date;

// ワンタイムトークンのチェック
if (!isset($post['token']) || !CommonUtil::isValidToken($post['token'])) {
    // エラーメッセージをセッションに保存して、リダイレクトする
    $_SESSION['msg']['err']  = '不正な処理が行われました。';
    header('Location: ./login.php');
    exit;
}

try {
    // ユーザーの検索、ユーザー情報の取得
    $admin_db = new AdminsModel();
    $admin = $admin_db->getAdmin($post['email'], $post['password']);

    if (empty($admin)) {
        // adminの情報が取得できなかったとき
        // エラーメッセージをセッション変数に保存→ログインページに表示させる。
        $_SESSION['msg']['err'] = "Emailまたはパスワードが違います。";

        // POSTされてきたEmailをセッション変数に保存→ログインページのEmailのテキストボックスに表示
        $_SESSION['post']['email'] = $post['email'];

        // ログインページへリダイレクト
        header('Location: ./login.php');
    } else {
        // adminの情報が取得できたとき
        // adminの情報をセッション変数に保存
        $_SESSION['admin'] = $admin;

        // セッション変数に保存されているエラーメッセージをクリア
        $_SESSION['msg']['err'] = "";
        unset($_SESSION['msg']['err']);

        // セッション変数に保存されているPOSTされてきたデータをクリア
        $_SESSION['post'] = "";
        unset($_SESSION['post']);

        // 本日の予約を表示
        header('Location: ../studio/timetable.php');
    }
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする。
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ./login.php');
    exit;
}
