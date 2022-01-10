<?php

require_once('../../classes/util/SessionUtil.php');
require_once('../../classes/util/CommonUtil.php');
require_once('../../classes/model/BaseModel.php');
require_once('../../classes/model/StudioModel.php');

// セッションスタート
SessionUtil::sessionStart();

// データベースに登録する内容を連想配列にする。
$data = array(
    'reservation_id' => $_SESSION['post']['reservation_id'],
    'cancel_fee' => $_SESSION['post']['cancel_fee'],
);

try {
    // Studioテーブルクラスのインスタンスを生成する。
    $studio_db = new StudioModel();

    $studio_db->begin();

    // reservationsテーブルの削除（論理的削除）
    $studio_db->deleteReservation($_SESSION['post']['reservation_id']);

    // time_tablesテーブルの削除（物理的削除）
    $studio_db->deleteTimeTable($_SESSION['post']['reservation_id']);

    // キャンセル料が発生する場合はcancelsテーブルへ登録
    if ($_SESSION['post']['cancel_fee'] > 0) {
        $studio_db->registerCancels($data);
    }

    $studio_db->commit();

    // 正常終了したときは、エラーメッセージを削除して、リダイレクトする。
    unset($_SESSION['msg']['err']);
    header('Location: ./deletion_success.php');
    exit;
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする。
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ./deletion_confirm.php');
    exit;
}
