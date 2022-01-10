# 音楽スタジオ予約管理システム

## 目的
音楽スタジオ経営をしている友人が、予約管理をExcelで作成した表をWEBに上げて利用しているが、確認をするタイミングによっては最新の情報を得ることができない。また、その表がレシポンシブデザインではないため、端末によって表示されない場合がある。どんな端末からでも最新のスタジオ空き状況が確認でき、且つ、予約管理が可能になるようにシステムを作成する。

## 機能
- ログイン、ログアウト
- 会員登録、会員検索、会員情報変更、会員削除、予約状況確認（会員ごと）
- 予約登録、予約検索、予約変更、予約削除、予約状況確認（日ごと）
- 利用料金計算（割引等含む）、キャンセル料金計算及び未領収管理

## 工夫した点
timetable.phpでの時間ごとの予約状況をを各セル毎にメソッドを呼び出し表示する事でシンプル且つ分かりやすくした。

## 使用したツール
PHP 8.0.11

MySQL 10.4.21-MariaDB
