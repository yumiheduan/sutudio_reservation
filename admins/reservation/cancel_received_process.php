<?php

require_once('../../classes/util/SessionUtil.php');
require_once('../../classes/util/CommonUtil.php');
require_once('../../classes/model/BaseModel.php');
require_once('../../classes/model/StudioModel.php');

// セッションスタート
SessionUtil::sessionStart();

try {
    // Studioテーブルクラスのインスタンスを生成する。
    $studio_db = new StudioModel();

    // キャンセル料を領収済みにアップデートする
    $studio_db->deleteCancelFee($_SESSION['post']['reservation_id']);

    // 正常終了したときは、エラーメッセージを削除して、リダイレクトする。
    unset($_SESSION['msg']['err']);
    header('Location: ./confirmation.php');
    exit;
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする。
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ./confirmation.php');
    exit;
}
