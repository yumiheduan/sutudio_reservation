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

unset($_SESSION['msg']['err']);

// サニタイズ
$post = CommonUtil::sanitize($_POST);

// ワンタイムトークンのチェック
if (!isset($post['token']) || !CommonUtil::isValidToken($post['token'])) {
    // エラーメッセージをセッションに保存して、リダイレクトする
    $_SESSION['msg']['err']  = '不正な処理が行われました。';
    header('Location: ../login.php');
    exit;
}

// POSTされてきた値をセッション変数に保存する
$_SESSION['post']['reservation_date'] = $post['reservation_date'];
$_SESSION['post']['start_time'] = $post['start_time'];
$_SESSION['post']['utilization_time'] = $post['utilization_time'];
$_SESSION['post']['room'] = $post['room'];
$_SESSION['post']['fee_type'] = $post['fee_type'];
$_SESSION['post']['discount_type'] = $post['discount_type'];


// バリデーション
if ($_SESSION['post']['reservation_date'] == '') {
    $_SESSION['msg']['err'] = '予約日をお選びください。';
    header('Location: ./registration.php');
    exit;
}
if ($_SESSION['post']['start_time'] == '') {
    $_SESSION['msg']['err'] = '開始時間をお選びください。';
    header('Location: ./registration.php');
    exit;
}

if ($_SESSION['post']['utilization_time'] == '') {
    $_SESSION['msg']['err'] = '利用時間をお選びください。';
    header('Location: ./registration.php');
    exit;
}

if ($_SESSION['post']['start_time'] == 23 && $_SESSION['post']['utilization_time'] > 1) {
    $_SESSION['msg']['err'] = '営業時間は24時までです。';
    header('Location: ./registration.php');
    exit;
}

if ($_SESSION['post']['start_time'] == 22 && $_SESSION['post']['utilization_time'] > 2) {
    $_SESSION['msg']['err'] = '営業時間は24時までです。';
    header('Location: ./registration.php');
    exit;
}

if ($_SESSION['post']['room'] == '') {
    $_SESSION['msg']['err'] = 'スタジオの種類をお選びください。';
    header('Location: ./registration.php');
    exit;
}

if ($_SESSION['post']['fee_type'] == '') {
    $_SESSION['msg']['err'] = '料金情報をお選びください。';
    header('Location: ./registration.php');
    exit;
}

if ($_SESSION['post']['discount_type'] == '') {
    $_SESSION['msg']['err'] = '割引情報をお選びください。';
    header('Location: ./registration.php');
    exit;
}

try {
    // membersテーブルクラスのインスタンスを生成する
    $member_db = new MembersModel();
    // Studioテーブルクラスのインスタンスを生成する。
    $studio_db = new studioModel();

    // メンバーの情報を取得
    $members = $member_db->getMember($_SESSION['get']['member_id']);

    // 料金情報を取得する。
    $fees = $studio_db->getFees($_SESSION['post']['fee_type']);

    // 割引情報を取得する。
    $discounts = $studio_db->getDiscounts($_SESSION['post']['discount_type']);

    // 使用時間分をループするため$numに代入
    $num = $_SESSION['post']['utilization_time'];

    // 指定した日付の予約時間割を取得（予約重複の防止）
    for ($i = 1; $i <= $num; $i++) {
        $table = $studio_db->getTimeTable($_SESSION['post']['reservation_date'], $_SESSION['post']['start_time'], $_SESSION['post']['room']);
        $post['start_time']++;
        if ($table != false) {
            $_SESSION['msg']['err'] = 'その時間はすでに予約が入っています。';
            header('Location: ./registration.php');
            exit;
        }
    }
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ./registration.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約内容確認</title>

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
                <strong>予約内容確認</strong> -- 予約の内容を確認して下さい --
            </div>
            <!-- アラート ここまで -->

            <!-- 確認用テーブル -->
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
                                        <th>予約日</th>
                                        <td><?php echo $_SESSION['post']['reservation_date'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>スタジオの種類</th>
                                            <td><?php echo $_SESSION['post']['room'] ?>スタジオ</td>
                                        </tr>
                                        <tr>
                                            <th>開始時間</th>
                                            <td><?php echo $_SESSION['post']['start_time'] ?>時から</td>
                                        </tr>
                                        <tr>
                                            <th>利用時間</th>
                                            <td><?php echo $_SESSION['post']['utilization_time'] ?>時間</td>
                                        </tr>
                                        <tr>
                                            <th>料金情報</th>
                                            <?php foreach ($fees as $fee) : ?>
                                                <td><?= $fee['fee_name'] . ' 1時間 ' . $fee['fees'] . '円' ?></td>
                                            <?php endforeach ?>
                                        </tr>
                                        <tr>
                                            <th>割引情報</th>
                                            <?php foreach ($discounts as $discount) : ?>
                                                <td><?= $discount['discount_name'] . ' ' . $discount['discount_rate_name'] ?></td>
                                            <?php endforeach ?>
                                        </tr>
                                        <tr>
                                            <th>利用料金</th>
                                            <td><?php echo $_SESSION['post']['usage_fee'] = $_SESSION['post']['utilization_time'] * $fee['fees'] * $discount['discount_rate'] ?>円</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- テーブルここまで -->

                            <!-- 送信ボタン -->
                            <div class="row my-2">
                                <div class="my-2">
                                    <a class="btn btn-outline-secondary mb-3" href="#" onclick="history.back(); return false;">前へ戻る</a>
                                    <a class="btn btn-success mb-3" href="./registration_process.php">予約登録</a>
                                </div>
                            </div>
                            <!-- 送信ボタン ここまで -->

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