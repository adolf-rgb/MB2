<?php
include("db.php");
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("
    SELECT * 
    FROM companies 
    ORDER BY id DESC
");

$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Companies</title>

<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css"></head>

<body class="bg-light">

<div class="container py-4">

    <div class="mb-3">
        <a href="dashboard.php" class="btn btn-secondary">⬅ Back</a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Manage Companies</h2>

        <div>
            <a href="company-create.php" class="btn btn-success btn-sm">Add Company</a>
            <a href="companies-deactivated.php" class="btn btn-warning btn-sm">Deactivated</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover table-sm align-middle text-center">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Company</th>
                        <th>Address</th>
                        <th>Telephone</th>
                        <th>Email</th>
                        <th>Owner</th>
                        <th>Owner Mobile</th>
                        <th>Owner Email</th>
                        <th>Contact</th>
                        <th>Contact Mobile</th>
                        <th>Contact Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($companies as $row) { ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['company_name']; ?></td>
                        <td><?= $row['company_address']; ?></td>
                        <td><?= $row['company_telephone']; ?></td>
                        <td><?= $row['company_email']; ?></td>

                        <td><?= $row['owner_name']; ?></td>
                        <td><?= $row['owner_mobile']; ?></td>
                        <td><?= $row['owner_email']; ?></td>

                        <td><?= $row['contact_name']; ?></td>
                        <td><?= $row['contact_mobile']; ?></td>
                        <td><?= $row['contact_email']; ?></td>

                        <td>
                            <?php if ($row['is_deactivated']) { ?>
                                <span class="badge bg-danger">Deactivated</span>
                            <?php } else { ?>
                                <span class="badge bg-success">Active</span>
                            <?php } ?>
                        </td>

                        <td>
                            <a href="company-view.php?id=<?= $row['id']; ?>" class="btn btn-info btn-sm">View</a>
                            <a href="company-edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>

        </div>
    </div>

</div>

</body>
</html>