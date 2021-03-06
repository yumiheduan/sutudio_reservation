<?php

/**
 * 共通関数クラス
 */
class CommonUtil
{
    /**
     * POSTされたデータをサニタイズする
     *
     * @param array $before サニタイズ前のPOST配列
     * @return array サニタイズ後のPOST配列
     */
    public static function sanitize($before)
    {
        $after = array();
        foreach ($before as $k => $v) {
            $after[$k] = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
        }
        return $after;
    }


    /**
     * 指定の長さのランダムな文字列を作成する
     *
     * @param integer $length 作成する文字列の長さ
     * @return string
     */
    public static function makeRandomString(int $length = 32): string
    {
        return bin2hex(openssl_random_pseudo_bytes($length));
    }


    /**
     * ワンタイムトークンを発生させる
     *
     * @param string $tokenName セッションに保存するトークンのキーの名前
     * @return string
     */
    public static function generateToken(string $tokenName = 'token'): string
    {
        // ワンタイムトークンを生成してセッションに保存する
        $token = self::makeRandomString();
        $_SESSION[$tokenName] = $token;
        return $token;
    }



    /**
     * 送信されてきたトークンが正しいかどうか調べる
     *
     * @param string $token 送信されてきたトークン nullが与えられる可能性があるので、nullableにしている
     * @param string $tokenName セッションに保存されているトークンのキーの名前
     * @return boolean
     *
     */
    public static function isValidToken(?string $token, string $tokenName = 'token'): bool
    {
        if (!isset($_SESSION[$tokenName]) || $_SESSION[$tokenName] !== $token) {
            return false;
        }
        return true;
    }
}
