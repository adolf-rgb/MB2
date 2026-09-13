<?php 
session_start();

$error = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? '' 
];

$activeForm = $_SESSION['active_form'] ?? 'login';

session_unset();
function showError($error){
    return !empty($error) ? "<p class='error-message'>Error</p>" : '';

}

function isActiveForm($formName, $activeForm){
    return $formName === $activeForm ? 'active' : '';
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/animate.css">
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", serif;
        }
        body{
            display: flex;
            justify-content: center;
            align-items: center;
            color: antiquewhite;
            min-height: 100vh;
            background: url("assets/img/image-bg2.png");
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }

        .contain{
          
           
            margin: 0 15px;

        }

        .form-box{
            width: 100%;
            max-width: 450px;
            padding: 30px;
            border-radius: 30px;
            box-shadow: 0 0 10px rgb(0, 0, 0, 0.1);
              background-color: rgba(240, 248, 255, 0.123);
               backdrop-filter: blur(25px);
              
        }
       

        h2{
            font-size: 34px;
            text-align: center;
            margin-bottom: 20px;
        }

        input,select{
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: none;
            outline: none;
            font-size: 16px;
            color: #2b2a2a;
            margin-bottom: 20px;


        }

        button{
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: black;
            font-weight: 500;
            margin-bottom: 20px;
            transition: 0.2s ease;
            
        }

        button:hover{
            transform: scale(1.10);
        }

        p{
            font-size: 14.5px;
            text-align: center;
            margin-bottom: 10px;
        }

        p a{
            text-decoration: none;
            color: #52fcc3;
        } 

        .error-message{
            padding:12px;
            background: white;
            border-radius:6px;
            font-size:16px;
            color:red;
            text-align:center;
            margin-bottom:20px;
        }

        
        
    </style>
</head>
<body>

    <div class="contain animate__animated animate__fadeInTopLeft">
        <div class="form-box <?= isActiveForm('register', $activeForm);?>" id="signup-form">
    <form action="login_register.php" method="post">
        <h2>Sign-up</h2>
          <?= showError($error['register']); ?>
        <input type="email" name="email" placeholder="Put your Email Here!" required>
        <input type="password" name="password" placeholder="Put your Password Here!" required>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
    <button class="btn" type="submit" name="signup">Sign-in</button>
    <p>Do you have any account?<a href="login.php" > Log-in Here</a></p>
    </form>
    </div>
    </div>
    
</body>
</html>