<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>商品詳細</title>
        <link rel="stylesheet" href="./css/detail_view.css">
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
                foreach ($err_msg as $read) { ?>
                    <p class="font_red"><?php print $read; ?></p>
            <?php    }
            } ?>
            <div class="flex background_gray">
                <div class="item_img">
                    <img src="<?php print img_dir . $item['img']; ?>">
                </div>
                <div class="item_info">
                    <p class="item_name"><?php print $item['item_name']; ?></p>
                    <p class="explain"><?php print $item['comment']; ?></p>
                    <p>淹れ方: <?php print $item['type_name']; ?><br>内容量: <?php print $item['cups']; ?>杯</p>
                    <p><?php print $item['price']; ?>円</p>
                    <form method="post" action = "cart.php">
                        <input type="hidden" name="process_kind" value="insert_cart">
                        <input type="hidden" name="item_id" value="<?php print $item['item_id']; ?>">
                        <label>数量: <input type="number" class="amount" name="amount" min="1" value="1"<?php print $amount; ?>></label><br>
                        <input type="hidden" name="token" value="<?php print $token; ?>">
                        <input class="btn" type="submit" value="カートに入れる">
                    </form>
                </div>
            </div>
            <div class="review">
                <h2>口コミ</h2>
                <form method="post">
                    <input type="hidden" name="process_kind" value="review">
                    <input type="hidden" name="item_id" value="<?php print $item_id; ?>">
                    <textarea name="review" cols="80" rows="5" maxlength="250" placeholder="ご意見・ご感想をご記入ください"></textarea><br>
                    <input type="hidden" name="token" value="<?php print $token; ?>">
                    <input type="submit" class="btn_review" value="口コミ投稿">
                </form>
                <?php if (count($item_review) === 0) { ?>
                <p class="font_red">口コミは0件です</p>
                <?php } else { ?>
                <h3>口コミ一覧</h3>
                    <?php foreach ($item_review as $read) { ?>
                        <div class="review_list">
                        <p><?php print $read['review'] ?></p>    
                    </div>    
                <?php    }
                } ?>
                <div class="link">
                    <a class="reverse" href="itemlist.php">商品一覧ページへ戻る</a>
                </div>
            </div>
        </main>
    </body>
</html>