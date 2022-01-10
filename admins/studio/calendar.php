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

// カレンダー作成
// 今月を0とする。
$month = 0;

// GETパラメータがあって、かつ、数値形式で、かつ、整数のとき。

if (isset($_GET['month']) && is_numeric($_GET['month'])) {
    $month = (int) $_GET['month'];
}

// 今日の日付のDateTimeクラスのインスタンスを生成する。
$dateTime = new DateTime();

// タイムゾーンを「アジア/東京」にする。
$dateTime->setTimezone(new DateTimeZone('Asia/Tokyo'));

// 今日の日付から(今日の日付 - 1)を引き、DateTimeクラスのインスタンスを今月の1日の日付に設定する。
// 21日なら1を引いた20日前に遡ると1日になる
$d = $dateTime->format('d');
$dateTime->sub(new DateInterval('P' . ($d - 1) . 'D'));

if ($month > 0) {
    // $monthが0より大きいときは、現在月の「ついたち」に、その月数を追加。
    $dateTime->add(new DateInterval("P" . $month . "M"));
} else {
    // $monthが0より小さいときは、現在月の「ついたち」から、その月数を引く。
    $dateTime->sub(new DateInterval("P" . (0 - $month) . "M"));
}

// カレンダーの表示及びリンク用に今月の1日のクローンを作成する。
$dateTime2 = clone $dateTime;

// 当月の「ついたち」が何曜日か求める。当月の「ついたち」までに何日あるか、という日数と等しくなる。
$beginDayOfWeek = $dateTime->format('w');

// 当月に何日あるかの日数を求める。
$monthDays = $dateTime->format('t');

// 当月に何週あるかを求める。小数点以下を切り上げることで、同月の週数が求められる。
$weeks = ceil(($monthDays + $beginDayOfWeek) / 7);

// カレンダーに記述する日付のカウンタ。
$date = 1;

// 一日のDateIntervalクラスのインスタンスを作成する。
$interval = new DateInterval('P1D');

?>

<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約状況</title>

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
                            <a class="nav-link active" aria-current="page" href="./timetable.php">予約状況</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../members/registration.php">会員登録</a>
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

        <div class="row my-3"></div>
        <div class="col-md-6 offset-md-3">
            <div class="alert alert-success" role="alert">
                <strong>予約カレンダー -- <?= $dateTime->format('Y年n月') ?> -- </strong> 日付を選んで下さい
            </div>

            <!-- エラーメッセージ -->
            <?php if (isset($_SESSION['msg']['err'])) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['msg']['err'] ?>
                </div>
            <?php endif ?>
            <!-- エラーメッセージ ここまで -->

            <!-- テーブル -->
            <div class="card border-success">
                <div class="card-body text-success">
                    <div class="table-responsive">
                        <table class="table table-success table-striped table-bordered">
                            <tr>
                                <th>日</th>
                                <th>月</th>
                                <th>火</th>
                                <th>水</th>
                                <th>木</th>
                                <th>金</th>
                                <th>土</th>
                            </tr>
                            <!-- 当月にある週数分繰り返し -->
                            <?php
                            for ($week = 0; $week < $weeks; $week++) : ?>
                                <tr>
                                    <!-- 一週間の日数分（7日分）繰り返し -->
                                    <?php for ($day = 0; $day < 7; $day++) : ?>
                                        <td>
                                            <?php if ($week == 0 && $day >= $beginDayOfWeek) {
                                                // 月の1週目で、かつ、月初の日（曜日）以上のときは、
                                                // 日付のカウンタを表示して、1を足す                      
                                                echo '<a href="./timetable.php?date=' . $dateTime2->format('Y-m-d') . '">' . $date . '</a>';
                                                $date++;
                                                $dateTime2->add($interval);
                                            } elseif ($week > 0 && $date <= $monthDays) {
                                                // 月の2週目以降で、かつ、月末の日までのときは、
                                                // 日付のカウンタを表示して、1を足す
                                                echo '<a href="./timetable.php?date=' . $dateTime2->format('Y-m-d') . '">' . $date . '</a>';
                                                $date++;
                                                $dateTime2->add($interval);
                                            }
                                            // その他の日は何も表示しない
                                            ?>
                                        </td>
                                    <?php endfor ?>
                                </tr>
                            <?php endfor ?>
                        </table>
                        <!-- テーブルここまで -->

                        <div class="card-footer" style="text-align: center;">
                            <a href="./calendar.php?month=<?= $month - 1 ?>" class="btn btn-outline-success">&lt;&lt;前の月</a>
                            <a href="./calendar.php" class="btn btn-success">今月</a>
                            <a href="./calendar.php?month=<?= $month + 1 ?>" class="btn btn-outline-success">次の月&gt;&gt;</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </div>
    <!-- コンテナ ここまで -->

    <!-- javascript -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

</body>

</html>