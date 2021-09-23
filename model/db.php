<?php
/**
 * DBハンドルを取得
 * @return obj $dbh DBハンドル
*/
function get_db_connect() {
    try {
        // データベースに接続
        $dbh = new PDO(DSN, DB_USER, DB_PASSWD, array(PDO::MYSQL_ATTR_INIT_COMMAND => DB_CHAESET));
        $dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh -> setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        throw $e;
    }
    return $dbh;
}

/**
 * クエリを実行しその結果を配列で取得する
 * @param obj $dbh DBハンドル
 * @param str $sql SQL文
 * @return array 結果配列データ
*/
function get_as_array($dbh, $sql) {
    try {
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQLを実行
        $stmt -> execute();
        // レコードの取得
        $rows = $stmt->fetchAll();
    } catch (PDOException $e) {
        throw $e;
    }
    return $rows;
}

/**
 * 商品一覧を取得する（管理用）
 * @param obj $dbh DBハンドル
 * @return array 商品一覧データ
*/
function get_ec_items_table_list($dbh) {
    // SQL生成
    $sql = 'SELECT ec_items.item_id, item_name, price, img, status, ec_items.type, cups, comment, stock, type_name
            FROM ec_items
            INNER JOIN ec_stock on ec_items.item_id = ec_stock.item_id
            INNER JOIN ec_type on ec_items.type = ec_type.type
            ORDER BY ec_items.create_date DESC';
    return get_as_array($dbh, $sql);
}

/**
 * 商品一覧を取得する（公開ステータス確認あり）
 * @pram obj $dbh DBハンドル
 * @return array 商品一覧 
*/
function get_itemlist($dbh) {
    // SQL生成
    $sql = 'SELECT ec_items.item_id, item_name, price, img, status, ec_items.type, cups, comment, stock, type_name
            FROM ec_items
            INNER JOIN ec_stock on ec_items.item_id = ec_stock.item_id
            INNER JOIN ec_type on ec_items.type = ec_type.type
            WHERE status = 1
            ORDER BY ec_items.create_date DESC';
    return get_as_array($dbh, $sql);
}

function get_recommend_itemlist($dbh) {
    // SQL生成
    $sql = 'SELECT ec_items.item_id, item_name, price, img, status, ec_items.type, cups, comment, stock, type_name
            FROM ec_items
            INNER JOIN ec_stock on ec_items.item_id = ec_stock.item_id
            INNER JOIN ec_type on ec_items.type = ec_type.type
            WHERE status = 1 AND stock > 0
            ORDER BY ec_items.create_date DESC';
    return get_as_array($dbh, $sql);
}

/**
 * キーワード検索結果を取得する
 * @param obj $dbh DBハンドル
 * @param str $keyword 検索ワード
 * @return 
*/
function get_keyword_search_results($dbh, $keyword) {
    try {
        // SQL生成
        $sql = 'SELECT ec_items.item_id, item_name, price, img, status, ec_items.type, cups, comment, stock, type_name
                FROM ec_items
                INNER JOIN ec_stock on ec_items.item_id = ec_stock.item_id
                INNER JOIN ec_type on ec_items.type = ec_type.type
                WHERE item_name like :item_name AND status = 1
                ORDER BY ec_items.create_date DESC';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt -> bindValue(':item_name', '%' . $keyword . '%', PDO::PARAM_STR);
        // SQL文を実行
        $stmt -> execute();
        // レコードの取得
        $items = $stmt->fetchAll();
    } catch (PDOException $e) {
        throw $e;
    }
    return $items;
}

