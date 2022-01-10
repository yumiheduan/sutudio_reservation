<?php

require_once('../../classes/util/SessionUtil.php');
require_once('../../classes/util/CommonUtil.php');
require_once('../../classes/model/BaseModel.php');
require_once('../../classes/model/MembersModel.php');
require_once('../../classes/model/StudioModel.php');


// セッションスタート
SessionUtil::sessionStart();

// ブラウザの戻るボタンを押したときのエラー防止
if (!isset($_SESSION['get'])) {
    header('Location: ../studio/timetable.php');
    exit;
}

if (empty($_SESSION['admin'])) {
    // 未ログインのとき
    header('Location: ../login/login.php');
} else {
    // ログイン済みのとき
    $admin = $_SESSION['admin'];
}

// メンバー情報取得の為の変数を用意する
$member_id = $_SESSION['get']['member_id'];

try {

    // membersテーブルクラスのインスタンスを生成する
    $member_db = new MembersModel();

    // Studioテーブルクラスのインスタンスを生成する。
    $studio_db = new studioModel();

    // メンバーの情報を取得
    $members = $member_db->getMember($member_id);

    // 指定したメンバーのキャンセル情報を取得
    $cancels = $studio_db->getCancels($member_id);

    // 指定したメンバーの予約情報を全て取得
    $reservations = $studio_db->getMemberReservations($member_id, $_SESSION['date']);

    // 正常終了したときは、エラーメッセージを削除する
    unset($_SESSION['msg']['err']);
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする。
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ../members/member.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員予約確認</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <!-- コンテナ -->
    <div class="container-md">
        <!-- ナビゲーション -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-success">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">スタジオ予約管理</a>
                <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#Navbar" aria-controls="Navbar" aria-expanded="false" aria-label="ナビゲーションの切替">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="Navbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="../studio/timetable.php">予約状況</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="../members/member.php?member_id=<?= $_SESSION['get']['member_id'] ?>">会員情報</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../login/logout.php">ログアウト</a>
                        </li>
                    </ul>
                    <form class="d-flex" action="../members/result.php" method="POST">
                        <input type="search" class="form-control me-sm-2" name="search" id="search" placeholder="検索..." aria-label="検索...">
                        <button type="submit" class="btn btn-outline-light flex-shrink-0">検索</button>
                    </form>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        <!-- ナビゲーション ここまで -->

        <!-- アラート -->
        <div class="row my-3"></div>
        <div class="col-md-6 offset-md-3">
            <div class="alert alert-success" role="alert">
                <strong>予約確認</strong> -- 処理を選択してください --
            </div>
            <!-- アラート ここまで -->

            <!-- エラーメッセージ -->
            <?php if (isset($_SESSION['msg']['err'])) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['msg']['err'] ?>
                </div>
            <?php endif ?>
            <!-- エラーメッセージ ここまで -->

            <!-- 確認用テーブル -->
            <div class="row my-2">
                <div class="col-md">
                    <div class="card border-success">
                        <div class="card-header text-white bg-success">
                            <?php foreach ($members as $member) {
                                echo $member['kana_name'] . ' さん';
                            } ?>
                        </div>

                        <!-- 予約がない場合のメッセージ表示 -->
                        <div class="card-body text-success">
                            <?php if (count($reservations) == 0) : ?>
                                <div class="table-responsive">
                                    <table class="table table-success">
                                        <tbody>
                                            <th><?php echo '予約はありません'; ?></th>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- メッセージ ここまで -->

                                <!-- 送信ボタン -->
                                <div class="row my-2">
                                    <div class="my-2">
                                        <a class="btn btn-outline-success mb-3" href="../members/member.php?member_id=<?= $member_id ?>">会員情報</a>
                                        <a class="btn btn-success mb-3" href="../reservation/registration.php">予約入力</a>
                                        <a class="btn btn-secondary mb-3" href="../members/unset.php">処理終了</a>
                                    </div>
                                </div>
                                <!-- 送信ボタン ここまで -->
                            <?php endif ?>

                            <!-- キャンセル料金用 テーブル -->
                            <?php foreach ($cancels as $cancel) : ?>
                                <div class="table-responsive">
                                    <table class="table table-danger table-striped">
                                        <tbody>
                                            <tr>
                                                <th>未領収のキャンセル料があります</th>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>予約日</th>
                                                <td><?= $cancel['reservation_date'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>キャンセル料</th>
                                                <td><?= $cancel['cancel_fee'] ?>円</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- キャンセル料金用 テーブルここまで -->

                                <!-- キャンセル料金用 送信ボタン -->
                                <div class="row my-2">
                                    <div class="my-4">
                                        <form action="./cancel_received_confirm.php" method="post">
                                            <a class="btn btn-outline-secondary mb-3" href="../members/member.php?member_id=<?= $member_id ?>">会員情報</a>
                                            <input type="hidden" name="reservation_id" id="reservation_id" value="<?= $cancel['reservation_id'] ?>">
                                            <button type="submit" class="btn btn-danger mb-3" name="btn_confirm">領収確認</button>
                                            <a class="btn btn-secondary mb-3" href="../members/unset.php">処理終了</a>
                                    </div>
                                </div>
                                </form>
                                <!-- キャンセル料金用 送信ボタン ここまで -->
                            <?php endforeach ?>

                            <?php
                            foreach ($reservations as $reservation) :

                                // 予約時間と部屋の情報を取得する
                                $time_tables = $studio_db->getReservationsTimeRoomById($reservation['id']);

                                // 料金情報を取得する
                                $fees = $studio_db->getFees($reservation['fee_type']);

                                // 割引情報を取得する
                                $discounts = $studio_db->getDiscounts($reservation['discount_type']);
                            ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <th>予約日</th>
                                            <td><?= $reservation['reservation_date'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>スタジオの種類</th>
                                                <?php foreach ($time_tables as $time_table) : ?>
                                                    <td><?= $time_table['room'] ?>スタジオ</td>
                                            </tr>
                                            <tr>
                                                <th>利用時間</th>
                                                <td><?= $time_table['start_time'] ?>時から<?= $time_table['end_time'] ?>時まで
                                                    <?php echo $time = $time_table['end_time'] - $time_table['start_time'] ?>時間</td>
                                            </tr>
                                            <tr>
                                                <th>料金情報</th>
                                                <?php foreach ($fees as $fee) : ?>
                                                    <td><?= $fee['fee_name'] . ' 1時間 ' . $fee['fees'] . '円' ?></td>
                                            </tr>
                                            <tr>
                                                <th>割引情報</th>
                                                <?php foreach ($discounts as $discount) : ?>
                                                    <td><?= $discount['discount_name'] . ' ' . $discount['discount_rate_name'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>利用料金</th>
                                                <td><?= $reservation['usage_fee'] ?>円</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- テーブルここまで -->

                                <!-- 送信ボタン -->
                                <div class="row my-2">
                                    <div class="my-4">
                                        <form action="./update_confirm.php" method="post">
                                            <a class="btn btn-outline-secondary mb-3" href="../members/member.php?member_id=<?= $member_id ?>">会員情報</a>
                                            <input type="hidden" name="reservation_id" id="reservation_id" value="<?= $reservation['id'] ?>">
                                            <input type="hidden" name="fee_type" id="fee_type" value="<?= $reservation['fee_type'] ?>">
                                            <input type="hidden" name="discount_type" id="discount_type" value="<?= $reservation['discount_type'] ?>">
                                            <button type="submit" class="btn btn-success mb-3" name="btn_confirm">予約変更</button>
                                            <a class="btn btn-secondary mb-3" href="../members/unset.php">処理終了</a>
                                        </form>
                                    </div>
                                </div>
                                <!-- 送信ボタン ここまで -->
                            <?php endforeach ?>
                        <?php endforeach ?>
                    <?php endforeach ?>
                <?php endforeach ?>

                        </div>
                    </div>
                </div>
            </div>
            <!-- コンテナ ここまで -->

            <!-- javascript -->
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

</body>

</html>