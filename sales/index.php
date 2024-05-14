<?php
// データベースに接続
require_once "../db.php";

function periodDate($year, $month)
{
    $type = ($month > 0) ? '+1 month' : '+1 year';
    if (!$month) $month = 1;

    $from_at = new DateTime("{$year}-{$month}-01");
    $to_at = new DateTime("{$year}-{$month}-01");
    $to_at->modify($type);

    $period['from_at'] = $from_at->format('Y-m-d');
    $period['to_at'] = $to_at->format('Y-m-d');

    return $period;
}

function salesList($pdo, $year, $month)
{
    if ($year > 0) {
        $period = periodDate($year, $month);
        $sql = "SELECT * FROM sales 
            WHERE created_at >= '{$period['from_at']}'
            AND created_at < '{$period['to_at']}'
            ORDER BY created_at DESC;";
    } else {
        // 売上一覧をすべて取得
        $sql = "SELECT * FROM sales ORDER BY created_at DESC;";
    }
    // SQLを実行
    $stmt = $pdo->query($sql);

    // PHP配列に変換
    $sales = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sales[] = $row;
    }
    return $sales;
}

function selected($value, $target)
{
    if ($value == $target) return "selected";
}

$current_year = $_GET['year'] ?? 0;
$current_month = $_GET['month'] ?? 0;

// 売上一覧
$sales = salesList($pdo, $current_year, $current_month);

// 総売上金額
$total_sales = array_sum(array_column($sales, 'price'));

// 年月プルダウンデータ
$years = range(2023, date('Y'));
$months = range(1, 12);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YSEレジ</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <h2 class="text-2xl text-center">
        <?php if ($current_year) : ?><?= $current_year ?><?php endif ?>
        <?php if ($current_month) : ?>/<?= $current_month ?><?php endif ?>
        売上
    </h2>

    <div class="p-3 text-center">
        <form action="" method="get">
            <select name="year">
                <option value="">----</option>
                <?php foreach ($years as $year) : ?>
                    <option value="<?= $year ?>" <?= selected($year, $current_year) ?>><?= $year ?></option>
                <?php endforeach ?>
            </select>
            <select name="month">
                <option value="">----</option>
                <?php foreach ($months as $month) : ?>
                    <option value="<?= $month ?>" <?= selected($month, $current_month) ?>><?= $month ?></option>
                <?php endforeach ?>
            </select>
            <button class="border px-3 py-2">検索</button>
            <a class="border px-3 py-2" href="./">クリア</a>
            <a class="border px-3 py-2" href="../">戻る</a>
        </form>
    </div>
    <h3 class="text-xl text-center p-3">
        <label for="">総売上</label>
        <span><?= number_format($total_sales) ?></span>
    </h3>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">売上日時</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">売上高</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if ($sales) : ?>
                <?php foreach ($sales as $sale) : ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $sale['created_at'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= number_format($sale['price']) ?></td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>

</body>

</html>