/**
 * 絞り込み検索結果を取得する
 * @param obj $dbh DBハンドル
 * @param array $type 淹れ方
 * @param str $price 価格
 * @param str $cups 内容量
 * @return array $items 絞り込み結果
*/
function get_refined_search_results($dbh, $type, $price, $cups) {
    try {
        // 淹れ方未選択の場合
        if (empty($type) === true) {
            $type = [1, 2, 3];
        }
        // 淹れ方選択数に合わせたプレースホルダ作成
        $num = count($type);
        $place = substr(str_repeat(',?', $num), 1);
        // 価格未選択の場合
        if (empty($price) === true) {
            $price = '1,999999';
        }
        // 配列化
        $price = explode(',', $price);
        // 内容量未選択の場合
        if (empty($cups) === true) {
            $cups = '1,9999';
        }
        // 配列化
        $cups = explode(',', $cups);
        // SQL生成
        $sql = 'SELECT ec_items.item_id, item_name, price, img, status, ec_items.type, cups, comment, stock, type_name
                FROM ec_items
                INNER JOIN ec_stock on ec_items.item_id = ec_stock.item_id
                INNER JOIN ec_type on ec_items.type = ec_type.type
                WHERE ec_items.type in ('.$place.') AND price between ? and ? AND cups between ? and ? AND status = 1
                ORDER BY ec_items.create_date DESC';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        for ($i = 0; $i < $num; $i++) {
            $stmt -> bindValue($i + 1, $type[$i], PDO::PARAM_INT);    
        }
        $stmt -> bindValue($num + 1, $price[0], PDO::PARAM_INT);
        $stmt -> bindValue($num + 2, $price[1], PDO::PARAM_INT);
        $stmt -> bindValue($num + 3, $cups[0], PDO::PARAM_INT);
        $stmt -> bindValue($num + 4, $cups[1], PDO::PARAM_INT);
        // SQL文を実行
        $stmt -> execute();
        // レコードの取得
        $items = $stmt->fetchAll();
    } catch (PDOException $e) {
        throw $e;
    }
    return $items;
}

/**
 * おすすめ商品をランダムに取得する
 * @param obj $dbh DBハンドル
 * @return array $recommend_item おすすめ商品
*/
function recommend_item($dbh) {
    $recommend_item = [];
    // 商品一覧を取得
    $items = get_recommend_itemlist($dbh);
    // HTMLエンティティに変換
    $items = entity_assoc_array($items);
    // おすすめ商品をランダムで選択
    $num = count($items) - 1;
    $recommend_num = rand(0, $num);
    $recommend_item = $items[$recommend_num];
    return $recommend_item;
}

/**
 * 商品を売上個数順に取得する
 * @param obj $dbh DBハンドル
 * @return array 売上個数順の商品一覧データ
*/
function get_popular_items($dbh) {
    $sql = 'SELECT item_name, img, price FROM ec_items
            LEFT OUTER JOIN (select item_id, IFNULL(sum(amount), 0) as amount_sum FROM ec_history group by item_id) as sales
            ON ec_items.item_id = sales.item_id
            ORDER BY amount_sum DESC';
    return get_as_array($dbh, $sql);
}

/**
 * 商品詳細情報を取得する
 * @param obj $dbh DBハンドル
 * @param int $item_id 商品ID
 * @return array 商品情報
*/
function get_item_detail($dbh, $item_id) {
    try {
        // SQL生成
        $sql = 'SELECT item_id, item_name, price, img, comment, type_name, cups
                FROM ec_items
                INNER JOIN ec_type on ec_items.type = ec_type.type
                where item_id = :item_id';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(':item_id', $item_id, PDO::PARAM_INT);
        // SQL文を実行
        $stmt->execute();
        // データを取得
        $item = $stmt->fetch();        
    } catch (PDOException $e) {
        throw $e;
    }
    return $item;
}

/**
 * 口コミ取得
 * @param obj $dbh DBハンドル
 * @param int $item_id 商品ID
 * @return array 口コミ・ユーザーID
*/
function get_review($dbh, $item_id) {
    try {
        // SQL生成
        $sql = 'SELECT review, user_id FROM ec_review WHERE item_id = :item_id ORDER BY update_date DESC';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(':item_id', $item_id, PDO::PARAM_INT);
        // SQL文を実行
        $stmt->execute();
        // データを取得
        $review = $stmt->fetchAll();        
    } catch (PDOException $e) {
        throw $e;
    }
    return $review;
}

