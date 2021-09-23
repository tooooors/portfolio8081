<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>トップページ</title>
        <link rel="stylesheet" href="./css/login_view.css"> 
    </head>
    <body>
        <header>
            <div class="header">
                <img class="logo" src="./pic/logo.png">
            </div>
        </header>
        <main>
            <?php if (count($err_msg) !== 0) {
                    foreach ($err_msg as $read) { ?>
                    <p class="err_msg"><?php print $read; ?></p>
                    <?php }
                } ?>
            <div class="explain">
                <h1>カフェインレスコーヒーとは</h1>
                <p>
                    日本では、カフェインを90パーセント以上除去したコーヒーのことをカフェインレスコーヒーといいます。<br>
                    普通のコーヒーと変わらない美味しさの商品も数多くあります。<br>
                    コーヒーが好きだけどカフェインを控えている方、<br>
                    寝る前のリラックスタイムに、<br>
                    カフェインレスコーヒーのあるくらしを始めてみませんか？
                </p>
            </div>
            <div class="login">
                <h2>ログインして商品を探す</h2>
                <form method="post">
                    <label>ユーザー名　<input type="text" name="user_name" value="<?php print $user_name; ?>"></label><br>
                    <lavel>パスワード　<input type="password" name="password" value="<?php print $password; ?>"></lavel><br>
                    <input type="hidden" name="token" value="<?php print $token; ?>">
                    <input class="btn" type="submit" value="ログイン">
                </form>
                <p>※はじめての方はコチラから新規登録してください</p>
                <a class="btn" href="register.php">新規登録</a>
            </div>
            <div>
                <h2>人気TOP3</h2>
                    <ol class="rank">
                        <li>
                            1位<br>
                            <img src="<?php print img_dir . $rows[0]['img']; ?>">
                            <figcaption><?php print $rows[0]['item_name'];?></figcaption><br>
                            <figcaption class="item_price"><?php print $rows[0]['price'] . '円'; ?></figcaption>
                        </li>
                        <li>
                            2位<br>
                            <img src="<?php print img_dir . $rows[1]['img']; ?>">
                            <figcaption><?php print $rows[1]['item_name']; ?></figcaption><br>
                            <figcaption class="item_price"><?php print $rows[1]['price'] . '円'; ?></figcaption>
                        </li>
                        <li>
                            3位<br>
                            <img src="<?php print img_dir . $rows[2]['img']; ?>">
                            <figcaption><?php print $rows[2]['item_name']; ?></figcaption><br>
                            <figcaption class="item_price"><?php print $rows[2]['price'] . '円'; ?></figcaption>
                        </li>
                    </ol>
                </div>
            </div>
        </main>
    </body>
</html>