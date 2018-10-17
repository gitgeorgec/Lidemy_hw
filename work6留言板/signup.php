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
    <h1>sign up</h1>
</header>

<form action="/myapp/signup.php" method="post">
    <input type="text" name="username" placeholder="username" required>
    <input type="text" name="nickname" placeholder="nickname" required>
    <input type="password" name="password" placeholder="password" required>
    <button type="submit">Sign UP</button>
</form>

<?php


if(isset($_POST["username"])){
    $username = $_POST["username"];
    $password = $_POST["password"];
    //password hash
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $nickname = $_POST["nickname"];
    $input = "SELECT * from `users` Where username = '$username'";
    $usernameList=$conn->query($input);
    if ($usernameList->num_rows > 0) {
        header("Location:http://localhost:8080/myapp/signup.php");
        return;
    }
    $stmt = $conn->prepare("INSERT INTO `users` (`username`, `password`, `nickname`) VALUES (?, ?, ?)");
    //store password as hash
    $stmt->bind_param("sss",$username, $hash, $nickname);
    $stmt->execute();

    $id = $conn->query("SELECT id FROM `users` WHERE username = '$username'")->fetch_assoc()['id'];
    //store login stage in session
    $sessionhash = password_hash($id, PASSWORD_DEFAULT);
    $_SESSION[$sessionhash]=$id;
    setcookie("certificate_id", $sessionhash, time()+3600);
    header("Location:http://localhost:8080/myapp/index.html");
} 
?>


</html>
