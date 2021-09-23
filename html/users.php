<?php
// 設定ファイル読み込み
require_once '../conf/const.php';
// 関数ファイル読み込み
require_once '../model/db.php';
require_once '../model/function.php';

// 関数初期化
$err_msg = []; // エラーメッセージ

// セッション開始
session_start();

if ($_SESSION['user_id'] !== 1) {
    // 管理者以外の場合、ログインページへリダイレクト
    header('Location: login.php');
    exit;
}

try {
    // データベースに接続
    $dbh = get_db_connect();
    // ユーザ情報一覧取得
    $rows = get_ec_users_table_list($dbh);
    // 特殊文字をHTMLエンティティに変換
    $rows = entity_assoc_array($rows);
} catch (Exception $e) {
    $err_msg[] = '管理者にお問い合わせください';
}

// ユーザ情報一覧テンプレートファイル読み込み
include_once '../view/users_view.php';