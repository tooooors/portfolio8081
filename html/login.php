<?php
// 設定ファイルの読み込み
require_once '../conf/const.php';
// 関数ファイルの読み込み
require_once '../model/function.php';
require_once '../model/validate.php';
require_once '../model/db.php';

// 関数初期化
$user_name = ''; // ユーザー名
$password = ''; // パスワード
$err_msg = []; // エラーメッセージ

try {
    // DBハンドルを取得
    $dbh = get_db_connect();
    // 売上個数順に商品一覧を取得
    $rows = get_popular_items($dbh);
    // HTMLエンティティに変換
    $rows = entity_assoc_array($rows);
    
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
            header('Location: login.php');
            exit;
        }
        
        // ユーザー名とパスワードの確認
        $err_msg = user_match($dbh, $user_name, $password);
            
        if (count($err_msg) === 0) {
            // セッション変数にuser_nameを保存
            $_SESSION['user_name'] = $user_name;
            // ユーザIDの取得
            $user_id = get_user_id($dbh, $user_name);
            // セッション変数にuser_idを保存
            $_SESSION['user_id'] = $user_id['user_id'];
            if ($_SESSION['user_id'] === 1) {
                // 管理ページへリダイレクト
                header('Location: items.php');
                exit;
            } else {
                // 商品一覧ページへリダイレクト
                header('Location: itemlist.php');
                exit;    
            }
        } 
    }   
} catch (PDOException $e) {
    $err_msg[] = '管理者にお問い合わせください';
}



// トークンの生成
$token = get_csrf_token();

// iframeでの読み込みを禁止する
header("X-FRAME-OPTIONS: DENY");

include_once '../view/login_view.php';