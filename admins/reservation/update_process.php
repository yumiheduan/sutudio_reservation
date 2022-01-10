<?php

require_once('../../classes/util/SessionUtil.php');
require_once('../../classes/util/CommonUtil.php');
require_once('../../classes/model/BaseModel.php');
require_once('../../classes/model/StudioModel.php');

// セッションスタート
SessionUtil::sessionStart();

// サニタイズ
$post = CommonUtil::sanitize($_POST);

// POSTされてきた値をセッション変数に保存する
$_SESSION['post']['fee_type'] = $post['fee_type'];
$_SESSION['post']['discount_type'] = $post['discount_type'];
$_SESSION['post']['time'] = $post['time'];

// バリデーション
if ($post['fee_type'] == '') {
    $_SESSION['msg']['err'] = '料金情報をお選びください。';
    header('Location: ./update_confirm.php');
    exit;
}

if ($post['discount_type'] == '') {
    $_SESSION['msg']['err'] = '割引情報をお選びください。';
    header('Location: ./update_confirm.php');
    exit;
}

try {
    // Studioテーブルクラスのインスタンスを生成する。
    $studio_db = new StudioModel();

    // 料金情報を取得する。
    $fees = $studio_db->getFees($_SESSION['post']['fee_type']);

    // 割引情報を取得する。
    $discounts = $studio_db->getDiscounts($_SESSION['post']['discount_type']);

    // 利用料金の計算をする。
    foreach ($fees as $fee) {
        foreach ($discounts as $discount) {
            $_SESSION['post']['usage_fee'] = $_SESSION['post']['time'] * $fee['fees'] * $discount['discount_rate'];
        }
    }

    // データベースに登録する内容を連想配列にする。
    $data = array(
        'fee_type' => $_SESSION['post']['fee_type'],
        'discount_type' => $_SESSION['post']['discount_type'],
        'usage_fee' => $_SESSION['post']['usage_fee'],
        'id' => $_SESSION['post']['reservation_id']
    );

    // レコードのアップデート
    $ret = $studio_db->updateFeesAndDiscounts($data);

    // 正常終了したときは、エラーメッセージを削除して、リダイレクトする。
    unset($_SESSION['msg']['err']);
    header('Location: ./update_success.php');
    exit;
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする。
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ./update_confirm.php');
    exit;
}
