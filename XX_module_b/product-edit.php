<?php
include "db.php";
session_start();

if (!isset($_SESSION['admin'])) {
    http_response_code(401);
    exit("401 Forbidden - Admins only");
}

$id = $_GET['id'] ?? 0;
if (!$id) exit("Invalid product.");

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) exit("Product not found.");

$companies = $pdo->query("
    SELECT id, company_name FROM companies WHERE is_deactivated = 0
")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['update'])) {

    $gtin = trim($_POST['gtin'] ?? '');

    if (!preg_match('/^\d{13,14}$/', $gtin))
        exit("GTIN must be 13–14 digits.");

    $check = $pdo->prepare("
        SELECT 1 FROM products WHERE gtin = ? AND id != ?
    ");
    $check->execute([$gtin, $id]);

    if ($check->fetch())
        exit("GTIN already exists.");

    $image = $product['image_path'];

    /* Remove old image */
    if (isset($_POST['remove_image']) && $image) {
        $old = __DIR__ . "/" . ltrim($image, '/');
        if (file_exists($old)) unlink($old);
        $image = null;
    }

    /* Upload new image */
    if (!empty($_FILES['image']['name'])) {

        $file = $_FILES['image'];

        if ($file['error'])
            exit("Upload error: " . $file['error']);

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg','jpeg','png','gif','webp']))
            exit("Invalid image format.");

        if ($file['size'] > 2 * 1024 * 1024)
            exit("Image must be under 2MB.");

        $dir = __DIR__ . "/uploads/products/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        /* Delete old image */
        if ($product['image_path']) {
            $old = __DIR__ . "/" . ltrim($product['image_path'], '/');
            if (file_exists($old)) unlink($old);
        }

        $name = time() . "_" . uniqid() . "." . $ext;

        if (!move_uploaded_file($file['tmp_name'], $dir . $name))
            exit("Failed to upload image.");

        $image = "uploads/products/" . $name;
    }

    $sql = "UPDATE products SET
        company_id = ?,
        gtin = ?,
        name_en = ?,
        name_fr = ?,
        description_en = ?,
        description_fr = ?,
        brand = ?,
        country_of_origin = ?,
        gross_weight = ?,
        net_weight = ?,
        weight_unit = ?,
        image_path = ?,
        is_hidden = ?,
        updated_at = NOW()
        WHERE id = ?";

    $data = [
        $_POST['company_id'],
        $gtin,
        $_POST['name_en'] ?: 'N/A',
        $_POST['name_fr'] ?: 'N/A',
        $_POST['description_en'] ?: 'N/A',
        $_POST['description_fr'] ?: 'N/A',
        $_POST['brand'] ?: 'No Brand',
        $_POST['country_of_origin'] ?: 'Unknown',
        $_POST['gross_weight'] !== '' ? $_POST['gross_weight'] : 0,
        $_POST['net_weight'] !== '' ? $_POST['net_weight'] : 0,
        $_POST['weight_unit'] ?: 'g',
        $image,
        isset($_POST['is_hidden']) ? 1 : 0,
        $id
    ];

    $pdo->prepare($sql)->execute($data);

    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="container">
    <h2>Edit Product</h2>

    <form method="POST" enctype="multipart/form-data">

        <p>Company</p>
        <select name="company_id" required>
            <?php foreach ($companies as $c): ?>
                <option value="<?= $c['id'] ?>"
                    <?= $c['id'] == $product['company_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['company_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p>GTIN</p>
        <input type="text"
               name="gtin"
               value="<?= htmlspecialchars($product['gtin']) ?>"
               maxlength="14"
               pattern="\d{13,14}"
               inputmode="numeric"
               oninput="this.value=this.value.replace(/\D/g,'').slice(0,14)"
               required>

        <p>Name (EN)</p>
        <input type="text" name="name_en"
               value="<?= htmlspecialchars($product['name_en']) ?>">

        <p>Name (FR)</p>
        <input type="text" name="name_fr"
               value="<?= htmlspecialchars($product['name_fr']) ?>">

        <p>Description (EN)</p>
        <textarea name="description_en"><?= htmlspecialchars($product['description_en']) ?></textarea>

        <p>Description (FR)</p>
        <textarea name="description_fr"><?= htmlspecialchars($product['description_fr']) ?></textarea>

        <p>Brand</p>
        <input type="text" name="brand"
               value="<?= htmlspecialchars($product['brand']) ?>">

        <p>Country</p>
        <input type="text" name="country_of_origin"
               value="<?= htmlspecialchars($product['country_of_origin']) ?>">

        <p>Gross Weight</p>
        <input type="number" step="0.01" name="gross_weight"
               value="<?= htmlspecialchars($product['gross_weight']) ?>">

        <p>Net Weight</p>
        <input type="number" step="0.01" name="net_weight"
               value="<?= htmlspecialchars($product['net_weight']) ?>">

        <p>Unit</p>
        <input type="text" name="weight_unit"
               value="<?= htmlspecialchars($product['weight_unit']) ?>">

        <p>Current Image</p>

        <?php if ($product['image_path']): ?>
            <img src="<?= htmlspecialchars($product['image_path']) ?>"
                 width="120"
                 alt="Product Image">

            <p>
                <label>
                    <input type="checkbox" name="remove_image">
                    Remove Image
                </label>
            </p>
        <?php else: ?>
            <p>No image uploaded.</p>
        <?php endif; ?>

        <input type="file" name="image" accept="image/*">

        <p>
            <label>
                <input type="checkbox" name="is_hidden"
                    <?= $product['is_hidden'] ? 'checked' : '' ?>>
                Hidden
            </label>
        </p>

        <button type="submit" name="update">
            Update Product
        </button>

    </form>
</div>

</body>
</html>