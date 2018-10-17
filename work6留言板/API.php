<?session_start();?>
<?php
    $notLogin = true;
    if(isset($_COOKIE["certificate_id"])){
        if(isset($_SESSION[$_COOKIE["certificate_id"]])){
                $id_info = $_SESSION[$_COOKIE["certificate_id"]];
                $notLogin = false;
        }
    }

require('conn.php');
$messagelist =[];
if($notLogin){
    array_push($messagelist, "not login");
    echo    json_encode($messagelist); //输出 "not login" JSON
}else{
    //GET LOGIN USER
    if(isset($_GET["id"])){
        $cookieId = $id_info;
        $stmt =$conn->prepare("SELECT * From users WHERE id = ?");
        $stmt->bind_param("s", $cookieId);
        $stmt->execute();
        $result = $stmt->get_result();
        $loginUser = $result->fetch_assoc();
        echo json_encode($loginUser);
    }

    //GET ALL MESSAGE DATA
    if(isset($_GET["Message"])){
        $seletMessages = "SELECT * FROM `messages` order by `date` DESC";
        $MessageList = $conn->query($seletMessages);
        if ($MessageList->num_rows > 0) {
            // output messages data of each row
            $messagelist =[];
            while($row = $MessageList->fetch_assoc()) {
                $item =[
                    "id"=>$row['id'],
                    "username"=>$row['username'],
                    "message"=>htmlspecialchars($row["message"]),
                    "SubType"=>$row['SubType'],
                    "date"=>$row['date'],
                    "belongTo"=>$row['belongTo']
                ];
                array_push($messagelist, $item);
            }
            echo json_encode($messagelist); 
        }
    }
    /*    以下均未进行注入过滤，自行修改    */
    // $option = $_GET['option']; //操作
    // if($option == 'return'){
    //     echo    json_encode($messagelist); //输出JSON
    // } //继续其他操作
    if(isset($_GET["create"])){
        $username = $_GET["username"];
        $message = $_GET["message"];
        $stmt = $conn->prepare("INSERT INTO `messages` (`username`, `message`) VALUES(?,?)");
        $stmt->bind_param("ss", $username, $message);
        $stmt->execute();
        echo "success Post";
    }

    if(isset($_POST["submessage"])){
        $message = $_POST["submessage"];
        $username = $_POST["username"];
        $id =$_POST["id"];
        $stmt = $conn->prepare("INSERT INTO `messages` (`username`, `message`, `SubType`, `belongTo`) VALUES (?, ?, '1', '$id')");
        $stmt->bind_param("ss", $username, $message);
        $stmt->execute();
        echo "success Post";
    }

    if(isset($_GET["delete"])){
        $deleteId = $_GET["delete"];
        $stmt = $conn->prepare("DELETE FROM `messages` WHERE `id`=?");
        $stmt->bind_param("s", $deleteId);
        $stmt->execute();
    }

    if(isset($_GET["update"])){
        $updateId = $_GET["updateId"];
        $message = $_GET["update"];
        $stmt = $conn->prepare("UPDATE `messages` SET `message` = ? WHERE `messages`.`id` = ?");
        $stmt->bind_param("ss", $message, $updateId);
        $stmt->execute();
    }
    if(isset($_GET["page"])){
        $count = $conn->query("SELECT count(*) From `messages` WHERE SubType = 0")->fetch_assoc();
        $maxpage = (floor($count['count(*)']/10)+1);
        echo json_encode($maxpage); 
    }

}
?>
