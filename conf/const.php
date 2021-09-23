<?php

// データベースの接続情報
define('DB_USER', 'testuser'); // MySQLのユーザ名
define('DB_PASSWD', 'password'); // MySQLのパスワード
define('DB_NAME', 'portfolio8081'); // MySQLのDB名
define('DB_CHAESET', 'SET NAMES utf8mb4'); // MySQLのcharset
define('DSN', 'mysql:dbname='.DB_NAME.';host=localhost;charset=utf8'); // データベースのDSN情報

define('img_dir', './img/'); // アップロードした画像ファイルの保存ディレクトリ
define('HTML_CHARACTER_SET', 'UTF-8'); // HTML文字エンコーディング

// 正規表現
define('pattern_num', '/^[0-9]+$/'); // 0以上の整数
define('pattern_null', '/^[\s|　]+$/'); // 　行頭・行末に半角スペースまたは全角スペースが1個以上繰り返し存在する場合
define('pattern_status', '/^[0-1]$/'); // 0または1
define('pattern_type', '/^[1-3]$/'); // 1,2,3
define('pattern_extension', '/^(jpg|jpeg|png)$/i'); // ファイル形式
define('pattern_user', '/^[0-9a-z]{6,}$/i'); // 半角英数字かつ6文字以上