<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>カート</title>
        <link rel="stylesheet" href="./css/cart_view.css">
    </head>
    <body>
        <header>
            <div class="header">
                <div class=header-left>
                    <img class="logo" src="./pic/logo.png">   
                </div>
                <div class="header-right">
                    <div class="user">
                        <p>ようこそ、<?php print $user_name; ?>さん！</p>
                    </div>
                    <div class="cart">
                        <a href="cart.php" id="cart"><img class="cart_icon" src="./pic/cart.png"></a><br>   
                        <label for="cart">カート</label>   
                    </div>
                    <a href="logout.php" class="logout">ログアウト</a>
                </div>    
            </div>
        </header>
        <main>
            <?php if (count($err_msg) !== 0) {
                foreach ($err_msg as $read) {?>
            <p class="font_red"><?php print $read; ?></p>
            <?php }
            } ?>
            <?php if (count($msg) !== 0) {
                foreach($msg as $read) { ?>
                <p><?php print $read; ?></p>
            <?php    }
            } ?>
            <h1>ショッピングカート</h1>
            <?php if (count($carts) === 0) { ?>
            <p class="font_red">カートは空です</p>
            <?php } else { 
            foreach ($carts as $read) { ?>
            <div class="flex">
                <div class="cart_img">
                    <img src="<?php print img_dir . $read['img']; ?>">
                </div>
                <div class="cart_info">
                    <p><?php print $read['item_name']; ?><br><?php print $read['price']; ?>円</p>
                    <form method="post">
                        <input type="hidden" name="process_kind" value="update_amount">
                        <input type="hidden" name="item_id" value="<?php print $read['item_id']; ?>">
                        <label>個数<input class="amount" type="text" name="amount" value="<?php print $read['amount']; ?>"<?php print $amount; ?>></label>
                        <input type="hidden" name="token" value="<?php print $token; ?>">
                        <input class="cart_btn" type="submit" value="変更">
                    </form>
                </div>
                <div class="delete">
                    <form method="post">
                        <input type="hidden" name="process_kind" value="delete_cart_item">
                        <input type="hidden" name="item_id" value="<?php print $read['item_id']; ?>">
                        <input type="hidden" name="token" value="<?php print $token; ?>">
                        <input class="cart_btn delete_btn" type="submit" value="削除">
                    </form>
                </div>
            </div>
            <?php } ?>
            <div class="text_center">
                 <p class="sum">合計: <?php print $sum_price; ?>円</p>
            <form method="post">
                <input type="hidden" name="process_kind" value="buy">
                <input type="hidden" name="token" value="<?php print $token; ?>">
                <input class="btn" type="submit" value="購入する">
            </form><br>
            </div>
            <?php } ?>
            <div class="text_center">
                <a class="reverse" href="itemlist.php">商品一覧へ戻る</a> 
            </div>
        </main>
    </body>
</html>