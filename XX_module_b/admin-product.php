<?php
require_once __DIR__ . '/db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    http_response_code(404);
    echo "404 Forbidden - Admins only";
    exit();
}

$gtin = $_GET['gtin'] ?? null;

if (!$gtin) {
    http_response_code(400);
    echo "Missing GTIN.";
    exit();
}

$lang = $_GET['lang'] ?? 'en';

$stmt = $pdo->prepare("
    SELECT products.*, companies.company_name
    FROM products
    INNER JOIN companies ON products.company_id = companies.id
    WHERE products.gtin = ?
    LIMIT 1
");
$stmt->execute([$gtin]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    http_response_code(404);
    echo "Product not found.";
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($lang == 'fr' ? $product['name_fr'] : $product['name_en']) ?></title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
        }

        .header {
            background: #1e1e2f;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .container {
            max-width: 1000px;
            width: 90%;
            margin: 20px auto;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .left {
            flex: 1 1 300px;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            aspect-ratio: 1/1;
        }

        .left img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .right {
            flex: 1 1 300px;
        }

        h1 {
            margin-top: 0;
        }

        .box-row {
            display: flex;
            gap: 10px;
            margin: 15px 0;
            flex-wrap: wrap;
        }

        .box {
            background: #f1f1f1;
            padding: 10px;
            border-radius: 8px;
            flex: 1 1 120px;
        }

        .tag {
            display: inline-block;
            background: #ddd;
            padding: 5px 10px;
            border-radius: 20px;
            margin: 5px 5px 0 0;
            font-size: 12px;
        }

        .hidden {
            color: red;
            font-weight: bold;
        }

        a {
            color: white;
            border: 1px solid white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .lang {
            text-align: right;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .lang a {
            color: #333;
            text-decoration: none;
        }

        .lang a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .container {
                width: 95%;
                margin: 15px auto;
            }

            .card {
                padding: 15px;
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>

<div class="header">
    <div>Admin Product View</div>
</div>

<div class="container">
    <div class="card">

        <div class="left">
            <img src="/xx_module_b/<?= htmlspecialchars($product['image_path']) ?>"
                 onerror="this.onerror=null; this.src='/xx_module_b/images/no-image.avif';">
        </div>

        <div class="right">

            <div class="lang">
                <a href="?gtin=<?= $gtin ?>&lang=en" <?= $lang=='en'?'style="font-weight:bold"':'' ?>>EN</a> /
                <a href="?gtin=<?= $gtin ?>&lang=fr" <?= $lang=='fr'?'style="font-weight:bold"':'' ?>>FR</a>
            </div>

            <h1>
                <?= htmlspecialchars($lang == 'fr' ? $product['name_fr'] : $product['name_en']) ?>
            </h1>

            <p>
                Company: <?= htmlspecialchars($product['company_name']) ?>
            </p>

            <p>
                <strong>GTIN</strong><br>
                <?= htmlspecialchars($product['gtin']) ?>
            </p>

            <p>
                <strong>Description</strong><br>
                <?= nl2br(htmlspecialchars(
                    $lang == 'fr' ? $product['description_fr'] : $product['description_en']
                )) ?>
            </p>

            <div class="box-row">
                <div class="box">
                    <strong>Gross Weight</strong><br>
                    <?= $product['gross_weight'] . ' ' . $product['weight_unit'] ?>
                </div>

                <div class="box">
                    <strong>Net Weight</strong><br>
                    <?= $product['net_weight'] . ' ' . $product['weight_unit'] ?>
                </div>
            </div>

            <div>
                <span class="tag">
                    Brand: <?= htmlspecialchars($product['brand']) ?>
                </span>

                <span class="tag">
                    Origin: <?= htmlspecialchars($product['country_of_origin']) ?>
                </span>
            </div>

            <?php if ($product['is_hidden']): ?>
                <p class="hidden">⚠ Hidden Product</p>
            <?php endif; ?>

        </div>

    </div>
</div>

</body>

</html>