<?php
// 設定ファイルの読み込み
require_once '../conf/const.php';
// 関数ファイルの読み込み
require_once '../model/function.php';
require_once '../model/validate.php';
require_once '../model/db.php';

// 関数初期化
$item_id = ''; // 商品ID
$process_kind = ''; // ボタン判別
$err_msg = []; // エラーメッセージ
$review = []; // 口コミ情報
$amount = '';  // 数量

// セッション開始
session_start();
// セッション変数からuser_name取得
if (isset($_SESSION['user_name'])) {
    $user_name = $_SESSION['user_name'];
    $user_id = $_SESSION['user_id'];
} else {
    // 非ログインの場合、ログインページへリダイレクト
    header('Location: login.php');
    exit;
}


try {
    // DBハンドルの取得
    $dbh = get_db_connect();    

    // GETデータの取得
    $item_id = get_get_data('item_id');
    // 商品IDが公開されているか確認
    $err_msg = item_id_check($dbh, $item_id);
    
    if (count($err_msg) !== 0) {
        // 商品IDが非公開のものの場合、商品一覧ページへリダイレクト
        header('Location: itemlist.php');
        exit;
    } else {
        // POSTデータの取得
        $process_kind = get_post_data('process_kind');
        //item_idをint型へ変換
        $item_id = (int)$item_id;
        // 商品情報の取得
        $item = get_item_detail($dbh, $item_id);
        // 口コミ登録
        if ($process_kind === 'review') {
            // POSTデータの取得
            $review = get_post_data('review');
            $token = get_post_data('token');
            // トークンのチェック
            if(is_valid_csrf_token($token) === false) {
                $err_msg[] = '不正なアクセスです';
                header('Location: login.php');
                exit;
            }
            
            // データチェック
            $err_msg = validate_review($review);
            if (count($err_msg) === 0) {
                try {
                    insert_review($dbh, $item_id, $user_id, $review);
                } catch (PDOException $e) {
                    throw $e;
                }   
            }
        }
        // 口コミ情報の取得
        $item_review = get_review($dbh, $item_id);
    } 
} catch (PDOException $e) {
        $err_msg[] = '管理者にお問い合わせください';
}

// トークンの生成
$token = get_csrf_token();

// iframeでの読み込みを禁止する
header("X-FRAME-OPTIONS: DENY");


include_once '../view/detail_view.php';