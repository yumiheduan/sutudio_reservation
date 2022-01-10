<?php

require_once('../../classes/util/SessionUtil.php');
require_once('../../classes/model/BaseModel.php');
require_once('../../classes/model/MembersModel.php');
require_once('../../classes/model/StudioModel.php');

// セッションスタート
SessionUtil::sessionStart();

// ブラウザの戻るボタンを押したときのエラー防止
if (!isset($_SESSION['get']) || !isset($_SESSION['post'])) {
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

try {
    // membersテーブルクラスのインスタンスを生成する
    $member_db = new MembersModel();

    // Studioテーブルクラスのインスタンスを生成する。
    $studio_db = new StudioModel();

    // メンバーの情報を取得
    $members = $member_db->getMember($_SESSION['get']['member_id']);

    // 指定した予約IDの情報を取得
    $reservations = $studio_db->getReservationById($_SESSION['post']['reservation_id']);
    $time_tables = $studio_db->getReservationsTimeRoomById($_SESSION['post']['reservation_id']);

    // 料金情報を取得する。
    $fees = $studio_db->getFees($_SESSION['post']['fee_type']);

    // 割引情報を取得する。
    $discounts = $studio_db->getDiscounts($_SESSION['post']['discount_type']);

    // 正常終了したときは、エラーメッセージを削除する。
    unset($_SESSION['msg']['err']);
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする。
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ./update_confirm.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約変更完了</title>

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
                <strong>料金、割引内容変更完了</strong> -- 処理を選択してください --
            </div>
            <!-- アラート ここまで -->

            <!-- エラーメッセージ -->
            <?php if (isset($_SESSION['msg']['err'])) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['msg']['err'] ?>
                </div>
            <?php endif ?>
            <!-- エラーメッセージ ここまで -->

            <!-- 確認テーブル -->
            <div class="row my-2">
                <div class="col-md">
                    <div class="card border-success">
                        <div class="card-header text-white bg-success">
                            <?php foreach ($members as $member) {
                                echo $member['kana_name'] . ' さん';
                            } ?>
                        </div>
                        <div class="card-body text-success">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        <?php foreach ($reservations as $reservation) : ?>
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
                                                <td><?php foreach ($fees as $fee) : ?>
                                                        <?= $fee['fee_name'] . ' 1時間 ' . $fee['fees'] . '円' ?>
                                                    <?php endforeach ?></td>
                                            </tr>
                                            <tr>
                                                <th>割引情報</th>
                                                <td><?php foreach ($discounts as $discount) : ?>
                                                        <?= $discount['discount_name'] . ' ' . $discount['discount_rate_name'] ?>
                                                    <?php endforeach ?></td>
                                            </tr>
                                            <tr>
                                                <th>利用料金</th>
                                                <td><?= $_SESSION['post']['usage_fee'] ?></td>
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach ?>
                    <?php endforeach ?>
                    <!-- 確認テーブルここまで -->

                    <!-- 送信ボタン -->
                    <div class="row my-2">
                        <div class="my-4">
                            <a class="btn btn-outline-success mb-3" href="../members/member.php?member_id=<?= $_SESSION['get']['member_id'] ?>">会員情報</a>
                            <a class="btn btn-success mb-3" href="./confirmation.php">予約確認</a>
                            <a class="btn btn-outline-secondary mb-3" href="../members/update.php">情報変更</a>
                            <a class="btn btn-secondary mb-3" href="../members/unset.php">処理終了</a>
                        </div>
                    </div>
                    <!-- 送信ボタン ここまで -->

                        </div>
                    </div>
                    <!-- コンテナ ここまで -->
                    <!-- javascript -->
                    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

</body>

</html>