/**
 * ユーザ情報一覧を取得する
 * @param obj $dbh DBハンドル
 * @return array ユーザ情報一覧データ
*/
function get_ec_users_table_list($dbh) {
    // SQL生成
    $sql = 'SELECT user_name, create_date FROM ec_users ORDER BY create_date DESC';
    return get_as_array($dbh, $sql);
}

/**
 * ユーザIDを取得する
 * @param obj $dbh DBハンドル
 * @param str $user_name ユーザー名
 * @return array ユーザーID
*/
function get_user_id($dbh, $user_name) {
    try {
        // SQLを生成する
        $sql = 'SELECT user_id FROM ec_users where user_name = :user_name';
        // SQLを実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
        // SQL文を実行
        $stmt->execute();
        // レコードの取得
        $user_id = $stmt->fetch();
    } catch(PDOException $e) {
        throw $e;
    }
    return $user_id;
}

/**
 * カート情報を取得する
 * @param obj $dbh DBハンドル
 * @param int $user_id ユーザーID
 * @return array $carts カート情報
*/
function get_carts($dbh, $user_id) {
    try {
        // SQLの生成
        $sql = 'SELECT ec_carts.item_id, img, item_name, price, amount FROM ec_carts
                INNER JOIN ec_items on ec_carts.item_id = ec_items.item_id
                where user_id = :user_id';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        // SQL文を実行
        $stmt->execute();
        // レコードの取得
        $carts = $stmt->fetchAll();
    } catch (PDOException $e) {
        throw $e;
    }
    return $carts;
}

/**
 * 商品を削除する
 * @param obj $dbh DBハンドル
 * @param int $item_id 商品ID
*/
function delete_item($dbh, $item_id) {
    try {
        // SQL生成
        $sql = 'DELETE ec_items, ec_stock FROM ec_items INNER JOIN ec_stock ON ec_items.item_id = ec_stock.item_id 
                where ec_items.item_id = :item_id';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(':item_id', $item_id, PDO::PARAM_INT);
        // SQL文を実行
        $stmt->execute();    
    } catch (PDOException $e) {
        throw $e;
    }
}

/**
 * カート内の商品を削除する
 * @param obj $dbh DBハンドル
 * @param int $item_id 商品ID
 * @param int $user_id ユーザーID
*/
function delete_cart_item($dbh, $item_id, $user_id) {
    try {
        // SQL生成
        $sql = 'DELETE FROM ec_carts WHERE item_id = :item_id AND user_id = :user_id';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
        $stmt -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
        // SQL文を実行
        $stmt -> execute();
    } catch (PDOException $e) {
        throw $e;
    }
}

/**
 * 購入時カート内の商品を削除する
 * @param obj $dbh DBハンドル
 * @param int $user_id ユーザーID
*/
function delete_cart($dbh, $user_id) {
    try {
        // SQL生成
        $sql = 'DELETE FROM ec_carts WHERE user_id = :user_id';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
        // SQL文を実行
        $stmt -> execute();
    } catch (PDOException $e) {
        throw $e;
    }
}



/**
 * 新規商品追加
 * @param obj $dbh DBハンドル
 * @param str $item_name 商品名
 * @param int $price 商品価格
 * @param str $new_img_filename 新しいファイル名
 * @param int $status 公開ステータス
 * @param int $type 淹れ方
 * @param int $cups 内容量
 * @param str $comment 商品詳細情報
 * @param int $stock 在庫数
*/
function insert_item($dbh, $item_name, $price, $new_img_filename, $status, $type, $cups, $comment, $stock) {
    // トランザクション開始
    $dbh->beginTransaction();
    try {
        // ec_itemsへ挿入
        // SQL生成
        $sql = 'INSERT into ec_items (item_name, price, img, status, type, cups, comment, create_date, update_date)
                value (:item_name, :price, :img, :status, :type, :cups, :comment, now(), now())';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt -> bindValue(':item_name', $item_name, PDO::PARAM_STR);
        $stmt -> bindValue(':price', $price, PDO::PARAM_INT);
        $stmt -> bindValue(':img', $new_img_filename, PDO::PARAM_STR);
        $stmt -> bindValue(':status', $status, PDO::PARAM_INT);
        $stmt -> bindValue(':type', $type, PDO::PARAM_INT);
        $stmt -> bindValue(':cups', $cups, PDO::PARAM_INT);
        $stmt -> bindValue(':comment', $comment, PDO::PARAM_STR);
        // SQL文を実行
        $stmt->execute();
        // idを取得
        $id = $dbh->lastInsertId();
        
        // ec_stockへ挿入
        // SQLを生成
        $sql = 'INSERT into ec_stock (item_id, stock, create_date, update_date)
                value (:item_id, :stock, now(), now())';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt -> bindValue(':item_id', $id, PDO::PARAM_INT);
        $stmt -> bindValue(':stock', $stock, PDO::PARAM_INT);
        // SQL文を実行
        $stmt -> execute();
        
        // コミット処理
        $dbh -> commit();
    } catch (PDOException $e) {
        // ロールバック処理
        $dbh -> rollback();
        // 例外をスロー
        throw $e;
    }
}

