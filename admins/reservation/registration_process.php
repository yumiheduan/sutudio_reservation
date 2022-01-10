<?php

require_once('../../classes/util/SessionUtil.php');
require_once('../../classes/util/CommonUtil.php');
require_once('../../classes/model/BaseModel.php');
require_once('../../classes/model/StudioModel.php');

// セッションスタート
SessionUtil::sessionStart();

// データベースに登録する内容を連想配列にする。
$reservation = array(
    'member_id' => $_SESSION['get']['member_id'],
    'reservation_date' => $_SESSION['post']['reservation_date'],
    'fee_type' => $_SESSION['post']['fee_type'],
    'discount_type' => $_SESSION['post']['discount_type'],
    'usage_fee' => $_SESSION['post']['usage_fee']
);

$timeTable = array(
    'member_id' => $_SESSION['get']['member_id'],
    'reservation_date' => $_SESSION['post']['reservation_date'],
    'start_time' => $_SESSION['post']['start_time'],
    'room' => $_SESSION['post']['room']
);

// 使用時間分をループするため$numに代入
$num = $_SESSION['post']['utilization_time'];

try {
    // Studioクラスのインスタンスを生成する。
    $studio_db = new StudioModel();

    $studio_db->begin();

    // reservationテーブルへレコードのインサート
    $ret = $studio_db->registerReservation($reservation);

    // time_tableテーブルへレコードのインサート
    for ($i = 1; $i <= $num; $i++) {
        $rec = $studio_db->registerTimeTable($timeTable, $ret);
        $timeTable['start_time']++;
    }

    $studio_db->commit();
    // 正常終了したときは、エラーメッセージを削除して、success.phpにリダイレクト
    unset($_SESSION['msg']['err']);
    header('Location: ./registration_success.php');
    exit;
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクト。
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ./registration.php');
    exit;
}
