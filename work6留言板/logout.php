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
    unset($_SESSION[$_COOKIE["certificate_id"]]);
    setcookie("certificate_id", "");
    header("Location:http://localhost:8080/myapp/login.php");
?>
     
</main>
</html>
