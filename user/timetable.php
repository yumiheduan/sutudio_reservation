<?php

require_once('../classes/util/SessionUtil.php');
require_once('../classes/model/BaseModel.php');
require_once('../classes/model/StudioModel.php');

// セッションスタート
SessionUtil::sessionStart();

// セッションに保存したエラーメッセージを消去
unset($_SESSION['msg']['err']);

// 在⽇時の DateTime クラスのインスタンスを作成しセッションに保存する
$dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
$date = $dt->format('Y-m-d');
$_SESSION['date'] = $date;

// GETされてきた日付またはセッションに保存した今日の日付を$dateに代入
if (isset($_GET['date'])) {
    $date = $_GET['date'];
} else {
    $date = $_SESSION['date'];
}

try {

    // Studioクラスのインスタンスを生成する。
    $db = new StudioModel();

?>

    <!DOCTYPE html>
    <html lang="ja">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>スタジオ空き情報タイムテーブル</title>

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
                                <a class="nav-link" aria-current="page" href="./information.php">スタジオ情報</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="#">スタジオ空き情報</a>
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
                    <strong>予約タイムテーブル</strong> -- <?php echo $date ?> -- 空白は予約OKです！
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
                    <!-- カレンダーへのリンク -->
                    <div class="card-header" style="text-align: right;">
                        <a href="./calendar.php" class="btn btn-success">カレンダー表示</a>
                    </div>
                    <div class="card-body text-success">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>時間</th>
                                        <th>Aスタジオ</th>
                                        <th>Bスタジオ</th>
                                    </tr>
                                </thead>

                                <!-- // 指定した日時の予約時間割を取得 -->
                                <tbody>
                                    <tr>
                                        <td>10:00~11:00</td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 10, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 10, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>11:00~12:00</td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 11, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 11, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>12:00~13:00</td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 12, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 12, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>13:00~14:00</td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 13, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 13, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>14:00~15:00</td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 14, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 14, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>15:00~16:00</td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 15, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 15, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>16:00~17:00</td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 16, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 16, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>17:00~18:00</td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 17, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 17, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>18:00~19:00</td>
                                        <td><?php
                                            $table = $db->getTimeTable($date, 18, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 18, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>19:00~20:00</td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 19, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 19, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>20:00~21:00</td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 20, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 20, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>21:00~22:00</td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 21, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 21, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>22:00~23:00</td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 22, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 22, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>23:00~24:00</td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 23, 'A');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $table = $db->getTimeTable($date, 23, 'B');
                                            if ($table != false) {
                                                echo '予約有';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- テーブルここまで -->

                        <!-- カレンダーへのリンク -->
                        <div class="card-footer" style="text-align: right;">
                            <a href="./calendar.php" class="btn btn-success">カレンダー表示</a>
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

<?php
    //   // 正常終了したときは、エラーメッセージを削除
    unset($_SESSION['msg']['err']);
} catch (Exception $e) {   
    //   // 例外が発生したときは、エラーメッセージをセッションに保存して、リダイレクト。
    $_SESSION['msg']['err'] = '申し訳ございません。エラーが発生しました。';
    header('Location: ./');
    exit;
}
