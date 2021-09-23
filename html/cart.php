<?php
// 設定ファイルの読み込み
require_once '../conf/const.php';
// 関数ファイルの読み込み
require_once '../model/function.php';
require_once '../model/validate.php';
require_once '../model/db.php';

// 関数初期化
$user_name = ''; // ユーザー名
$user_id = ''; // ユーザーID
$err_msg = []; // エラーメッセージ
$amount = ''; // 購入予定商品数
$item_id = ''; // 商品ID
$msg = []; // 成功時のメッセージ
$process_kind = '';
$sum_price = ''; // 合計価格

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

// リクエストメソッド確認
$request_method = get_request_method();
if ($request_method === 'POST') {
    // トークンの取得
    $token = get_post_data('token');
    // トークンのチェック
    if (is_valid_csrf_token($token) === false) {
        $err_msg[] = '不正なアクセスです';
        header('Location: login.php');
        exit;
    }
}

try {
    // データベースに接続
    $dbh = get_db_connect();

    // POSTデータ取得
    $process_kind = get_post_data('process_kind');
    $amount = get_post_data('amount');
    $item_id = get_post_data('item_id');
    $item_id = (int)$item_id;
    
    
    // カートに追加
    if ($process_kind === 'insert_cart') {
        try {   
                // 商品IDが公開中のものであるかチェック
                $err_msg = item_id_check($dbh, $item_id);
                // 購入予定数のチェック
                $tmp_err_msg = validate_amount($amount);
                $err_msg = array_merge($err_msg, $tmp_err_msg);
            if (count($err_msg) === 0) {
                // 同じユーザが同じ商品をカートに入れていないか確認
                $cart_match = cart_match($dbh, $user_id, $item_id);
                // 商品をカートに追加
                insert_cart($cart_match, $dbh, $user_id, $item_id, $amount);
                $msg[] = 'カートに商品を追加しました';    
            }
        } catch(PDOException $e) {
            throw $e;
        }
    }
    
    // 購入予定商品数の更新
    if ($process_kind === 'update_amount') {
        // 商品IDが公開中のものであるかチェック
        $err_msg = item_id_check($dbh, $item_id);
        // 入力値チェック
        $tmp_err_msg = validate_amount($amount);
        $err_msg = array_merge($err_msg, $tmp_err_msg);
        if (count($err_msg) === 0) {
            try {
                update_amount($dbh, $amount, $item_id);
                $msg[] = '購入予定数を変更しました';
            } catch (PDOException $e) {
                throw $e;
            }
        }
    }
    
    // カート内商品を削除する
    if ($process_kind === 'delete_cart_item') {
        // 商品IDが公開中のものであるかチェック
        $err_msg = item_id_check($dbh, $item_id);
        if (count($err_msg) === 0) {
            try {
                delete_cart_item($dbh, $item_id, $user_id);
                $msg[] = '商品を削除しました';
            } catch (PDOException $e) {
                throw $e;
            }    
        }
    }
    
    // カート情報を取得
    $carts = get_carts($dbh, $user_id);
    // HTMLエンティティに変換
    $carts = entity_assoc_array($carts);
    // 合計価格の取得
    $sum_price = get_sum_price($dbh, $user_id);
    
     // 購入時、在庫と公開ステータスの確認
    if ($process_kind === 'buy') {
        $err_msg = buy_match($dbh, $user_id);
        // 在庫あり、公開の場合、購入完了ページへリダイレクト
        if (count($err_msg) === 0) {
            header ('Location: finish.php');
            exit;
        }
    }
    
} catch (PDOException $e) {
    $err_msg[] = '管理者にお問い合わせください';
}

// トークンの生成
$token = get_csrf_token();

// iframeでの読み込みを禁止する
header("X-FRAME-OPTIONS: DENY");

// カートテンプレートファイル読み込み
include_once '../view/cart_view.php';