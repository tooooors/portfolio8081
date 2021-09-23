<?php
// 設定ファイルの読み込み
require_once '../conf/const.php';
// 関数ファイルの読み込み
require_once '../model/function.php';
require_once '../model/validate.php';
require_once '../model/db.php';

// 関数初期化
$user_name = ''; // ユーザー名
$err_msg = []; // エラーメッセージ
$keyword = ''; // 検索ワード
$process_kind = ''; // 判定用
$recommend_item = ''; // おすすめ商品


// セッション開始
session_start();
// セッション変数からuser_name取得
if (isset($_SESSION['user_name'])) {
    $user_name = $_SESSION['user_name'];
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

// POSTデータの取得
$process_kind = get_post_data('process_kind');

try {
    // DBハンドル取得
    $dbh = get_db_connect();
    // キーワード検索時
    if ($process_kind === 'key_search') {
        // POSTデータの取得
        $keyword = get_post_data('keyword');
        // キーワード検索結果の取得
        $items = get_keyword_search_results($dbh, $keyword);
    } elseif ($process_kind === 'refined_search'){
        // POSTデータの取得
        $type = get_post_array_data('type');
        $price = get_post_data('price');
        $cups = get_post_data('cups');
        // int型へ変換
        $type = array_map('intval', $type);
        // 絞り込み検索結果の取得
        $items = get_refined_search_results($dbh, $type, $price, $cups);
    }else {
        // 商品一覧を取得
        $items = get_itemlist($dbh);
    }
    // HTMLエンティティに変換
    $items = entity_assoc_array($items);
    // 表示する商品数
    $item_num = count($items);
    // おすすめ商品をランダムで取得
    $recommend_item = recommend_item($dbh);
} catch (PDOException $e) {
    $err_msg[] = '管理者にお問い合わせください';
}

// トークンの生成
$token = get_csrf_token();

// iframeでの読み込みを禁止する
header("X-FRAME-OPTIONS: DENY");

// 商品一覧テンプレートファイル読み込み
include_once '../view/itemlist_view.php';