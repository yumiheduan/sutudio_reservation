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

try {
    // membersテーブルクラスのインスタンスを生成する
    $member_db = new MembersModel();

    // studioテーブルクラスのインスタンスを生成する
    $studio_db = new StudioModel();

    // メンバーの情報を取得
    $members = $member_db->getMember($_SESSION['get']['member_id']);

    // スタジオ料金の情報を取得
    $studio_fees = $studio_db->displaystudioFees();

    // 割引情報を取得
    $discounts = $studio_db->displayDiscounts();
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ../members/member.php');
    exit;
}

$token = CommonUtil::generateToken();

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スタジオ予約</title>

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
                <strong>スタジオ予約</strong> -- 予約内容を選択してください --
            </div>
            <!-- アラート ここまで -->

            <!-- エラーメッセージ -->
            <?php if (isset($_SESSION['msg']['err'])) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['msg']['err'] ?>
                </div>
            <?php endif ?>
            <!-- エラーメッセージ ここまで -->

            <!-- 予約フォーム -->
            <div class="row my-2">
                <form action="./registration_confirm.php" method="POST">
                    <input type="hidden" name="token" value="<?= $token ?>">
                    <div class="col-md">
                        <div class="card border-success">
                            <div class="card-header text-white bg-success">
                                <?php foreach ($members as $member) {
                                    echo $member['kana_name'] . ' さん';
                                } ?>
                            </div>

                            <div class="card-body text-success">
                                <div class="mb-3">
                                    <label for="reservation_date" class="form-label">-- 予約日 --</label>
                                    <input type="date" class="form-control" name="reservation_date" id="reservation_date" value="<?= isset($_SESSION['post']['reservation_date']) ? $_SESSION['post']['reservation_date'] : ''; ?>">
                                </div>

                                <div class="my-3">
                                    <label for="room" class="form-label">-- スタジオの種類 --</label>
                                    <select class="form-select" id="room" name="room">
                                        <option></option>
                                        <option value="A" <?= isset($_SESSION['post']['room']) && $_SESSION['post']['room'] == 'A' ? ' selected' : ''; ?>>スタジオA</option>
                                        <option value="B" <?= isset($_SESSION['post']['room']) && $_SESSION['post']['room'] == 'B' ? ' selected' : ''; ?>>スタジオB</option>
                                    </select>
                                </div>

                                <div class="my-3">
                                    <label for="start_time" class="form-label">-- 開始時間 --</label>
                                    <select class="form-select" id="start_time" name="start_time">
                                        <option></option>
                                        <option value="10" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 10 ? ' selected' : ''; ?>>10:00から</option>
                                        <option value="11" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 11 ? ' selected' : ''; ?>>11:00から</option>
                                        <option value="12" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 12 ? ' selected' : ''; ?>>12:00から</option>
                                        <option value="13" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 13 ? ' selected' : ''; ?>>13:00から</option>
                                        <option value="14" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 14 ? ' selected' : ''; ?>>14:00から</option>
                                        <option value="15" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 15 ? ' selected' : ''; ?>>15:00から</option>
                                        <option value="16" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 16 ? ' selected' : ''; ?>>16:00から</option>
                                        <option value="17" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 17 ? ' selected' : ''; ?>>17:00から</option>
                                        <option value="18" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 18 ? ' selected' : ''; ?>>18:00から</option>
                                        <option value="19" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 19 ? ' selected' : ''; ?>>19:00から</option>
                                        <option value="20" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 20 ? ' selected' : ''; ?>>20:00から</option>
                                        <option value="21" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 21 ? ' selected' : ''; ?>>21:00から</option>
                                        <option value="22" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 22 ? ' selected' : ''; ?>>22:00から</option>
                                        <option value="23" <?= isset($_SESSION['post']['start_time']) && $_SESSION['post']['start_time'] == 23 ? ' selected' : ''; ?>>23:00から</option>
                                    </select>
                                </div>

                                <div class="my-3">
                                    <label for="utilization_time" class="form-label">-- 利用時間 --</label>
                                    <select class="form-select" id="utilization_time" name="utilization_time">
                                        <option></option>
                                        <option value="1" <?= isset($_SESSION['post']['utilization_time']) && $_SESSION['post']['utilization_time'] == 1 ? ' selected' : ''; ?>>1時間</option>
                                        <option value="2" <?= isset($_SESSION['post']['utilization_time']) && $_SESSION['post']['utilization_time'] == 2 ? ' selected' : ''; ?>>2時間</option>
                                        <option value="3" <?= isset($_SESSION['post']['utilization_time']) && $_SESSION['post']['utilization_time'] == 3 ? ' selected' : ''; ?>>3時間</option>
                                    </select>
                                </div>

                                <div class="my-3">
                                    <label for="feetype" class="form-label">-- 料金情報 --</label>
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
                            </div>
                            <!-- 予約フォーム ここまで -->

                            <!-- 送信ボタン -->
                            <div class="my-3 mx-3">
                                <a class="btn btn-outline-success mb-3" href="../members/member.php?member_id=<?= $_SESSION['get']['member_id'] ?>">会員情報</a>
                                <button type="submit" class="btn btn-success mb-3" name="btn_confirm">確認する</button>
                            </div>
                        </div>
                </form>
                <!-- 送信ボタン ここまで -->

            </div>
        </div>
    </div>
    <!-- コンテナ ここまで -->

    <!-- javascript -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

</body>

</html>