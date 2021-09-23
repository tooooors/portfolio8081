<?php

/**
 * 商品追加時入力値チェック
 * @param str $item_name 商品名
 * @param int $price 商品価格
 * @param int $stock 在庫数
 * @param int $cups 内容量
 * @param int $type 淹れ方
 * @param int $status 公開ステータス
 * @param str $comment 商品詳細情報
 * @return array $err_msg エラーメッセージ
*/
function validate_add_item($item_name, $price, $stock, $cups, $type, $status, $comment) {
    $err_msg = [];
    if (mb_strlen($item_name) === 0 || preg_match(pattern_null, $item_name) === 1) {
        $err_msg[] = '商品名を入力してください';
    }
    if (mb_strlen($price) === 0 || preg_match(pattern_null, $price) === 1) {
        $err_msg[] = '値段を入力してください';
    } else if (preg_match(pattern_num, $price) === 0) {
        $err_msg[] = '値段は半角数字を入力してください';
    }
    if (mb_strlen($stock) === 0 || preg_match(pattern_null, $stock) === 1) {
        $err_msg[] = '在庫数を入力してください';
    } else if (preg_match(pattern_num, $stock) === 0) {
        $err_msg[] = '在庫数は半角数字を入力してください';
    }
    if (mb_strlen($cups) === 0 || preg_match(pattern_null, $cups) === 1) {
        $err_msg[] = '内容量を入力してください';
    } else if (preg_match(pattern_num, $cups) === 0) {
        $err_msg[] = '内容量は半角数字を入力してください';
    }
    if (preg_match(pattern_type, $type) === 0) {
        $err_msg[] = '淹れ方を選択してください';
    }
    if (preg_match(pattern_status, $status) === 0) {
        $err_msg[] = '公開ステータスを選択してください';
    }
    if (mb_strlen($comment) === 0 || preg_match(pattern_null, $comment) === 1) {
        $err_msg[] = '商品詳細情報を入力してください';
    }
    return $err_msg;
}


/**
 * 在庫数更新時入力値チェック
 * @param int $stock 在庫数
 * @return array $err_msg エラーメッセージ
*/
function validate_stock($stock, $item_id) {
    $err_msg = [];
    if (mb_strlen($stock) === 0 || preg_match(pattern_null, $stock) === 1) {
        $err_msg[] = '在庫数を入力してください';
    } else if (preg_match(pattern_num, $stock) === 0) {
        $err_msg[] = '在庫数は半角数字を入力してください';
    }
    if (mb_strlen($item_id) === 0 || preg_match(pattern_null, $item_id) === 1) {
        $err_msg[] = '商品ＩＤが未入力です';
    } else if (preg_match(pattern_num, $item_id) === 0) {
        $err_msg[] = '商品ＩＤは半角数字を入力してください';
    }
    return $err_msg;
}

/**
 * 商品詳細情報更新時入力値チェック
 * @param int $commet 商品詳細情報
 * @param int $item_id 商品ID
 * @return array $err_msg エラーメッセージ
*/
function validate_comment($comment, $item_id) {
    $err_msg = [];
    if (mb_strlen($comment) === 0 || preg_match(pattern_null, $comment) === 1) {
        $err_msg[] = 'コメントを入力してください';
    }
    if (mb_strlen($item_id) === 0 || preg_match(pattern_null, $item_id) === 1) {
        $err_msg[] = '商品ＩＤが未入力です';
    } else if (preg_match(pattern_num, $item_id) === 0) {
        $err_msg[] = '商品ＩＤは半角数字を入力してください';
    }
    return $err_msg;
}

/**
 * 公開ステータス更新時入力値チェック
 * @param int $status 公開ステータス
 * @param int $item_id 商品ID
 * @return array $err_msg エラーメッセージ
*/
function validate_status($status, $item_id) {
    $err_msg = [];
    if (preg_match(pattern_status, $status) === 0) {
        $err_msg[] = '公開ステータスを選択してください';
    }
    if (mb_strlen($item_id) === 0 || preg_match(pattern_null, $item_id) === 1) {
        $err_msg[] = '商品ＩＤが未入力です';
    } else if (preg_match(pattern_num, $item_id) === 0) {
        $err_msg[] = '商品ＩＤは半角数字を入力してください';
    }
    return $err_msg;
}

/**
 * 商品ＩＤ入力値チェック
 * @param int $item_id 商品ID
 * @return array $err_msg エラーメッセージ
*/
function validate_item_id($item_id) {
    $err_msg = [];
    if (mb_strlen($item_id) === 0 || preg_match(pattern_null, $item_id) === 1) {
        $err_msg[] = '商品ＩＤが未入力です';
    } else if (preg_match(pattern_num, $item_id) === 0) {
        $err_msg[] = '商品ＩＤは半角数字を入力してください';
    }
    return $err_msg;
}

/**
 * ユーザ登録時入力値チェック
 * @param str $user_name ユーザー名
 * @param str $password パスワード
 * @return array エラーメッセージ
*/
function validate_user($user_name, $password) {
    $err_msg = [];
    if (preg_match(pattern_user, $user_name) === 0) {
        $err_msg[] = 'ユーザー名は6文字以上の半角英数字で入力してください';
    }
    if (preg_match(pattern_user, $password) === 0) {
        $err_msg[] = 'パスワードは6文字以上の半角英数字で入力してください';
    }
    return $err_msg;
}

/**
 * 口コミ投稿時チェック
 * @param str $review 口コミ
 * @return array エラーメッセージ
*/
function validate_review($review) {
    $err_msg = [];
    if (mb_strlen($review) === 0 || preg_match(pattern_null, $review) === 1) {
        $err_msg[] = '口コミを入力してください';
    }
    return $err_msg;
}

/**
 * 購入予定商品数更新時入力値チェック
 * @param int $amount 在庫数
 * @return array $err_msg エラーメッセージ
*/
function validate_amount($amount) {
    $err_msg = [];
    if (mb_strlen($amount) === 0 || preg_match(pattern_null, $amount) === 1) {
        $err_msg[] = '購入予定数を入力してください';
    } else if (preg_match(pattern_num, $amount) === 0) {
        $err_msg[] = '購入予定数は半角数字を入力してください';
    }
    return $err_msg;
}

