<?php
/**
 * POSTデータの取得
*/
function get_post_data($key) {
    $str = '';
    if (isset($_POST[$key]) === TRUE) {
        $str = $_POST[$key];
    }
    return $str;
}

/**
 * チェックボックスで複数選択された場合のPOSTデータの取得
*/
function get_post_array_data($key) {
    $array = [];
    if (isset($_POST[$key]) && is_array($_POST[$key])) {
        $array = $_POST[$key];
    }
    return $array;
}

/**
 * GETデータの取得
*/
function get_get_data($key) {
    $str = '';
    if (isset($_GET[$key]) === TRUE) {
        $str = $_GET[$key];
    }
    return $str;
}

/**
 * 特殊文字をHTMLエンティティに変換する
 * @param str $str 変換前文字
 * @return str 変換後文字
*/
function entity_str($str) {
    return htmlspecialchars($str, ENT_QUOTES, HTML_CHARACTER_SET);
}

/**
 * 特殊文字をHTMLエンティティに変換する（2次元配列の値）
 * @param array $assoc_array 変換前配列
 * @return array 変換後配列
*/
function entity_assoc_array($assoc_array) {
    foreach ($assoc_array as $key => $value) {
        foreach ($value as $keys => $values) {
            // 特殊文字をHTMLエンティティに変換
            $assoc_array[$key][$keys] = entity_str($values);
        }
    }
    return $assoc_array;
}

/**
 * リクエストメソッドを取得
 * @return str POSTなど
*/
function get_request_method() {
    return $_SERVER['REQUEST_METHOD'];
}

/**
 * ランダムな文字列の生成
*/
function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

/**
 * トークンの生成
 * @return str $token 生成したトークン
*/
function get_csrf_token(){
    $token = get_random_string(30);
    // セッション変数にトークンを保存
    $_SESSION['token'] = $token;
    return $token;
}

/**
 * トークンのチェック
 * @param str $token トークン
*/
function is_valid_csrf_token($token){
    if($token === '') {
        return false;
    }
    return $token === $_SESSION['token'];
}