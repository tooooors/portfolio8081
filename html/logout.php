<?php

// セッション開始
session_start();

// セッション名取得
$session_name = session_name();
// セッション変数をすべて削除
$_SESSION = array();

// ユーザのCookieに保存されているセッションIDを削除
if (isset($_COOKIE[$session_name])) {
    // sessionに関連する設定を取得
    $params = session_get_cookie_params();
    
    // sessionに利用しているクッキーの有効期限を過去に設定することで無効化
    setcookie($session_name, '', time() - 42000,
        $params["user_name"], $params["user_id"]
    );
}
// セッションIDを無効化
session_destroy();
// ログアウトの処理が完了したらログインページへリダイレクト
header('Location: login.php');
exit;