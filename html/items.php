<?php
// 設定ファイル読み込み
require_once '../conf/const.php';
// 関数ファイル読み込み
require_once '../model/db.php';
require_once '../model/validate.php';
require_once '../model/function.php';
require_once '../model/img.php';

// 関数の初期化
$item_name = ''; // 商品名
$price = ''; // 商品価格
$stock = ''; // 在庫数
$cups = ''; // 内容量
$type = ''; // 淹れ方
$status = ''; // ステータス
$comment = ''; // 商品詳細情報
$process_kind = ''; // どのボタンを押したか判別
$err_msg = []; // エラーメッセージ
$new_img_filename = ''; // 新しい画像ファイル名
$msg = []; //成功時のメッセージ

// セッション開始
session_start();

if ($_SESSION['user_id'] !== 1) {
    // 管理者以外の場合、ログインページへリダイレクト
    header('Location: login.php');
    exit;
}

// リクエストメソッド確認
$request_method = get_request_method();
if ($request_method === 'POST') {
    // トークンの取得
    $token = get_post_data('token');
    if (is_valid_csrf_token($token) === false) {
        $err_msg[] = '不正なアクセスです';
        header('Location: login.php');
        exit;
    }
}

// POSTデータの取得
$process_kind = get_post_data('process_kind');

try {
    // DBハンドルを取得
    $dbh = get_db_connect();
    // 新規商品追加時
    if ($process_kind === 'insert') {
        // POSTデータの取得
        $item_name = get_post_data('item_name');
        $price = get_post_data('price');
        $stock = get_post_data('stock');
        $cups = get_post_data('cups');
        $type = get_post_data('type');
        $status = get_post_data('status');
        $comment = get_post_data('comment');
        
        // 入力値チェック
        $err_msg = validate_add_item($item_name, $price, $stock, $cups, $type, $status, $comment);
        
        // 新しい画像ファイル名を取得
        list($new_img_filename, $tmp_err_msg) = get_img('new_img');
        $err_msg = array_merge($err_msg,$tmp_err_msg);
        
        // 新規商品追加
        if (count($err_msg) === 0) {
            try {
                insert_item($dbh, $item_name, $price, $new_img_filename, $status, $type, $cups, $comment, $stock);
                $msg[] = '新規商品追加成功';    
            } catch (PDOException $e) {
                $err_msg[] = '新規商品追加失敗';
            }   
        } 
    }
    
    // 在庫数更新
    if ($process_kind === 'update_stock') {
        // POSTデータの取得
        $update_stock = get_post_data('update_stock');
        $item_id = get_post_data('item_id');
        
        // 入力値チェック
        $err_msg = validate_stock($update_stock, $item_id);
        
        // 在庫数更新
        if (count($err_msg) === 0) {
            try {
                update_stock($dbh, $update_stock, $item_id);
                $msg[] = '在庫数更新成功';
            } catch (PDOException $e) {
                $err_msg[] = '在庫数更新失敗';
            }
        }
    }
    
    // 商品詳細情報更新
    if ($process_kind === 'update_comment') {
        // POSTデータの取得
        $comment = get_post_data('comment');
        $item_id = get_post_data('item_id');
        
        // 入力値チェック
        $err_msg = validate_comment($comment, $item_id);
        
        // 商品詳細情報更新
        if (count($err_msg) === 0) {
            try {
                update_comment($dbh, $comment, $item_id);
                $msg[] = '商品詳細情報更新成功';
            } catch (PDOException $e) {
                $err_msg[] = '商品詳細情報更新失敗';
            }
        }
    }
    
    // 公開ステータス更新
    if ($process_kind === 'update_status') {
        // POSTデータの取得
        $status = get_post_data('status');
        $item_id = get_post_data('item_id');
        
        // 入力値チェック
        $err_msg = validate_status($status, $item_id);
        
        // 公開ステータス更新
        if (count($err_msg) === 0) {
            try {
                update_status($dbh, $status, $item_id);
                $msg[] = '公開ステータス更新成功';
            } catch (PDOException $e) {
                $err_msg[] = '公開ステータス更新失敗';
            }
        }
    }
    
    // 商品を削除する
    if ($process_kind === 'delete_item') {
        // POSTデータの取得
        $item_id =get_post_data('item_id');
        // 入力値チェック
        $err_msg = validate_item_id($item_id);
        if (count($err_msg) === 0) {
            try {
                delete_item($dbh, $item_id);
                $msg[] = '商品削除成功';
            } catch (PDOException $e) {
                $err_msg[] = '商品削除失敗';
            }
        }
    }
    
    // 商品一覧情報取得
    $rows = get_ec_items_table_list($dbh);
    // 特殊文字をHTMLエンティティに変換する
    $rows = entity_assoc_array($rows);
    
} catch (PDOException $e) {
    $err_msg[] = '管理者にお問い合わせください';
}

// トークンの生成
$token = get_csrf_token();

// iframeでの読み込みを禁止する
header("X-FRAME-OPTIONS: DENY");

// 商品管理ページテンプレートファイル読み込み
include_once '../view/items_view.php';
