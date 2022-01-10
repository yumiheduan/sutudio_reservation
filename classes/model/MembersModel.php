<?php

/**
 * MembersModelクラス
 */
class MembersModel extends BaseModel
{

    //  * コンストラクタ

    public function __construct()
    {
        // 親クラスのコンストラクタの呼び出し
        parent::__construct();
    }


    /**
     * 
     *  同一のメールアドレスのユーザーを探す
     *
     * @param string $email
     * @return array
     */
    public function findMemberByEmail(string $email): array
    {
        $sql = 'select * from members ';
        $sql .= 'where email=:email';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);

        // falseが返却されたときは、空の配列を返却
        if (empty($rec)) {
            return [];
        }
        return $rec;
    }


    /**
     * // 会員情報登録
     *
     * @param array $data
     * @return string
     */
    public function registerMember(array $data): string
    {

        $sql = '';
        $sql .= 'insert into members (kana_name, phone, email)';
        $sql .= ' values ';
        $sql .= '(:kana_name, :phone, :email)';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':kana_name', $data['kana_name'], PDO::PARAM_STR);
        $stmt->bindValue(':phone', $data['phone'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);

        $ret = $stmt->execute();

        if ($ret) {
            $id = $this->dbh->lastInsertId();
            return $id;
        } else {
            return null;
        }
    }


    /**
     *  会員情報変更
     *
     * @param array $data
     * @return boolean
     */
    public function updateMember(array $data): bool
    {

        $sql = '';
        $sql .= 'update members set ';
        $sql .= 'kana_name=:kana_name,';
        $sql .= 'phone=:phone,';
        $sql .= 'email=:email ';
        $sql .= 'where id=:id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':kana_name', $data['kana_name'], PDO::PARAM_STR);
        $stmt->bindValue(':phone', $data['phone'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);

        $stmt->execute();

        return true;
    }


    /**
     * 会員を検索条件で抽出して取得
     *
     * @param [string] $kana_name
     * @param [string] $phone
     * @param [string] $email
     * @return array
     */
    public function getMemberBySearch($search): array
    {
        $sql = '';
        $sql .= 'select ';
        $sql .= 'id,';
        $sql .= 'kana_name,';
        $sql .= 'phone,';
        $sql .= 'email ';
        $sql .= 'from members ';
        $sql .= 'where (';
        $sql .= 'kana_name=:kana_name ';
        $sql .= 'or phone=:phone ';
        $sql .= 'or email=:email';
        $sql .= ') ';
        $sql .= 'order by id asc';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':kana_name', $search, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $search, PDO::PARAM_STR);
        $stmt->bindParam(':email', $search, PDO::PARAM_STR);
        $stmt->execute();
        $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $ret;
    }


    /**
     *  指定されたIDで会員情報を取得
     *
     * @param [int] $id
     * @return array
     */
    public function getMember($id): array
    {
        $sql = '';
        $sql .= 'select ';
        $sql .= 'id,';
        $sql .= 'kana_name,';
        $sql .= 'phone,';
        $sql .= 'email ';
        $sql .= 'from members ';
        $sql .= 'where id=:id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $ret;
    }


    /**
     * 指定したIDの会員を削除する（物理的削除）
     *
     * @param [int] $id
     * @return boolean
     */
    public function deleteMember($id): bool
    {

        $sql = '';
        $sql .= 'delete from members ';
        $sql .= 'where id=:id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return true;
    }
}
