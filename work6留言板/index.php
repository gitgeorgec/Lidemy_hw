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

<?php
    if(isset($_POST["message"])){
        $username = $_POST["username"];
        $message = $_POST["message"];
        $stmt = $conn->prepare("INSERT INTO `messages` (`username`, `message`) VALUES(?,?)");
        $stmt->bind_param("ss", $username, $message);
        $stmt->execute();
        header("Location:http://localhost:8080/myapp/index.php");
    }
    
    if(isset($_POST["id"])){
        $message = $_POST["submessage"];
        $username = $_POST["username"];
        $id =$_POST["id"];
        $stmt = $conn->prepare("INSERT INTO `messages` (`username`, `message`, `SubType`, `belongTo`) VALUES (?, ?, '1', '$id')");
        $stmt->bind_param("ss", $username, $message);
        $stmt->execute();
        header("Location:http://localhost:8080/myapp/index.php");
    } 

    if(isset($_GET["delete"])){
        $deleteId = $_GET["delete"];
        $stmt = $conn->prepare("DELETE FROM `messages` WHERE `id`=?");
        $stmt->bind_param("s", $deleteId);
        $stmt->execute();
    }

    if(isset($_GET["update"])){
        $updateId = $_GET["id"];
        $message = $_GET["update"];
        $stmt = $conn->prepare("UPDATE `messages` SET `message` = ? WHERE `messages`.`id` = ?");
        $stmt->bind_param("ss", $message, $updateId);
        $stmt->execute();
    }
?>

