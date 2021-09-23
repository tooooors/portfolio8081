<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>ユーザ登録ページ</title>
        <link rel="stylesheet" href="./css/register_view.css">
    </head>
    <body>
        <header>
            <img class="logo" src="./pic/logo.png">
        </header>
        <main>
            <h1>新規登録</h1>
            <p>任意のユーザー名・パスワードを入力してください。<br>※半角英数字かつ6文字以上にしてください。</p>
            <form method="post">
                <label>ユーザー名　<input type="text" name="user_name" value="<?php print $user_name; ?>"></label><br>
                <label>パスワード　<input type="password" name="password" value="<?php print $password; ?>"></label><br>
                <?php if(count($err_msg) !== 0) {
                    foreach ($err_msg as $read) { ?>
                        <p class="err_msg"><?php print $read; ?></p>
                <?php    }
                } ?>
                <input type="hidden" name="token" value="<?php print $token; ?>">
                <input class="btn" type="submit" value="登録"><br>
            </form>
            <a class="reverse" href="login.php">ログインページへ戻る</a>
        </main>
    </body>
</html>