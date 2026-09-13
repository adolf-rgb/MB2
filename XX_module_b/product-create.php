<?php
include "db.php";
session_start();

if (!isset($_SESSION['admin'])) {
    http_response_code(401);
    exit("401 Forbidden - Admins only");
}

$companies = $pdo->query("
    SELECT id, company_name FROM companies WHERE is_deactivated = 0
")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['create'])) {
    $company = $_POST['company_id'] ?? '';
    $gtin = trim($_POST['gtin'] ?? '');

    if (!$company) exit("Select a company.");
    if (!preg_match('/^\d{13,14}$/', $gtin)) exit("GTIN must be 13–14 digits.");

    $check = $pdo->prepare("SELECT 1 FROM products WHERE gtin = ?");
    $check->execute([$gtin]);
    if ($check->fetch()) exit("GTIN already exists.");

    $image = null;

    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];

        if ($file['error']) exit("Upload error: " . $file['error']);

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','gif','webp']))
            exit("Invalid image format.");

        if ($file['size'] > 2 * 1024 * 1024)
            exit("Image must be under 2MB.");

        $dir = __DIR__ . "/uploads/products/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $name = time() . "_" . uniqid() . "." . $ext;

        if (!move_uploaded_file($file['tmp_name'], $dir . $name))
            exit("Failed to upload image.");

        $image = "uploads/products/" . $name;
    }

    $data = [
        $company,
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
        $image
    ];

    $sql = "INSERT INTO products
        (company_id, gtin, name_en, name_fr, description_en, description_fr,
        brand, country_of_origin, gross_weight, net_weight, weight_unit,
        image_path, created_at, updated_at)
        VALUES (?,?,?,?,?,?,?,?,?,?,?, ?,NOW(),NOW())";

    $pdo->prepare($sql)->execute($data);

    header("Location: /xx_module_b/products.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <h2>Add Product</h2>

    <form method="POST" enctype="multipart/form-data">

        <p>Company</p>
        <select name="company_id" required>
            <option value="">Select Company</option>

            <?php foreach ($companies as $c): ?>
                <option value="<?= $c['id'] ?>">
                    <?= htmlspecialchars($c['company_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p>GTIN (13–14 digits)</p>
        <input type="text"
               name="gtin"
               maxlength="14"
               pattern="\d{13,14}"
               inputmode="numeric"
               oninput="this.value=this.value.replace(/\D/g,'').slice(0,14)"
               required>

        <p>Name (EN)</p>
        <input type="text" name="name_en">

        <p>Name (FR)</p>
        <input type="text" name="name_fr">

        <p>Description (EN)</p>
        <textarea name="description_en"></textarea>

        <p>Description (FR)</p>
        <textarea name="description_fr"></textarea>

        <p>Brand</p>
        <input type="text" name="brand">

        <p>Country</p>
        <input type="text" name="country_of_origin">

        <p>Gross Weight</p>
        <input type="number" step="0.01" name="gross_weight">

        <p>Net Weight</p>
        <input type="number" step="0.01" name="net_weight">

        <p>Unit</p>
        <input type="text" name="weight_unit" value="g">

        <p>Image</p>
        <input type="file" name="image" accept="image/*">

        <br><br>
        <button type="submit" name="create">Create Product</button>

    </form>
</div>

</body>
</html>