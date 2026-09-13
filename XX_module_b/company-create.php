<?php
include("db.php");
session_start();

if (!isset($_SESSION['admin'])) {
    http_response_code(404);
    echo "404 Forbidden - Admins only";
    exit();
}

if (isset($_POST['create'])) {

    $sql = "
        INSERT INTO companies(
            company_name,
            company_address,
            company_telephone,
            company_email,
            owner_name,
            owner_mobile,
            owner_email,
            contact_name,
            contact_mobile,
            contact_email,
            created_at,
            updated_at
        ) VALUES (
            :company_name,
            :company_address,
            :company_telephone,
            :company_email,
            :owner_name,
            :owner_mobile,
            :owner_email,
            :contact_name,
            :contact_mobile,
            :contact_email,
            NOW(),
            NOW()
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        "company_name"       => $_POST['company_name'] ?? '',
        "company_address"    => $_POST['company_address'] ?? '',
        "company_telephone" => $_POST['company_telephone'] ?? '',
        "company_email"     => $_POST['company_email'] ?? '',
        "owner_name"        => $_POST['owner_name'] ?? '',
        "owner_mobile"      => $_POST['owner_mobile'] ?? '',
        "owner_email"       => $_POST['owner_email'] ?? '',
        "contact_name"      => $_POST['contact_name'] ?? '',
        "contact_mobile"    => $_POST['contact_mobile'] ?? '',
        "contact_email"     => $_POST['contact_email'] ?? ''
    ]);

    header("Location: companies.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Company</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="container">

    <a class="back" href="companies.php">← Back to Companies</a>

    <h2>Create Company</h2>

    <form method="POST">

        <label>Company Name</label>
        <input type="text" name="company_name" required>

        <label>Address</label>
        <input type="text" name="company_address">

        <label>Telephone</label>
        <input type="text" name="company_telephone">

        <label>Company Email</label>
        <input type="email" name="company_email">

        <h3>Owner Information</h3>

        <label>Owner Name</label>
        <input type="text" name="owner_name">

        <label>Owner Mobile</label>
        <input type="text" name="owner_mobile">

        <label>Owner Email</label>
        <input type="email" name="owner_email">

        <h3>Contact Person</h3>

        <label>Contact Name</label>
        <input type="text" name="contact_name">

        <label>Contact Mobile</label>
        <input type="text" name="contact_mobile">

        <label>Contact Email</label>
        <input type="email" name="contact_email">

        <br><br>

        <button type="submit" name="create">Create Company</button>

    </form>

</div>

</body>
</html>