<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>購入完了</title>
        <link rel="stylesheet" href="./css/finish_view.css">
    </head>
    <body>
        <header>
            <div class="header">
                <div class="header-left">
                    <img class="logo" src="./pic/logo.png">   
                </div>
                <div class="header-right">
                    <div class="user">
                        <p>ようこそ、<?php print $user_name; ?>さん！</p>
                    </div>
                    <a href="logout.php" class="logout">ログアウト</a>
                </div>
            </div>
        </header>
        <main>
            <p class="thanks">ご購入ありがとうございました。</p>
            <?php foreach($carts as $read) { ?>
            <div class="flex">
                <div class="item_img">
                    <img src="<?php print img_dir . $read['img']; ?>">    
                </div>
                <div class="item_info">
                    <p><?php print $read['item_name']; ?><br><?php print $read['price']; ?>円<br><?php print $read['amount']; ?>個</p>    
                </div>
            </div>
            <?php } ?>
            <?php if ($sum_price > 0) { ?>
            <p>合計: <?php print $sum_price; ?>円</p>
            <?php } ?>
            <a href="itemlist.php" class="reverse">商品一覧へ戻る</a></br>
            <a href="logout.php" class="logout_btn">ログアウト</a></br>
            <p class="remarks">※ログインページへ移行します。</p>
        </main>
    </body>
</html>