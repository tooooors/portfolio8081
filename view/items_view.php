<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>商品管理ページ</title>
        <link rel="stylesheet" href="./css/manage.css">
    </head>
    <body>
        <h1>カフェインレスコーヒーオンラインSHOP管理ページ</h1></h1>
         <?php if(count($err_msg) !== 0) {
            foreach ($err_msg as $read) { ?>
            <p><?php print $read; ?></p>
            <?php }
            } else {
                foreach ($msg as $read) { ?>
                <p><?php print $read; ?></p>
            <?php }
            }?>
        <a href="users.php">ユーザ管理ページ</a>
        <a href="itemlist.php">商品一覧ページ</a>
        <a href="logout.php">ログアウト</a>
        <h2>商品の登録</h2>
        <form method="post" enctype="multipart/form-data">
            <label>商品名: <input type="text" name="item_name" value="<?php print $item_name; ?>"></label><br>
            <label>値　段: <input type="text" name="price" value="<?php print $price; ?>"></label><br>
            <label>在庫数: <input type="text" name="stock" value="<?php print $stock; ?>"></label><br>
            <label>内容量: <input type="text" name="cups" value="<?php print $cups; ?>"></label><br>
            <label>商品画像: <input type="file" name="new_img"></label><br>
            <label for="type">淹れ方: </label>
            <select id="type" name="type">
                <option value="1">インスタント</option>
                <option value="2">ドリップ</option>
                <option value="3">その他</option>
            </select><br>
            <label for="status">ステータス: </label>
            <select id="status" name="status">
                <option value="0">非公開</option>
                <option value="1">公開</option>
            </select><br>
            <lavel for="comment">商品詳細情報:</lavel><br>
            <textarea id="comment" name="comment" cols="50" rows="5"></textarea><br>
            <input type="hidden" name="process_kind" value="insert">
            <input type="hidden" name="token" value="<?php print $token; ?>">
            <input type="submit" value="商品を登録する">
        </form>
        <h2>商品情報の一覧・変更</h2>
        <table>
            <tr>
                <th>商品画像</th>
                <th>商品名</th>
                <th>価格</th>
                <th>内容量</th>
                <th>淹れ方</th>
                <th>在庫数</th>
                <th>商品詳細情報</th>
                <th>ステータス</th>
                <th>操作</th>
            </tr>
            <?php foreach($rows as $read) { ?>
            <tr>
                <td><img src="<?php print img_dir . $read['img']; ?>"></td>
                <td><?php print $read['item_name']; ?></td>
                <td><?php print $read['price'] . '円'; ?> </td>
                <td><?php print $read['cups'] . '杯'; ?></td>
                <td><?php print $read['type_name']; ?></td>
                <td>
                    <form method="post">
                        <input type="text" name="update_stock" value="<?php print $read['stock']; ?>"<?php print $stock; ?>>個<br>
                        <input type="hidden" name="process_kind" value="update_stock">
                        <input type="hidden" name="item_id" value="<?php print $read['item_id']; ?>">
                        <input type="hidden" name="token" value="<?php print $token; ?>">
                        <input type="submit" value="更新">
                    </form>
                </td>
                <td>
                    <form method="post">
                        <textarea name="comment" cols="30" rows="5" ><?php print $read['comment']; ?></textarea><br>
                        <input type="hidden" name="process_kind" value="update_comment">
                        <input type="hidden" name="item_id" value="<?php print $read['item_id']; ?>">
                        <input type="hidden" name="token" value="<?php print $token; ?>">
                        <input type="submit" value="更新">
                    </form>
                </td>
                <td>
                    <form method="post">
                        <?php if ($read['status'] === '0') { ?>
                        <input type="hidden" name="process_kind" value="update_status">
                        <input type="hidden" name="status" value=1 >
                        <input type="hidden" name="item_id" value="<?php print $read['item_id']; ?>">
                        <input type="hidden" name="token" value="<?php print $token; ?>">
                        <input type="submit" value="非公開→公開">
                        <?php } else if ($read['status'] === '1'){ ?>
                        <input type="hidden" name="process_kind" value="update_status">
                        <input type="hidden" name="status" value=0>
                        <input type="hidden" name="item_id" value="<?php print $read['item_id'];?>">
                        <input type="hidden" name="token" value="<?php print $token; ?>">
                        <input type="submit" value="公開→非公開">
                        <?php } ?>
                    </form>
                </td>
                <td>
                    <form method="post">
                        <input type="hidden" name="item_id" value="<?php print $read['item_id']; ?>">
                        <input type="hidden" name="process_kind" value="delete_item">
                        <input type="hidden" name="token" value="<?php print $token; ?>">
                        <input type="submit" value="削除する">
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </body>
</html>