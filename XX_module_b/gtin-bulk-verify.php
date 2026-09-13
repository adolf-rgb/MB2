<?php
include("db.php");

$results = [];

if(isset($_POST['validate'])){

    $lines = explode("\n", trim($_POST['gtins']));

    foreach($lines as $gtin){

        $gtin = trim($gtin);

        if($gtin == "") continue;

        $stmt = $pdo->prepare("
            SELECT id 
            FROM products   
            WHERE gtin = :gtin 
            AND is_hidden = 0
        ");
        $stmt->execute(["gtin" => $gtin]);

        if($stmt->rowCount() > 0){
            $results[$gtin] = "valid";
        }else{
            $results[$gtin] = "invalid";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Public GTIN Bulk Verification</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="topbar">

    <b>Public Products</b>

    <span style="float:right;">
        <a href="index.php">Back to Products</a>
        <a href="login">Admin Login</a>
    </span>

</div>

<div class="container">

    <h2>Public GTIN Bulk Verification</h2>

    <p>
        Paste multiple GTIN numbers (one per line) to check if they are registered and visible.
    </p>

    <form method="POST">

        <label>GTIN Numbers (one per line)</label>

        <textarea name="gtins" placeholder="e.g.

0123456789012
1234567890123
9876543210987
"><?php 
if(isset($_POST['gtins'])) echo $_POST['gtins']; 
?></textarea>

        <br>

        <button class="validate" name="validate">Submit</button>
        <button class="clear" type="reset">Clear</button>

    </form>

    <div class="result">

        <?php
        if(!empty($results)){

            echo "<h3>Results</h3>";

            foreach($results as $gtin => $status){

                if($status == "valid"){
                    echo "<p class='valid'>✔ $gtin</p>";
                }else{
                    echo "<p class='invalid'>✖ $gtin</p>";
                }

            }
        }
        ?>

    </div>

</div>

</body>
</html>