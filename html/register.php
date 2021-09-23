<?php
// 設定ファイルの読み込み
require_once '../conf/const.php';
// 関数ファイルの読み込み
require_once '../model/function.php';
require_once '../model/validate.php';
require_once '../model/db.php';

// 関数の初期化
$user_name = ''; // ユーザー名
$password = ''; // パスワード
$err_msg = []; // エラーメッセージ

// セッション開始
session_start();

// リクエストメソッド確認
$request_method = get_request_method();
if ($request_method === 'POST') {
    
    // POSTデータの取得
    $token = get_post_data('token');
    $user_name = get_post_data('user_name');
    $password = get_post_data('password');
    
    // トークンのチェック
    if (is_valid_csrf_token($token) === false) {
        $err_msg[] = '不正なアクセスです';
        // ログインページへリダイレクト
        header('Location: register.php');
        exit;
    }
    
    // 入力値チェック
    $err_msg = validate_user($user_name, $password);
    
    if (count($err_msg) === 0) {
        try {
            $dbh = get_db_connect();
            // 既に同じユーザー名が登録されていないかチェック
            $err_msg = user_name_match($dbh, $user_name);
            if (count($err_msg) === 0) {
                // ユーザー登録
                user_register($dbh, $user_name, $password);
                header ('Location: completion.php');
                exit;
            }
        } catch (PDOException $e) {
            $err_msg[] = '管理者にお問い合わせください';
        }
    }
}



// トークンの生成
$token = get_csrf_token();

// iframeでの読み込みを禁止する
header("X-FRAME-OPTIONS: DENY");

include_once '../view/register_view.php';