/**
 * 口コミ追加
 * @param obj $dbh DBハンドル
 * @param int $item_id 商品ID
 * @param int $user_id ユーザーID
 * @param str $review 口コミ
*/
function insert_review($dbh, $item_id, $user_id, $review) {
    try {
        // SQL生成
        $sql = 'INSERT into ec_review (item_id, user_id, review, create_date, update_date)
                value (:item_id, :user_id, :review, now(), now())';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
        $stmt -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt -> bindValue(':review', $review, PDO::PARAM_STR);
        // SQL文を実行
        $stmt -> execute();
    } catch (PDOException $e) {
        throw $e;
    }
}

/**
 * カートに追加
 * @param obj $dbh DBハンドル
 * @param int $user_id ユーザーID
 * @param int $item_id 商品ID
 * @param int $amount 購入予定商品数
*/
function insert_cart($cart_match, $dbh, $user_id, $item_id, $amount) {
    try {
        if ($cart_match === false) {
            // SQL生成
            $sql = 'INSERT into ec_carts (user_id, item_id, amount, create_date, update_date)
                    value(:user_id, :item_id, :amount, now(), now())';    
        } else {
            // SQL生成
            $sql = 'UPDATE ec_carts
                    SET amount = amount + :amount, update_date = now()
                    WHERE item_id = :item_id AND user_id = :user_id';
        }
        
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
        $stmt -> bindValue(':amount', $amount, PDO::PARAM_INT);
        // SQL文を実行
        $stmt ->execute();
    } catch (PDOException $e) {
        throw $e;
    }
}

/**
 * 購入履歴追加
 * @param obj $dbh DBハンドル
 * @param int $item_id 商品ID
 * @param int $amount 購入数
*/
function insert_history($dbh, $item_id, $amount) {
    try {
        // SQL生成
        $sql = 'INSERT into ec_history (item_id, amount, create_date)
                VALUES (:item_id, :amount, now())';
        // SQLを実行する準備
        $stmt = $dbh -> prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
        $stmt -> bindValue(':amount', $amount, PDO::PARAM_INT);
        // SQL文を実行
        $stmt -> execute();
    } catch (PDOException $e) {
        throw $e;
    }
}

/**
 * 在庫数更新
 * @param obj $dbh DBハンドル
 * @param int $stock 在庫数
 * @param int $item_id 商品ID
*/
function update_stock($dbh, $stock, $item_id) {
    try {
         // SQL生成
        $sql = 'UPDATE ec_stock SET stock = :stock, update_date = now() where item_id = :item_id';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(':stock', $stock, PDO::PARAM_INT);
        $stmt->bindValue(':item_id', $item_id,PDO::PARAM_INT);
        // SQL文を実行
        $stmt->execute();
    } catch (PDOException $e) {
        throw $e;
    }
}

