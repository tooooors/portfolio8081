<?php
// 設定ファイルの読み込み
require_once '../conf/const.php';
// 関数ファイルの読み込み
require_once '../model/function.php';
require_once '../model/db.php';

// 関数初期化
$user_name = ''; // ユーザー名
$user_id = ''; // ユーザーID

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
    // データベースに接続
    $dbh = get_db_connect();
     // カート情報を取得
    $carts = get_carts($dbh, $user_id);
    // HTMLエンティティに変換
    $carts = entity_assoc_array($carts);
    // 合計価格の取得
    $sum_price = get_sum_price($dbh, $user_id);
    
    // 購入処理
    $dbh -> beginTransaction();
    try {
        
        foreach ($carts as $read) {
            // 購入履歴追加
            insert_history($dbh, (int)$read['item_id'], (int)$read['amount']);
            // 在庫数更新
            buy_update_stock($dbh, (int)$read['amount'], (int)$read['item_id']);
        }
        // カート内の商品を削除
        delete_cart($dbh, $user_id);
        // コミット処理
        $dbh -> commit();
    } catch (PDOException $e) {
        // ロールバック処理
        $dbh -> rollback();
        // 例外をスロー
        throw $e;
    }
    
} catch (PDOException $e) {
    $err_msg[] = '管理者にお問い合わせください';
}


// iframeでの読み込みを禁止する
header("X-FRAME-OPTIONS: DENY");

// 購入完了ページテンプレートファイルの読み込み
include_once '../view/finish_view.php';