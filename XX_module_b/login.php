<?php
session_start();

if (isset($_POST['passphrase'])) {

    if ($_POST['passphrase'] == "admin") {
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit;
    }

    $error = "Wrong passphrase";
}
?>

<html>

<?php
$previous = $_SERVER['HTTP_REFERER'] ?? 'index.php';
?>

<link rel="stylesheet" href="css/style.css">

<body>

   
    <div class="container">

        <h2>Admin Login</h2>

         <?php if (isset($error)) { ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

        <form method="post">

            <input type="password" name="passphrase" placeholder="Passphrase">

            <button type="submit">Login</button>

        </form>

    </div>

</body>
</html>