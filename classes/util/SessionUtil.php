<?php

/**
 * セッション関連ユーティリティクラス
 */
class SessionUtil
{
    /**
     * セッションスタート
     *
     * @return void
     */
    public static function sessionStart()
    {
        session_start();
        session_regenerate_id(true);
    }
}
