<?php

require_once('../../classes/util/SessionUtil.php');
require_once('../../classes/util/CommonUtil.php');
require_once('../../classes/model/BaseModel.php');
require_once('../../classes/model/MembersModel.php');

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

// ワンタイムトークンのチェック
if (!isset($post['token']) || !CommonUtil::isValidToken($post['token'])) {
    // エラーメッセージをセッションに保存して、リダイレクトする
    $_SESSION['msg']['err']  = '不正な処理が行われました。';
    header('Location: ../login/login.php');
    exit;
}

// POSTされてきた値をセッション変数に保存する
$_SESSION['post']['kana_name'] = $post['kana_name'];
$_SESSION['post']['phone'] = $post['phone'];
$_SESSION['post']['email'] = $post['email'];

// バリデーション
if ($_SESSION['post']['kana_name'] == '') {
    $_SESSION['msg']['err'] = '氏名かなを入力してください。';
    header('Location: ./registration.php');
    exit;
}

if (preg_match('/[^ぁ-んー]/u', $_SESSION['post']['kana_name'])) {
    $_SESSION['msg']['err'] = '氏名かなはひらがなで入力してください。';
    header('Location: ./registration.php');
    exit;
}

if ($_SESSION['post']['phone'] == '') {
    $_SESSION['msg']['err'] = '電話番号を入力してください。';
    header('Location: ./registration.php');
    exit;
}

if (!preg_match("/^[0-9-]{6,11}$/", $_SESSION['post']['phone'])) {
    $_SESSION['msg']['err'] = '電話番号はハイフンなしの半角数字で入力してください。';
    header('Location: ./registration.php');
    exit;
}

if ($_SESSION['post']['email'] == '') {
    $_SESSION['msg']['err'] = 'Emailを入力してください。';
    header('Location: ./registration.php');
    exit;
}

if (strlen($_SESSION['post']['kana_name']) > 100) {
    $_SESSION['msg']['err'] = '氏名かなは100文字以下にしてください。';
    header('Location: ./registration.php');
    exit;
}

if (strlen($_SESSION['post']['phone']) > 100) {
    $_SESSION['msg']['err'] = '電話番号は100文字以下にしてください。';
    header('Location: ./registration.php');
    exit;
}

if (strlen($_SESSION['post']['email']) > 255) {
    $_SESSION['msg']['err'] = 'Emailは255文字以下にしてください。';
    header('Location: ./registration.php');
    exit;
}

try {

    // membersテーブルクラスのインスタンスを生成する。
    $member_db = new MembersModel();

    if (!empty($member_db->findMemberByEmail($_SESSION['post']['email']))) {
        // エラーメッセージをセッションに保存して、リダイレクトする
        $_SESSION['msg']['err'] = '既に同じメールアドレスが登録されています。';
        header('Location: ./registration.php');
        exit;
    }

    // 正常終了したときは、エラーメッセージを削除する。
    unset($_SESSION['msg']['err']);
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする。
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
    <title>会員登録確認</title>

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
                            <a class="nav-link active" href="#">会員登録</a>
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
                <strong>会員登録確認</strong> -- 登録内容を確認して下さい --
            </div>
        </div>
        <!-- アラート ここまで -->

        <!-- テーブル -->
        <div class="row my-2">
            <div class="col-md-6 offset-md-3">
                <div class="card border-success">
                    <div class="card-body text-success">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>氏名かな</th>
                                        <td><?= $_SESSION['post']['kana_name'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>電話番号</th>
                                        <td><?= $_SESSION['post']['phone'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?= $_SESSION['post']['email'] ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- テーブルここまで -->

                        <!-- 送信ボタン -->
                        <div class="row my-2">
                            <div class="my-6">
                                <a class="btn btn-outline-success mb-3" href="./registration.php">前に戻る</a>
                                <a class="btn btn-success mb-3" href="./registration_process.php">会員登録</a>
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