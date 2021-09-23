<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>ユーザ管理ページ</title>
        <link rel="stylesheet" href="./css/manage.css">
    </head>
    <body>
        <h1>カフェインレスコーヒーオンラインSHOP管理ページ</h1>
        <a href="items.php">商品管理ページ</a>
        <h2>ユーザ情報一覧</h2>
        <table>
            <tr>
                <th>ユーザID</th>
                <th>登録日</th>
            </tr>
            <?php foreach($rows as $read) { ?>
            <tr>
                <td><?php print $read['user_name']; ?></td>
                <td><?php print $read['create_date']; ?></td>
            </tr>
            <?php } ?>
        </table>
    </body>
</html>