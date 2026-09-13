<?php
include("db.php");
session_start();

$lang = $_GET['lang'] ?? 'en';
$lang = ($lang === 'fr') ? 'fr' : 'en';

if (isset($_GET['gtin'])) {

    if (!isset($_SESSION['admin'])) {
        http_response_code(404);
        exit("Product not found.");
    }

    $stmt = $pdo->prepare("
        SELECT p.*, c.company_name
        FROM products p
        JOIN companies c ON p.company_id = c.id
        WHERE p.gtin = ?
    ");
    $stmt->execute([$_GET['gtin']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        http_response_code(404);
        exit("Product not found.");
    }

    $products = [$product];

} else {

    $products = $pdo->query("
        SELECT p.*, c.company_name
        FROM products p
        JOIN companies c ON p.company_id = c.id
        WHERE p.is_hidden = 0
          AND c.is_deactivated = 0
        ORDER BY p.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Public Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body{
            font-family:Arial, sans-serif;
            background:#f3f4f6;
            margin:0;
        }

        .topbar{
            background:#1f2937;
            color:#fff;
            padding:15px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:10px;
        }

        .topbar a{
            color:#fff;
            border:1px solid #fff;
            padding:5px 10px;
            border-radius:4px;
            margin-left:5px;
            text-decoration:none;
            display:inline-block;
        }

        .topbar a:hover{
            background:rgba(255,255,255,0.12);
        }

        .container{
            max-width:1100px;
            width:90%;
            margin:40px auto;
        }

        .grid{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));
            gap:20px;
        }

        .card{
            background:#fff;
            border:1px solid #d1d5db;
            padding:15px;
            text-align:center;
            border-radius:8px;
            box-shadow:0 2px 8px rgba(0,0,0,0.05);
        }

        .card img{
            width:100%;
            aspect-ratio:1 / 1;
            object-fit:contain;
            border:1px solid #ccc;
            margin-bottom:10px;
            background:#fff;
            border-radius:6px;
        }

        .company{
            font-weight:bold;
            font-size:16px;
            margin-bottom:10px;
        }

        .lang{
            text-align:right;
            font-size:12px;
            margin-bottom:10px;
        }

        .lang a{
            color:#2563eb;
            text-decoration:none;
        }

        .lang a:hover{
            text-decoration:underline;
        }

        .gtin{
            font-weight:bold;
            margin-top:5px;
        }

        .name_en,
        .desc,
        .weight{
            margin-top:10px;
            font-size:14px;
            color:#111827;
        }

        h2{
            text-align:center;
            margin-bottom:30px;
            color:#111827;
        }

        .empty{
            text-align:center;
            padding:40px 20px;
            background:#fff;
            border:1px solid #d1d5db;
            border-radius:8px;
            color:#6b7280;
        }
    </style>
</head>
<body>

<div class="topbar">
    <b>Public Products</b>

    <span>
        <a href="gtin-bulk-verify.php">Bulk GTIN</a>
        <a href="login">Admin</a>
    </span>
</div>

<div class="container">

    <h2><?= isset($_GET['gtin']) ? 'Product Details' : 'All Products' ?></h2>

    <?php if (!empty($products)) { ?>
        <div class="grid">

            <?php foreach ($products as $p) { ?>
                <?php
                    $image = 'images/no-image.avif';

                    if (!empty($p['image_path'])) {
                        $candidatePath = __DIR__ . '/' . ltrim($p['image_path'], '/');
                        if (file_exists($candidatePath)) {
                            $image = ltrim($p['image_path'], '/');
                        }
                    }
                ?>

                <div class="card">

                    <div class="lang">
                        <a href="?lang=en<?= isset($_GET['gtin']) ? '&gtin=' . urlencode($p['gtin']) : '' ?>">EN</a> /
                        <a href="?lang=fr<?= isset($_GET['gtin']) ? '&gtin=' . urlencode($p['gtin']) : '' ?>">FR</a>
                    </div>

                    <div class="company">
                        <?= htmlspecialchars($p['company_name']) ?>
                    </div>

                    <img src="<?= htmlspecialchars($image) ?>"
                         alt="product"
                         onerror="this.onerror=null;this.src='images/no-image.avif';">

                    <div class="gtin">
                        GTIN: <?= htmlspecialchars($p['gtin']) ?>
                    </div>

                    <div class="name_en">
                        Name: <?= htmlspecialchars($p['name_en']) ?>
                    </div>

                    <div class="desc">
                        <?= htmlspecialchars($lang === 'fr' ? ($p['description_fr'] ?? '') : ($p['description_en'] ?? '')) ?>
                    </div>

                    <div class="weight">
                        Weight: <?= htmlspecialchars($p['gross_weight']) . " " . htmlspecialchars($p['weight_unit']) ?><br>
                        Net: <?= htmlspecialchars($p['net_weight']) . " " . htmlspecialchars($p['weight_unit']) ?>
                    </div>

                </div>
            <?php } ?>

        </div>
    <?php } else { ?>
        <div class="empty">No products found.</div>
    <?php } ?>

</div>

</body>
</html>