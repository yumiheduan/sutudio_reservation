<?php

/**
 * BaseModelクラス
 */
class BaseModel
{

    /** データベース接続ユーザー名 */
    protected const DB_USER = 'root';

    /** データベース接続パスワード */
    protected const DB_PASS = '';

    /** データベースホスト名 */
    protected const DB_HOST = 'localhost';

    /** データベース名 */
    protected const DB_NAME = 'studio';

    /** PDOインスタンス */
    protected $dbh;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $dsn = 'mysql:dbname=' . self::DB_NAME . ';host=' . self::DB_HOST . ';charset=utf8';
        $this->dbh = new PDO($dsn, self::DB_USER, self::DB_PASS);
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * トランザクションを開始
     */
    public function begin()
    {
        $this->dbh->beginTransaction();
    }

    /**
     * トランザクションをコミット
     */
    public function commit()
    {
        $this->dbh->commit();
    }

    /**
     * トランザクションをロールバック
     */
    public function rollback()
    {
        $this->dbh->rollback();
    }
}
