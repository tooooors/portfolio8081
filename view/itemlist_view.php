<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>商品一覧</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
 
        <link rel="stylesheet" href="./css/itemlist_view.css">
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
            <div class="recommend">
                <h1>おすすめ商品</h1>
                <div class="flex">
                    <div class="recommend_pic">
                        <img src="<?php print img_dir . $recommend_item['img']; ?>">    
                    </div>
                    <div class="recommend_info">
                        <form method="get" action="detail.php">
                            <input type="hidden" name="item_id" value="<?php print $recommend_item['item_id']; ?>">
                            <input type="submit" class="item_name" value="<?php print $recommend_item['item_name']; ?>"><br>
                        </form>
                        <p><?php print $recommend_item['price'] . '円'; ?></p>
                        <?php if ($recommend_item['stock'] === '0') { ?>
                        <p class="font_red">売り切れ</p>
                        <?php } else { ?>
                        <form method="post" action="cart.php">
                            <input type="hidden" name="process_kind" value="insert_cart">
                            <input type="hidden" name="amount" value=1>
                            <input type="hidden" name="item_id" value="<?php print $recommend_item['item_id']; ?>">
                            <input type="hidden" name="token" value="<?php print $token; ?>">
                            <input class="btn" type="submit" value="カートに追加">
                        </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="search">
                <div id="search1">
                    <div class="card">
                        <h1 class="card-header search_title" data-toggle="collapse" data-target="#collapse1">商品検索</h1>
                        <div id="collapse1" class="collapse" data-parent="#search1">
                            <div class="card-body">
                                <form method="post">
                                    <label for="keyword">キーワードで探す</label><br>
                                    <input type="hidden" name="process_kind" value="key_search">
                                    <input class="search_text" type="text" name="keyword" id="keyword" placeholder="商品名を入力してください" value="<?php print $keyword; ?>">
                                    <input type="hidden" name="token" value="<?php print $token; ?>">
                                    <input class="search_btn" type="submit" value="検索する">
                                </form>    
                            </div>
                        </div>
                    </div>
                </div>
                <div id="search2">
                    <div class="card">
                        <h1 class="card-header search_title" data-toggle="collapse" data-target="#collapse2">絞り込み検索</h1>
                        <div id="collapse2" class="collapse" data-parent="#search2">
                            <div class="card-body">
                                <form method="post">
                                    <div class="flex">
                                        <div class="search_type">
                                        <p>淹れ方</p>
                                        <label><input type="checkbox" name="type[]" value=1>インスタント</label><br>
                                        <label><input type="checkbox" name="type[]" value=2>ドリップ</label><br>
                                        <label><input type="checkbox" name="type[]" value=3>その他</label><br>
                                        </div>
                                        <div class="search_type">
                                            <p>価格</p>
                                            <label><input type="radio" name="price" value="0,300">～￥300</label><br>
                                            <label><input type="radio" name="price" value="301,500">￥301～￥500</label><br>
                                            <label><input type="radio" name="price" value="501,1000">￥501～￥1000</label><br>
                                            <label><input type="radio" name="price" value="1001,999999">￥1001～</label>
                                        </div>
                                        <div class="search_type">
                                            <p>内容量</p>
                                            <label><input type="radio" name="cups" value="0,5">～5杯</label><br>
                                            <label><input type="radio" name="cups" value="6,10">6杯～10杯</label><br>
                                            <label><input type="radio" name="cups" value="11,20">11杯～20杯</label><br>
                                            <label><input type="radio" name="cups" value="21,9999">21杯～</label><br>
                                        </div>   
                                    </div>
                                    <div class="text_right">
                                        <input type="hidden" name="process_kind" value="refined_search">
                                        <input type="hidden" name="token" value="<?php print $token; ?>">
                                        <input class="search_btn" type="submit" value="絞り込む">    
                                    </div>
                                </form>      
                             </div>
                         </div>
                    </div>
                </div>
            </div>
            <div class="search_results">
                <h2>検索結果: <?php print $item_num; ?>件</h2>
                <div class="itemlist">
                    <?php foreach($items as $read) { ?>
                    <div class="item">
                        <img src="<?php print img_dir . $read['img']; ?>"><br>
                        <form method="get" action="detail.php">
                            <input type="hidden" name="item_id" value="<?php print $read['item_id']; ?>">
                            <input type="submit" class="item_name" value="<?php print $read['item_name']; ?>"><br>
                        </form>
                        <p><?php print $read['price'] . '円'; ?></p>
                        <?php if ($read['stock'] === '0') { ?>
                        <p class="font_red">売り切れ</p>
                        <?php } else { ?>
                        <form method="post" action="cart.php">
                            <input type="hidden" name="process_kind" value="insert_cart">
                            <input type="hidden" name="amount" value=1>
                            <input type="hidden" name="item_id" value="<?php print $read['item_id']; ?>">
                            <input type="hidden" name="token" value="<?php print $token; ?>">
                            <input class="btn" type="submit" value="カートに追加">
                        </form>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </main>
        <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

    </body>
</html>