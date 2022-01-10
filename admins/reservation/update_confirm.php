<?php

require_once('../../classes/util/SessionUtil.php');
require_once('../../classes/util/CommonUtil.php');
require_once('../../classes/model/BaseModel.php');
require_once('../../classes/model/MembersModel.php');
require_once('../../classes/model/StudioModel.php');

// セッションスタート
SessionUtil::sessionStart();

if (empty($_SESSION['admin'])) {
    // 未ログインのとき
    header('Location: ../login/login.php');
} else {
    // ログイン済みのとき
    $admin = $_SESSION['admin'];
}

// サニタイズ
$post = CommonUtil::sanitize($_POST);

// POSTされてきた値をセッション変数に保存する
$_SESSION['post']['reservation_id'] = $post['reservation_id'];
$_SESSION['post']['fee_type'] = $post['fee_type'];
$_SESSION['post']['discount_type'] = $post['discount_type'];

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

    // スタジオ料金の情報を取得
    $studio_fees = $studio_db->displaystudioFees();

    // 割引情報を取得
    $discounts = $studio_db->displayDiscounts();
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする。
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ./confirmation.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約内容変更</title>

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
                            <a class="nav-link" aria-current="page" href="../studio/time_table.php">予約状況</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="../members/member.php?member_id=<?= $_SESSION['get']['member_id'] ?>">会員情報</a>
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
                <strong>料金・割引変更</strong> -- 変更内容を選択してください --
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
                                        <?php endforeach ?>
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
                                    <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- 確認テーブルここまで -->

                            <!-- 変更フォーム -->
                            <div class="my-3">
                                <form action="./update_process.php" method="post">
                                    <input type="hidden" name="time" id="time" value="<?= $time ?>">
                                    <label for="fee_type" class="form-label">-- 料金情報 --</label>
                                    <select class="form-select" id="fee_type" name="fee_type">
                                        <option></option>
                                        <?php foreach ($studio_fees as $studio_fee) : ?>
                                            <option value="<?= $studio_fee['fee_type'] ?>" <?= isset($_SESSION['post']['fee_type']) && $_SESSION['post']['fee_type'] == $studio_fee['fee_type'] ? ' selected' : ''; ?>><?= $studio_fee['fee_name'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                            </div>

                            <div class="my-3">
                                <label for="discount_type" class="form-label">-- 割引情報 --</label>
                                <select class="form-select" id="discount_type" name="discount_type">
                                    <option></option>
                                    <?php foreach ($discounts as $discount) : ?>
                                        <option value="<?= $discount['discount_type'] ?>" <?= isset($_SESSION['post']['discount_type']) && $_SESSION['post']['discount_type'] == $discount['discount_type'] ? ' selected' : ''; ?>><?= $discount['discount_name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>


                            <!-- 変更フォーム ここまで -->

                            <!-- 送信ボタン -->
                            <div class="row my-2">
                                <div class="my-4">
                                    <a class="btn btn-outline-secondary mb-3" href="./confirmation.php">前に戻る</a>
                                    <button type="submit" class="btn btn-success mb-3" name="btn_confirm">変更確定</button>
                                    <a class="btn btn-warning mb-3" href="./deletion_confirm.php">予約削除</a>
                                </div>
                            </div>
                            <!-- 送信ボタン ここまで -->
                            </form>

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