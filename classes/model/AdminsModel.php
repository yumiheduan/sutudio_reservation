<?php

/**
 * AdminsModelクラス
 */
class AdminsModel extends BaseModel
{
    //  * コンストラクタ

    public function __construct()
    {
        // 親クラスのコンストラクタの呼び出し
        parent::__construct();
    }


    /**
     *  同一のメールアドレスの管理者を探す
     *
     * @param string $email
     * @return array
     */
    public function findAdminByEmail(string $email): array
    {
        $sql = 'select * from admins where email=:email';
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
     * メールアドレスとパスワードが一致する管理者を取得する(ログイン機能)
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function getAdmin(string $email, string $password): array
    {
        $rec = $this->findAdminByEmail($email);
        // 空の配列が返却されたとき
        if (empty($rec)) {
            return [];
        }

        // パスワードの照合
        if (password_verify($password, $rec['password'])) {
            // 照合できたら、ユーザーの連想配列を返却
            return $rec;
        }
        // 照合できなかったときは、空の配列を返却
        return [];
    }
}
