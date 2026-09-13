<?php
include("db.php");
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid company.");
}

$stmt = $pdo->prepare("
    SELECT * 
    FROM companies 
    WHERE id = :id
");
$stmt->execute(["id" => $id]);

$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    die("Company not found.");
}

$sql = "
    SELECT products.*, companies.company_name, companies.is_deactivated AS company_deactivated
    FROM products
    LEFT JOIN companies ON products.company_id = companies.id
    WHERE products.company_id = :id
    ORDER BY products.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Company Details</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<a href="companies.php">
    <button>⬅ Back</button>
</a>

<div class="container">

    <h2>Company Details</h2>

    <p><b>Company Name:</b> <?= $company['company_name']; ?></p>
    <p><b>Address:</b> <?= $company['company_address']; ?></p>
    <p><b>Telephone:</b> <?= $company['company_telephone']; ?></p>
    <p><b>Email:</b> <?= $company['company_email']; ?></p>

    <hr>

    <h3>Owner Information</h3>

    <p><b>Owner Name:</b> <?= $company['owner_name']; ?></p>
    <p><b>Owner Mobile:</b> <?= $company['owner_mobile']; ?></p>
    <p><b>Owner Email:</b> <?= $company['owner_email']; ?></p>

    <hr>

    <h3>Contact Person</h3>

    <p><b>Contact Name:</b> <?= $company['contact_name']; ?></p>
    <p><b>Contact Mobile:</b> <?= $company['contact_mobile']; ?></p>
    <p><b>Contact Email:</b> <?= $company['contact_email']; ?></p>

    <hr>

    <p>
        <b>Status:</b>
        <?= $company['is_deactivated'] ? "Deactivated" : "Active"; ?>
    </p>

    <br>

    <a href="companies.php">← Back to Companies</a>

</div>

<div class="container">

    <h2>Manage Products</h2>

    <a href="company-product.php?company_id=<?= $id; ?>">
        Add Product
    </a>

    <br><br>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>GTIN</th>   
            <th>Name</th>
            <th>Company</th>
            <th>Brand</th>
            <th>Country</th>
            <th>Weight</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach ($products as $row) { ?>

            <tr>

                <td><?= $row['id']; ?></td>
                <td><?= $row['gtin']; ?></td>
                <td><?= $row['name_en']; ?></td>
                <td><?= $row['company_name']; ?></td>
                <td><?= $row['brand']; ?></td>
                <td><?= $row['country_of_origin']; ?></td>

                <td>
                    <?= $row['net_weight'] . " " . $row['weight_unit']; ?>
                </td>

                <td>
                    <?php if ($row['is_hidden'] || $company['is_deactivated']) { ?>
                        <span class="status-hidden">Hidden</span>
                    <?php } else { ?>
                        <span class="status-visible">Visible</span>
                    <?php } ?>
                </td>

                <td class="actions">
                    <a class="edit" href="product-edit.php?id=<?= $row['id']; ?>">Edit</a> |
                    <?php if($row['is_hidden'] || $row['company_deactivated']) { ?>
                    <a class="delete"
                       href="product-delete.php?id=<?= $row['id']; ?>"
                       onclick="return confirm('Delete product?')">
                        Delete
                    </a>
                    <?php } ?>
                </td>

            </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>