<?php
include("db.php");
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$sql = "
    SELECT products.*, 
           companies.company_name, 
           companies.is_deactivated AS company_deactivated
    FROM products
    LEFT JOIN companies 
        ON products.company_id = companies.id
    ORDER BY products.id DESC
";

$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <link rel="stylesheet" href="css/style.css">
</head>
    
<body>

<a href="dashboard.php">
    <button>⬅ Back</button>
</a>

<div class="container">

    <h2>Manage Products</h2>

    <a href="products/new">Add Product</a>

    <br><br>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>GTIN</th>           
            <th>Name</th>
            <th>Company</th>
            <th>Brand</th>
            <th>Country</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach($products as $row){ ?>

        <tr>

            <td><?= $row['id']; ?></td>

            <td><?= $row['gtin']; ?></td>

            <td><?= $row['name_en']; ?></td>

            <td><?= $row['company_name']; ?></td>

            <td><?= $row['brand']; ?></td>

            <td><?= $row['country_of_origin']; ?></td>

            <td>
                <?php 
                if($row['is_hidden'] || $row['company_deactivated']){ 
                ?>
                    <span class="status-hidden">Hidden</span>
                <?php 
                }else{ 
                ?>
                    <span class="status-visible">Visible</span>
                <?php } ?>
            </td>

            <td class="actions">

                <a class="edit" href="product-edit.php?id=<?= $row['id']; ?>">Edit</a>

                <?php if($row['is_hidden'] || $row['company_deactivated']){ ?>
                    |
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