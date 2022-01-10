<?php

require_once('../../classes/util/SessionUtil.php');
require_once('../../classes/util/CommonUtil.php');
require_once('../../classes/model/BaseModel.php');
require_once('../../classes/model/MembersModel.php');

// セッションスタート
SessionUtil::sessionStart();

try {
    // membersテーブルクラスのインスタンスを生成する。
    $member_db = new MembersModel();

    // 指定したIDの会員情報を削除する（物理的削除）
    $member_db->deleteMember($_SESSION['get']['member_id']);

    // 正常終了したときは、エラーメッセージを削除して、リダイレクトする。
    unset($_SESSION['msg']['err']);
    header('Location: ./unset.php');
    exit;
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする。
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ./unsubscribe.php');
    exit;
}