/**
 * 購入時在庫数更新
 * @param obj $dbh DBハンドル
 * @param int $amount 購入数
 * @param int $item_id 商品ID
*/
function buy_update_stock($dbh, $amount, $item_id) {
    try {
         // SQL生成
        $sql = 'UPDATE ec_stock SET stock = stock - :amount, update_date = now() where item_id = :item_id';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(':amount', $amount, PDO::PARAM_INT);
        $stmt->bindValue(':item_id', $item_id,PDO::PARAM_INT);
        // SQL文を実行
        $stmt->execute();
    } catch (PDOException $e) {
        throw $e;
    }
}


/**
 * 商品詳細情報更新
 * @param obj $dbh DBハンドル
 * @param str $comment 商品詳細情報
 * @param int $item_id 商品ID
*/
function update_comment($dbh, $comment, $item_id) {
    try {
        // SQL生成
        $sql = 'UPDATE ec_items SET comment = :comment, update_date = now() where item_id = :item_id';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindValue(':item_id', $item_id, PDO::PARAM_INT);
        // SQL文を実行
        $stmt->execute();
    } catch (PDOException $E) {
        throw $e;
    }
}

/**
 * 公開ステータス更新
 * @param obj $dbh DBハンドル
 * @param int $status 公開ステータス
 * @param int $item_id 商品ID
*/
function update_status($dbh, $status, $item_id) {
    try {
         // SQL生成
        $sql = 'UPDATE ec_items SET status = :status, update_date = now() where item_id = :item_id';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(':status', $status, PDO::PARAM_INT);
        $stmt->bindValue(':item_id', $item_id, PDO::PARAM_INT);
        // SQL文を実行
        $stmt->execute();
    } catch (PDOException $e) {
        throw $e;
    }
}

/**
 * 購入予定商品数更新
 * @param obj $dbh DBハンドル
 * @param int $amount 購入予定商品数
 * @param int $item_id 商品ID
*/
function update_amount($dbh, $amount, $item_id) {
    try {
         // SQL生成
        $sql = 'UPDATE ec_carts SET amount = :amount, update_date = now() where item_id = :item_id';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(':amount', $amount, PDO::PARAM_INT);
        $stmt->bindValue(':item_id', $item_id,PDO::PARAM_INT);
        // SQL文を実行
        $stmt->execute();
    } catch (PDOException $e) {
        throw $e;
    }
}


/**
 * ユーザー名とパスワードの確認
 * @param obj $dbh DBハンドル
 * @param str $user_name ユーザー名
 * @param str $password パスワード
 * @return array $err_msg エラーメッセージ
*/
function user_match($dbh, $user_name, $password) {
    $err_msg = [];
    try {
        // SQL生成
        $sql = 'SELECT user_name, password FROM ec_users where user_name = :user_name';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
        // SQL文を実行
        $stmt->execute();
        // レコードの取得
        $row = $stmt->fetch();
    } catch (PDOException $e) {
        throw $e;
    }
    if ($row['password'] !== $password || $row['user_name'] !== $user_name) {
        $err_msg[] = 'ユーザー名あるいはパスワードが違います';
    }
    return $err_msg;
}

/**
 * 既に同じユーザー名が登録されていないかチェック
 * @param obj $dbh DBハンドル
 * @param str $user_name ユーザー名
 * @return array $err_msg エラーメッセージ
*/
function user_name_match($dbh, $user_name) {
    $err_msg = [];
    try {
        // SQL生成
        $sql = 'SELECT user_name FROM ec_users where user_name = :user_name';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt-> bindValue(':user_name', $user_name, PDO::PARAM_STR);
        // SQL文を実行
        $stmt->execute();
        // レコードの取得
        $row = $stmt->fetch();
    } catch (PDOException $e) {
        throw $e;
    }
    if ($row['user_name'] === $user_name) {
        $err_msg[] = 'このユーザー名は既に使われています';
    }
    return $err_msg;
}