<?
    $notLogin = true;
    if(isset($_SESSION[$_COOKIE["certificate_id"]])){
            $id_info = $_SESSION[$_COOKIE["certificate_id"]];
            $notLogin = false;
    }

    if($notLogin) {
        echo "<p>" ."not login" . "</p>";
?>
    <button><a href="http://localhost:8080/myapp/login.php">login</a></button>
    <button><a href="http://localhost:8080/myapp/signup.php">signup</a></button>
<?
    } else {
        $cookieId = $id_info;
        $stmt =$conn->prepare("SELECT * From users WHERE id = ?");
        $stmt->bind_param("s", $cookieId);
        $stmt->execute();
        $result = $stmt->get_result();
        $loginUser = $result->fetch_assoc();
        echo "<p>" . "username: " . $loginUser["username"] . "</p>";
?>
<button><a href="http://localhost:8080/myapp/logout.php">logout</a></button>
    <header>
        <h1>Message board</h1>
    </header>
    <main>
    <form action="/myapp/index.php" class="message" method="POST" autocomplete="off">
        <div class="user-info">
<?
    echo "<p>" . $loginUser["username"] . "</p>";
    echo "<input type='text' name=username value=" . $loginUser["username"]  . " required style='display:none'>";
?>
        </div>
        <textarea name="message" id="" cols="60" rows="10" required></textarea>
        <button type="submit" class="submitbtn">Leave message</button>
    </form>
<?
    //change page
    $count = $conn->query("SELECT count(*) From `messages` WHERE SubType = 0")->fetch_assoc();
    $maxpage = (floor($count['count(*)']/10)+1);
?>
    <form action='/myapp/index.php' method='GET'>
<?
        echo "<input type='number' name='page' value=1 min=1 max=$maxpage>"
?>
        <button type='submit'>go to </button>
    </form>
        
    <div class="messageList">
<?
    //get message from data base
    if(!isset($_GET['page'])){
        $_GET['page']=1;
    }
    if(isset($_GET['page'])){
        $pagestart = ($_GET['page']-1) * 10;
        $pageend = $pagestart + 10;
        $seletMessages = "SELECT * FROM `messages` Where SubType = 0 order by id DESC LIMIT $pagestart,$pageend";
        $MessageList = $conn->query($seletMessages);
        $floor = $pagestart + 1;
        if ($MessageList->num_rows > 0) {
            // output messages data of each row
            while($row = $MessageList->fetch_assoc()) {
            echo "<div class='message'>" . "<span>" .$floor ."</span>";
            $floor++;
            $id = $row["id"];
            if($row['username']===$loginUser['username']){
                echo "<div class='message'>";
                    echo "<div>"
                        .$row["username"] 
                        ."<br>" 
                        .$row["date"] 
                    ."</div>";
                    echo "<div class='user_message'>" 
                        . htmlspecialchars($row["message"])
                        . "<br> <button class='editbtn'>edit</button>"
                        ."</div>";
                    echo "<div class='user_message close'>" .
                        "<form action='index.php' method='GET' edit'>
                            <input type='text' name='id' value=$id hidden>
                            <textarea name='update' style='width:100%; height:100%;'>". $row["message"] . "</textarea>
                            <br> <button type='submit' class='editbtn'>edit</button>
                        </form>
                        </div>";
                    //delete button
                    echo "<form action='index.php' method='GET' style='display:inline'>" 
                        . "<input type='text' name='delete' value=$id hidden>" 
                        . "<button type='submit' class='deletebtn'>delete</button>" 
                        . "</form>";
                echo "</div>";
            }else{
                echo "<div class='message'>";
                echo "<div>" . $row["username"] ."<br>" . $row["date"] . "</div>";
                echo "<div>" . htmlspecialchars($row["message"]) . "</div>";
                echo "</div>";
            }                
                //output submessages data of each row
                $subMessageId = "SELECT * FROM `messages` Where belongTo = '$id'";
                $subMessage = $conn->query($subMessageId);
                echo "<div class='subcomment'>";
                    while($subrow = $subMessage->fetch_assoc()){
                        if($subrow['username']===$loginUser['username']){
                            if($subrow['username']===$row['username']){
                                echo "<div class='message onwer_response'>";
                            }else{
                                echo "<div class='message'>";
                            }
                            echo "<div>" 
                                    .$subrow["username"] 
                                    ."<br>" 
                                    .$subrow["date"] 
                                ."</div>";
                                echo "<div class='user_message'>" 
                                    . htmlspecialchars($subrow["message"])
                                    . "<br> <button class='editbtn'>edit</button>"
                                    ."</div>";;
                                echo "<div class='user_message close'>" .
                                    "<form action='index.php' method='GET' edit'>
                                        <input type='text' name='id' value=$id hidden>
                                        <textarea name='update' style='width:100%; height:100%;'>". $subrow["message"] . "</textarea>
                                        <br> <button type='submit' class='editbtn'>edit</button>
                                    </form>
                                    </div>";
                                //delete button
                                echo "<form action='index.php' method='GET' style='display:inline'>" 
                                    . "<input type='text' name='delete' value=$id hidden>" 
                                    . "<button type='submit' class='deletebtn'>delete</button>" 
                                    . "</form>";
                            echo "</div>";
                        }else{
                            if($subrow['username']===$row['username']){
                                echo "<div class='message onwer_response'>";
                            }else{
                                echo "<div class='message'>";
                            }
                            echo "<div>" . $subrow["username"] ."<br>" . $subrow["date"] . "</div>";
                            echo "<div>" . htmlspecialchars($subrow["message"]) . "</div>";
                            echo "</div>";
                        }
                    }
                echo "</div>"; 
                //response 
                    echo "
                    <button class='responbtn'>response</button>
                    <form action='/myapp/index.php' class='message input close' method='POST'>"
                    . $loginUser["username"] . " response
                        <input type='text' name='id' value=$id style='display:none'>
                        <div class='user-info'>
                            <input type='text' name=username value=" . $loginUser["username"]  . " style='display:none' required>
                        </div>
                        <textarea name='submessage' id='' cols='30' rows='2' required></textarea>
                        <button type='submit' class='submitbtn'>Leave comment</button>
                    </form>
                    ";
            echo "</div>";
            }
        }
    
    }
}
?>

</div>            
 
 <script>
const responbtns = document.querySelectorAll(".responbtn")
const subMessages = document.querySelectorAll(".message")
const editBtns = document.querySelectorAll(".user_message>.editbtn")
const editForms = document.querySelectorAll(".edit")

function handleEdit(){
    const editForms = this.parentElement.parentElement.querySelectorAll(".user_message")
    editForms.forEach(form=>form.classList.toggle("close"))
}

function handleresponse(e){
    const message = this.parentElement.querySelector(".input")
    message.classList.toggle("close")
}

function preventBubble(e){
    e.stopPropagation()
}

editBtns.forEach(editBtn=>editBtn.addEventListener("click", handleEdit))

responbtns.forEach(responbtn=>responbtn.addEventListener("click", handleresponse))
subMessages.forEach(message=>message.addEventListener("click", preventBubble))
editForms.forEach(form=>form.addEventListener("click", preventBubble))

 </script>
</main>
</html>
