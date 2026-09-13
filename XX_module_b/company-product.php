<?php
include("db.php");
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

/* GET COMPANY FROM URL */
$company_id = $_GET['company_id'] ?? $_POST['company_id'] ?? null;

if(!$company_id){
    die("No company selected.");
}

/* VALIDATE COMPANY */
$stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ? AND is_deactivated = 0");
$stmt->execute([$company_id]);
$companyData = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$companyData){
    die("Invalid company.");
}

/* CREATE PRODUCT */
if(isset($_POST['create'])){

    $company = $company_id;
    $gtin    = trim($_POST['gtin'] ?? '');

    if(!preg_match('/^\d{13,14}$/', $gtin)) die("GTIN must be 13–14 digits.");

    $check = $pdo->prepare("SELECT 1 FROM products WHERE gtin = ?");
    $check->execute([$gtin]);

    if($check->fetch()) die("GTIN already exists.");

    /* IMAGE UPLOAD */
    $image = null;

    if(isset($_FILES['image']) && $_FILES['image']['name'] != ''){

        if($_FILES['image']['error'] !== 0){
            die("Upload error: " . $_FILES['image']['error']);
        }

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if(!in_array($ext, $allowed)){
            die("Invalid image format.");
        }

        if($_FILES['image']['size'] > 2 * 1024 * 1024){
            die("Image must be under 2MB.");
        }

        $uploadDir = __DIR__ . "/uploads/products/";

        if(!is_dir($uploadDir)){
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . '_' . uniqid() . '.' . $ext;
        $filePath = $uploadDir . $fileName;

        if(move_uploaded_file($_FILES['image']['tmp_name'], $filePath)){
            $image = "uploads/products/" . $fileName;
        } else {
            die("Failed to upload image.");
        }
    }

    /* INSERT DATA */
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
        $_POST['net_weight']   !== '' ? $_POST['net_weight']   : 0,
        $_POST['weight_unit'] ?: 'g',
        $image
    ];

    $stmt = $pdo->prepare("
        INSERT INTO products (
            company_id,
            gtin,
            name_en,
            name_fr,
            description_en,
            description_fr,
            brand,
            country_of_origin,
            gross_weight,
            net_weight,
            weight_unit,
            image_path,
            created_at,
            updated_at
        ) VALUES (
            ?,?,?,?,?,?,?,?,?,?,?,?,
            NOW(),
            NOW()
        )
    ");

    $stmt->execute($data);

    header("Location: company-view.php?id=" . $company_id);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="container">

    <h2>Add Product</h2>

    <form method="POST" enctype="multipart/form-data">

        <input type="hidden" name="company_id" value="<?= htmlspecialchars($company_id) ?>">

        <p>GTIN (13–14 digits)</p>
        <input
            type="text"
            name="gtin"
            maxlength="14"
            pattern="\d{13,14}"
            inputmode="numeric"
            oninput="this.value=this.value.replace(/\D/g,'').slice(0,14)"
            required
        >

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