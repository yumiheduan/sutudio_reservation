<?php

require_once('../classes/model/BaseModel.php');
require_once('../classes/model/StudioModel.php');

try {

    // Studioテーブルクラスのインスタンスを生成する。
    $studio_db = new StudioModel();

    // スタジオ料金情報を表示する
    $studio_fees = $studio_db->displayStudioFees();

    // 割引情報を表示する
    $discounts = $studio_db->displayDiscounts();

    // キャンセル料情報を表示する
    $cancel_fees = $studio_db->displayCancelFees();
} catch (Exception $e) {
    // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクトする。
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ./information.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スタジオ情報</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <!-- コンテナ -->
    <div class="container-md">
        <!-- ナビゲーション -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-success">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">スタジオ予約</a>
                <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#Navbar" aria-controls="Navbar" aria-expanded="false" aria-label="ナビゲーションの切替">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="Navbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">スタジオ情報</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./calendar.php">スタジオ空き情報</a>
                        </li>
                    </ul>
                    <span class="navbar-text">
                        <h5>ご予約・お問い合わせはお気軽にお電話で！！ TEL(06)6666-6666 </h5>
                    </span>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        <!-- ナビゲーション ここまで -->

        <div class="row my-3"></div>
        <div class="col-md-6 offset-md-3">
            <div class="alert alert-success" role="alert">
                <strong>スタジオ情報</strong> -- 設備・機材・料金・割引 --
            </div>

            <!-- エラーメッセージ -->
            <?php if (isset($_SESSION['msg']['err'])) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['msg']['err'] ?>
                </div>
            <?php endif ?>
            <!-- エラーメッセージ ここまで -->

            <div class="card border-success">
                <div class="card-body text-success">

                    <!-- Aスタジオ情報 -->
                    <div class="row my-3"></div>
                    <div class="col">
                        <div class="card">
                            <div class="row"></div>
                            <div class="alert alert-success" role="alert">
                                <strong>Aスタジオ 11帖</strong>
                            </div>
                            <img src="https://picsum.photos/300/200" class="card-img-top" alt="Aスタジオ">
                            <div class="card-body">
                                <p class="card-text">
                                    どの位置でもボーカルが聞こえやすいように転がしモニターを設置
                                    <li>GUITAR AMP1 ----- MARSHALL JCM2000</li>
                                    <li>GUITAR AMP2 ----- MARSHALL CODA 100</li>
                                    <li>GUITAR AMP3 ----- ROLAND JC-120</li>
                                    <li>BASS AMP -------- GUYA GA300+S1520B</li>
                                    <li>DRUM ------------ PEARL/1BD/2TOM/1FT/Cy×3/</li>
                                    <li>KEYBOARD -------- KORG X-50</li>
                                    <li>MIXER ----------- YAMAHA EMX 312SC</li>
                                    <li>DECK ------------ PLAY CASSETTE ・MD ・CD</li>
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Aスタジオ情報 ここまで -->

                    <!-- Bスタジオ情報 -->
                    <div class="row my-3"></div>
                    <div class="col">
                        <div class="card">
                            <div class="row"></div>
                            <div class="alert alert-success" role="alert">
                                <strong>Bスタジオ 8帖</strong>
                            </div>
                            <img src="https://picsum.photos/300/200" class="card-img-top" alt="Bスタジオ">
                            <div class="card-body">
                                <p class="card-text">
                                    少人数向きのリーズナブルなスタジオです。パワー派にも十分対応！ TWINペダルも貸し出しOK!
                                    <li>GUITAR AMP1 ----- MARSHALL JCM2000</li>
                                    <li>GUITAR AMP2 ----- ROLAND JC-120</li>
                                    <li>GUITAR AMP3 ----- VOX AD120VT</li>
                                    <li>BASS AMP -------- GUYA GA300+S1520B</li>
                                    <li>DRUM ------------ PEARL/1BD/2TOM/1FT/Cy×3/</li>
                                    <li>KEYBOARD -------- KORG SP-280(88鍵）</li>
                                    <li>MIXER ----------- YAMAHA EMX 212S</li>
                                    <li>DECK ------------ PLAY MD ・CD</li>
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Bスタジオ情報 ここまで -->

                    <!-- スタジオ料金の表示テーブル -->
                    <div class="row my-3"></div>
                    <div class="alert alert-success" role="alert">
                        <strong>スタジオ料金</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>スタジオ・曜日・時間</th>
                                    <th>1時間・1バンド</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    foreach ($studio_fees as $studio_fee) : ?>
                                        <td><?= $studio_fee['fee_name'] ?></td>
                                        <td><?= $studio_fee['fees'] ?>円</td>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- スタジオ料金の表示テーブル ここまで -->

                    <!-- 割引情報の表示テーブル -->
                    <div class="row my-3"></div>
                    <div class="alert alert-success" role="alert">
                        <strong>各種割引</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>割引種類</th>
                                    <th>割引率</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    foreach ($discounts as $discount) : ?>
                                        <td><?= $discount['discount_name'] ?></td>
                                        <td><?= $discount['discount_rate_name'] ?></td>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- 割引情報の表示テーブル ここまで -->

                    <!-- キャンセル料金の表示テーブル -->
                    <div class="row my-3"></div>
                    <div class="alert alert-success" role="alert">
                        <strong>キャンセル料金</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>予約日までの日数</th>
                                    <th>キャンセル料金率</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    foreach ($cancel_fees as $cancel_fee) : ?>
                                        <td><?= $cancel_fee['cancel_name'] ?></td>
                                        <td><?= $cancel_fee['cancel_rate_name'] ?></td>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- キャンセル料金の表示テーブル ここまで -->

                </div>
            </div>
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