/**
 * 既に同じユーザが同じ商品をカートに入れていないかチェック
 * @param obj $dbh DBハンドル
 * @param int $user_id ユーザーID
 * @param int $item_id 商品ID
 * @return $count_cart 同じユーザー、同じ商品のレコード数
*/
function cart_match($dbh, $user_id, $item_id) {
    try {
        // SQL生成
        $sql = 'SELECT item_id FROM ec_carts WHERE user_id = :user_id AND item_id = :item_id';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
        // SQL文を実行
        $stmt -> execute();
        // レコードの取得
        $cart_match = $stmt->fetch();
        // レコード数の取得
        //$count_cart = count($row);
    } catch (PDOException $e) {
        throw $e;
    }
    return $cart_match;
}

/**
 * ユーザー登録
 * @param obj $dbh DBハンドル
 * @param str $user_name ユーザー名
 * @param str $password パスワード
*/
function user_register($dbh, $user_name, $password) {
    try {
        // SQL生成
        $sql = 'INSERT into ec_users(user_name, password, create_date, update_date)
                value (:user_name, :password, now(), now())';
        // SQLを実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        // SQL文を実行
        $stmt->execute();
    } catch (PDOException $e) {
        throw $e;
    }
}

/**
 * 合計価格の取得
 * @param obj $dbh DBハンドル
 * @param int $user_id ユーザーID
 * @return $sum_price 合計価格
*/
function get_sum_price($dbh, $user_id) {
    try {
        // SQL生成
        $sql = 'SELECT price, amount
                FROM ec_carts
                INNER JOIN ec_items ON ec_carts.item_id = ec_items.item_id
                WHERE user_id = :user_id';
        // SQL文を実行する準備
        $stmt = $dbh -> prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
        // SQL文を実行
        $stmt -> execute();
        // レコードの取得
        $rows = $stmt->fetchAll();
        // レコード数の取得
        $num = count($rows);
        // 合計価格の取得
        $sum_price = 0;
        for ($i = 0; $i < $num; $i++) {
            $sum_price += $rows[$i]['price'] * $rows[$i]['amount']; 
        }
    } catch (PDOException $e) {
        throw $e;
    }
    return $sum_price;
}

/**
 * 購入時、在庫数と公開ステータスの確認
 * @param obh $dbh DBハンドル
 * @param $user_id ユーザーID
 * @return array $err_msg エラーメッセージ
*/
function buy_match($dbh, $user_id) {
    try {
        $sql = 'SELECT amount, item_name, stock, status
                FROM ec_carts
                INNER JOIN ec_items ON ec_carts.item_id = ec_items.item_id
                INNER JOIN ec_stock ON ec_carts.item_id = ec_stock.item_id
                WHERE user_id = :user_id';
        $stmt = $dbh -> prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
        // SQL文を実行
        $stmt -> execute();
        // レコードの取得
        $rows = $stmt->fetchAll();
        // レコード数の取得
        $num = count($rows);
        // 在庫数と公開ステータスの確認
        $err_msg = [];
        for ($i = 0; $i < $num; $i++) {
            if ($rows[$i]['stock'] - $rows[$i]['amount'] < 0) {
                $err_msg[] = $rows[$i]['item_name'] . 'の在庫数は' . $rows[$i]['stock'] . '個のため、購入数を変更してください';
            }
            if ($rows[$i]['status'] === 0) {
                $err_msg[] = $rows[$i]['item_name'] . 'は非公開です。カートから削除してください';
            }
        }
    } catch (PDOException $e) {
        throw $e;
    }
    return $err_msg;
}

/**
 * 商品IDが公開中のものであるか確認
 * @param obj $dbh DBハンドル
 * @param int $item_id 商品ID
 * @return array $err_msg エラーメッセージ
*/
function item_id_check($dbh, $item_id) {
    $err_msg = [];
    try {
        // SQL生成
        $sql = 'SELECT item_id FROM ec_items
                WHERE item_id = :item_id AND status = 1';
        // SQL文を実行する準備
        $stmt = $dbh -> prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
        // SQL文を実行
        $stmt -> execute();
        // レコードの取得
        $item = $stmt -> fetch();
        if ($item === false) {
            $err_msg[] = 'この商品は非公開です';
        }
    } catch (PDOException $e) {
        throw $e;
    }
    return $err_msg;
}