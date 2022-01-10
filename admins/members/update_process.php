<?php

require_once('../../classes/util/SessionUtil.php');
require_once('../../classes/util/CommonUtil.php');
require_once('../../classes/model/BaseModel.php');
require_once('../../classes/model/MembersModel.php');

// セッションスタート
SessionUtil::sessionStart();

// データベースに登録する内容を連想配列にする。
$data = array(
    'kana_name' => $_SESSION['post']['kana_name'],
    'phone' => $_SESSION['post']['phone'],
    'email' => $_SESSION['post']['email'],
    'id' => $_SESSION['get']['member_id']
);

try {
    // membersテーブルクラスのインスタンスを生成する。
    $member_db = new MembersModel();

    // レコードのインサート
    $member_db->updateMember($data);

    // セッションに保存したPOSTデータを削除
    unset($_SESSION['post']);

    // 正常終了したときは、エラーメッセージを削除して、リダイレクトする。
    unset($_SESSION['msg']['err']);
    header('Location: ./update_success.php');
    exit;
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする。
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ./update.php');
    exit;
}
