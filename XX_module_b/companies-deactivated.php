<?php
include("db.php");
session_start();

$stmt = $pdo->query("
    SELECT * 
    FROM companies 
    WHERE is_deactivated = 1 
    ORDER BY id DESC
");
$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

$previous = $_SERVER['HTTP_REFERER'] ?? 'index.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Deactivated Companies</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="container">

    <h2>Deactivated Companies</h2>

    <a href="companies.php">Back to Active Companies</a>

    <br><br>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>Company Name</th>
            <th>Telephone</th>
            <th>Email</th>
            <th>Owner</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach($companies as $row){ ?>

        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['company_name']; ?></td>
            <td><?php echo $row['company_telephone']; ?></td>
            <td><?php echo $row['company_email']; ?></td>
            <td><?php echo $row['owner_name']; ?></td>
            <td>Deactivated</td>

            <td>
                <a href="company-view.php?id=<?php echo $row['id']; ?>">View Details</a> |
                <a href="company-edit.php?id=<?php echo $row['id']; ?>">Edit</a>
            </td>
        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>