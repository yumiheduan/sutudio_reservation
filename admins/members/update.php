<?php

require_once('../../classes/util/SessionUtil.php');
require_once('../../classes/util/CommonUtil.php');
require_once('../../classes/model/BaseModel.php');
require_once('../../classes/model/MembersModel.php');

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

    // メンバーの情報を取得
    $members = $member_db->getMember($_SESSION['get']['member_id']);
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする。
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ./member.php');
    exit;
}

// ワンタイムトークンの生成
$token = CommonUtil::generateToken();

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員情報変更</title>

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
                <strong>会員情報変更</strong> -- 変更する内容を入力して下さい --
            </div>
            <!-- アラート ここまで -->

            <!-- エラーメッセージ -->
            <?php if (!empty($_SESSION['msg']['err'])) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['msg']['err'] ?>
                </div>
            <?php endif ?>
            <!-- エラーメッセージ ここまで -->

            <!-- 入力フォーム -->
            <div class="card border-success">
                <div class="card-body text-success">
                    <?php foreach ($members as $member) : ?>
                        <form action="./update_confirm.php" method="post">
                            <input type="hidden" name="token" value="<?= $token ?>">
                            <div class="my-4 row">
                                <label for="kana_name" class="col-sm-2 col-form-label">氏名かな</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="kana_name" id="kana_name" value="<?= $member['kana_name'] ?>">
                                </div>
                            </div>
                            <div class="my-4 row">
                                <label for="phone" class="col-sm-2 col-form-label">電話番号</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="phone" id="phone" value="<?= $member['phone'] ?>">
                                </div>
                            </div>
                            <div class="my-4 row">
                                <label for="email" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="email" id="email" value="<?= $member['email'] ?>">
                                </div>
                            </div>

                            <div class="row my-2">
                                <div class="my-4">
                                    <a class="btn btn-outline-success mb-3" href="../members/member.php?member_id=<?= $_SESSION['get']['member_id'] ?>">会員情報</a>
                                    <button type="submit" class="btn btn-success mb-3" name="btn_confirm">変更確認</button>
                                    <a class="btn btn-warning mb-3" href="./unsubscribe.php">退会手続</a>
                                </div>
                            </div>
                        </form>
                    <?php endforeach ?>
                </div>
            </div>
            <!-- 入力フォーム ここまで -->

        </div>
        <!-- コンテナ ここまで -->

        <!-- javascript -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

</body>

</html>