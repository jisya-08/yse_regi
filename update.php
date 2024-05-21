<?php
// データベースに接続
require_once "db.php";

try {
    // POSTデータを受け取る
    $posts = $_POST;

    // バリデーション：priceが存在し、かつ数値かどうか確認する
    if (isset($posts['price']) && is_numeric($posts['price'])) {
        $price = $posts['price'];

        // 合計金額「sales.price」を保存するSQLを作成
        $sql = "INSERT INTO sales (price) VALUES (:price)";
        
        // SQLを準備
        $stmt = $pdo->prepare($sql);
        
        // 値をバインド
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        
        // SQLを実行
        $stmt->execute();
    } else {
        throw new Exception(' 価格が不正です。数値を入力してください。');
    }

    // レジ画面に戻る（リダイレクト：ページ転送）
    header('Location: index.php');
    exit; // スクリプトの実行を停止
} catch (Exception $e) {
    // エラーメッセージを表示（またはログに記録）
    echo 'エラーが発生しました: ' . $e->getMessage();
}
?>