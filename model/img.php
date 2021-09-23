<?php

function get_img($key) {
    $new_img_filename = '';
    $err_msg = [];
    // HTTP POSTでファイルがアップロードされたかどうかチェック
    if (is_uploaded_file($_FILES[$key]['tmp_name']) === TRUE) {
        // 画像の拡張子を取得
        $extension = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
        // 指定の拡張子であるかどうかチェック
        if (preg_match(pattern_extension, $extension) === 1) {
            // 保存する新しいファイル名の生成（ユニークな値を設定する）
                $new_img_filename = sha1(uniqid(mt_rand(), true)). '.' . $extension;
            // 同名ファイルが存在するかどうかチェック
            if (is_file(img_dir . $new_img_filename) !== TRUE) {
                // アップロードされたファイルを指定ディレクトリに移動して保存
                if (move_uploaded_file($_FILES[$key]['tmp_name'], img_dir . $new_img_filename) !== TRUE) {
                    $err_msg[] = 'ファイルアップロードに失敗しました';
                }
            } else {
                $err_msg[] = 'ファイルアップロードに失敗しました。再度お試しください';
            }
        } else {
            $err_msg[] = 'ファイル形式が異なります。画像ファイルはJPEGまたはPNGのみ利用可能です';
        }
    } else {
        $err_msg[] = 'ファイルを選択してください';
    }
    return array($new_img_filename, $err_msg);
}