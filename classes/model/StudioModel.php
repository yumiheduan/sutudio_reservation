<?php

/**
 * StudioModelクラス
 */
class StudioModel extends BaseModel
{

    //  * コンストラクタ

    public function __construct()
    {
        // 親クラスのコンストラクタの呼び出し
        parent::__construct();
    }


    /**
     * 指定した料金情報の取得
     *
     * @param string $feeType
     * @return array
     */
    public function getFees($feeType): array
    {

        $sql = '';
        $sql .= 'select ';
        $sql .= 'fees,';
        $sql .= 'fee_name ';
        $sql .= 'from studio_fees ';
        $sql .= 'where fee_type=:fee_type';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':fee_type', $feeType, PDO::PARAM_STR);
        $stmt->execute();

        $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rec;
    }


    /**
     *  指定した割引情報の取得
     *
     * @param [string] $discountType
     * @return array
     */
    public function getDiscounts($discountType): array
    {

        $sql = '';
        $sql .= 'select ';
        $sql .= 'discount_rate,';
        $sql .= 'discount_name,';
        $sql .= 'discount_rate_name ';
        $sql .= 'from discounts ';
        $sql .= 'where discount_type=:discount_type';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':discount_type', $discountType, PDO::PARAM_STR);
        $stmt->execute();

        $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rec;
    }


    /**
     * 予約情報登録
     *
     * @param array $reservation
     * @return string
     */
    public function registerReservation(array $reservation): string
    {

        $sql = '';
        $sql .= 'insert into reservations (';
        $sql .= 'member_id,';
        $sql .= 'reservation_date,';
        $sql .= 'fee_type,';
        $sql .= 'discount_type,';
        $sql .= 'usage_fee ';
        $sql .= ') values (';
        $sql .= ':member_id,';
        $sql .= ':reservation_date,';
        $sql .= ':fee_type,';
        $sql .= ':discount_type,';
        $sql .= ':usage_fee';
        $sql .= ')';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':member_id', $reservation['member_id'], PDO::PARAM_INT);
        $stmt->bindParam(':reservation_date', $reservation['reservation_date'], PDO::PARAM_STR);
        $stmt->bindParam(':fee_type', $reservation['fee_type'], PDO::PARAM_STR);
        $stmt->bindParam(':discount_type', $reservation['discount_type'], PDO::PARAM_STR);
        $stmt->bindParam(':usage_fee', $reservation['usage_fee'], PDO::PARAM_INT);
        $ret = $stmt->execute();

        // 別テーブルに登録する予約IDと統一する為、最後に登録されたIDを返す
        if ($ret) {
            $id = $this->dbh->lastInsertId();
            return $id;
        } else {
            return null;
        }
    }


    /**
     * 予約時間割登録
     *
     * @param array $timeTable
     * @param string $ret
     * @return boolean
     */
    public function registerTimeTable(array $timeTable, string $ret): bool
    {

        $sql = '';
        $sql .= 'insert into time_tables (';
        $sql .= 'reservation_id,';
        $sql .= 'member_id,';
        $sql .= 'reservation_date,';
        $sql .= 'start_time,';
        $sql .= 'room ';
        $sql .= ') values (';
        $sql .= ':reservation_id,';
        $sql .= ':member_id,';
        $sql .= ':reservation_date,';
        $sql .= ':start_time,';
        $sql .= ':room';
        $sql .= ')';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':reservation_id', $ret, PDO::PARAM_INT);
        $stmt->bindParam(':member_id', $timeTable['member_id'], PDO::PARAM_INT);
        $stmt->bindParam(':reservation_date', $timeTable['reservation_date'], PDO::PARAM_STR);
        $stmt->bindParam(':start_time', $timeTable['start_time'], PDO::PARAM_STR);
        $stmt->bindParam(':room', $timeTable['room'], PDO::PARAM_STR);
        $rec = $stmt->execute();

        return $rec;
    }


    /**
     * 指定した日付の予約時間割を取得(タイムテーブル表示)
     *
     * @param [string] $date
     * @param [int] $time
     * @param [string] $room
     * @return mixed 成功したらarray、失敗したらfalse
     */
    public function getTimeTable($date, $time, $room)
    {

        $sql = '';
        $sql .= 'select ';
        $sql .= 't.member_id,';
        $sql .= 'm.id,';
        $sql .= 'm.kana_name,';
        $sql .= 't.reservation_date,';
        $sql .= 't.start_time,';
        $sql .= 't.room ';
        $sql .= 'from time_tables t ';
        $sql .= 'inner join members m on t.member_id=m.id ';
        $sql .= 'where t.reservation_date=:reservation_date ';
        $sql .= 'and t.start_time=:start_time ';
        $sql .= 'and t.room=:room';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':reservation_date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':start_time', $time, PDO::PARAM_INT);
        $stmt->bindParam(':room', $room, PDO::PARAM_STR);
        $stmt->execute();
        $ret = $stmt->fetch(PDO::FETCH_ASSOC);

        return $ret;
    }


    /**
     * 指定したメンバーの予約情報を全て取得
     *
     * @param [int] $member_id
     * @param [string] $date
     * @return array
     */
    public function getMemberReservations($member_id, $date): array
    {

        $sql = '';
        $sql .= 'select ';
        $sql .= 'id,';
        $sql .= 'member_id,';
        $sql .= 'reservation_date,';
        $sql .= 'fee_type,';
        $sql .= 'discount_type,';
        $sql .= 'usage_fee ';
        $sql .= 'from reservations ';
        $sql .= 'where member_id=:member_id ';
        $sql .= 'and reservation_date>=:reservation_date ';
        $sql .= 'and is_cancel=0 ';
        $sql .= 'order by reservation_date';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->bindParam(':reservation_date', $date, PDO::PARAM_STR);
        $stmt->execute();
        $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $ret;
    }


    /**
     * 指定した予約IDの情報を取得
     *
     * @param [int] $id
     * @return array
     */
    public function getReservationById($id): array
    {

        $sql = '';
        $sql .= 'select ';
        $sql .= 'id,';
        $sql .= 'member_id,';
        $sql .= 'reservation_date,';
        $sql .= 'fee_type,';
        $sql .= 'discount_type,';
        $sql .= 'usage_fee ';
        $sql .= 'from reservations ';
        $sql .= 'where id=:id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $ret;
    }


    /**
     *  指定した予約IDで予約時間と部屋の情報を取得
     *
     * @param [int] $id
     * @return array
     */
    public function getReservationsTimeRoomById($id): array
    {

        $sql = '';
        $sql .= 'select ';
        $sql .= 'reservation_id,';
        $sql .= 'member_id,';
        $sql .= 'reservation_date,';
        $sql .= 'room,';
        $sql .= 'min(start_time) as start_time,';
        $sql .= 'max(start_time) +1 as end_time ';
        $sql .= 'from time_tables ';
        $sql .= 'where reservation_id=:reservation_id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':reservation_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $ret;
    }


    /**
     * 指定した予約IDの料金、割引内容変更
     *
     * @param array $data
     * @return boolean
     */
    public function updateFeesAndDiscounts(array $data): bool
    {

        $sql = '';
        $sql .= 'update reservations set ';
        $sql .= 'fee_type=:fee_type,';
        $sql .= 'discount_type=:discount_type,';
        $sql .= 'usage_fee=:usage_fee ';
        $sql .= 'where id=:id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':fee_type', $data['fee_type'], PDO::PARAM_STR);
        $stmt->bindValue(':discount_type', $data['discount_type'], PDO::PARAM_STR);
        $stmt->bindValue(':usage_fee', $data['usage_fee'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);

        $stmt->execute();

        return true;
    }


    /**
     * 指定した日数のキャンセル料金掛率を取得
     *
     * @param [int] $day
     * @return float
     */
    public function getCancelMarkupRate($day): float
    {

        $sql = '';
        $sql .= 'select ';
        $sql .= 'cancel_markup_rate ';
        $sql .= 'from cancel_fees ';
        $sql .= 'where what_days_ago=:what_days_ago';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':what_days_ago', $day, PDO::PARAM_INT);
        $stmt->execute();

        $ret = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ret === false) {
            return 0;
        }
        return $ret['cancel_markup_rate'];
    }


    /**
     * 指定した予約IDのキャンセル情報を登録
     *
     * @param array $cancel
     * @return integer
     */
    public function registerCancels(array $cancel): int
    {

        $sql = '';
        $sql .= 'insert into cancels (';
        $sql .= 'reservation_id,';
        $sql .= 'cancel_fee ';
        $sql .= ') values (';
        $sql .= ':reservation_id,';
        $sql .= ':cancel_fee';
        $sql .= ')';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':reservation_id', $cancel['reservation_id'], PDO::PARAM_INT);
        $stmt->bindParam(':cancel_fee', $cancel['cancel_fee'], PDO::PARAM_INT);
        $ret = $stmt->execute();

        return $ret;
    }


    /**
     * 指定した予約IDの予約を削除（論理的削除）
     *
     * @param [int] $id
     * @return boolean
     */
    public function deleteReservation($id): bool
    {

        $sql = '';
        $sql .= 'update reservations set ';
        $sql .= 'is_cancel=1 ';
        $sql .= 'where id=:id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return true;
    }



    /**
     * 指定した予約IDの予約時間割を削除（物理的削除）
     *
     * @param [int] $id
     * @return boolean
     */
    public function deleteTimeTable($id): bool
    {

        $sql = '';
        $sql .= 'delete from time_tables ';
        $sql .= 'where reservation_id=:reservation_id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':reservation_id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return true;
    }


    /**
     * 指定したメンバーIDでキャンセル情報を取得
     *
     * @param [int] $id
     * @return array
     */
    public function getCancels($id): array
    {

        $sql = '';
        $sql .= 'select ';
        $sql .= 'c.reservation_id,';
        $sql .= 'r.reservation_date,';
        $sql .= 'c.cancel_fee ';
        $sql .= 'from cancels c ';
        $sql .= 'inner join reservations r on c.reservation_id=r.id ';
        $sql .= 'where r.member_id=:member_id ';
        $sql .= 'and is_received=0 ';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':member_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $ret;
    }


    /**
     * 指定した予約IDでキャンセル情報を取得
     *
     * @param [int] $id
     * @return array
     */
    public function getCancelsByReservationID($id): array
    {

        $sql = '';
        $sql .= 'select ';
        $sql .= 'c.reservation_id,';
        $sql .= 'r.reservation_date,';
        $sql .= 'c.cancel_fee ';
        $sql .= 'from cancels c ';
        $sql .= 'inner join reservations r on c.reservation_id=r.id ';
        $sql .= 'where c.reservation_id=:reservation_id ';
        $sql .= 'and is_received=0 ';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':reservation_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $ret;
    }


    /**
     * 指定した予約IDのキャンセル料金を領収済にする（論理的削除）
     *
     * @param [int] $id
     * @return boolean
     */
    public function deleteCancelFee($id): bool
    {

        $sql = '';
        $sql .= 'update cancels set ';
        $sql .= 'is_received=1 ';
        $sql .= 'where reservation_id=:reservation_id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':reservation_id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return true;
    }


    /**
     * スタジオ料金情報を全て取得する
     *
     * @return array
     */
    public function displayStudioFees(): array
    {

        $sql = '';
        $sql .= 'select * from studio_fees';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rec;
    }


    /**
     * 割引情報を全て取得する
     *
     * @return array
     */
    public function displayDiscounts(): array
    {

        $sql = '';
        $sql .= 'select * from discounts';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rec;
    }


    /**
     * キャンセル料情報全てを表示する
     *
     * @return array
     */
    public function displayCancelFees(): array
    {

        $sql = '';
        $sql .= 'select * from cancel_fees';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rec;
    }


    //トランザクションの開始
    public function begin()
    {
        $this->dbh->beginTransaction();
    }

    //更新を確定
    public function commit()
    {
        $this->dbh->commit();
    }
}
