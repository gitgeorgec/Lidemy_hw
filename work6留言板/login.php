<?session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>board</title>
</head>
<body>
<?php
    require('conn.php')    
?>
<header>
    <h1>Login Page</h1>
</header>
<main>
<form action="/myapp/login.php" method="GET">
    <input type="text" name="username" placeholder="username">
    <br>
    <input type="password" name="password" placeholder="password">
    <br>
    <input type="submit" value="Login">
</form>
<a href="/myapp/signup.php"><button>sign up</button></a>
<?php
    if(isset($_GET["username"])){
        $username = $_GET["username"];
        $password = $_GET["password"];
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        //chech if the account exist
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            //check password
            if(password_verify($password, $row['password'])){
                //random create a hash
                $sessionhash = password_hash($row['id'], PASSWORD_DEFAULT);
                //store login stage in session
                $_SESSION[$sessionhash]=$row['id'];
                setcookie("certificate_id", $sessionhash, time()+3600);
                header("Location:http://localhost:8080/myapp/index.html");
            }
            return;
        } else{
            $error_message = "wrong username or password";
            echo $error_message;
            echo $password;
        }   
    }
?>

       
</main>
</html>
