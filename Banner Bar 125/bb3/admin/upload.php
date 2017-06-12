<?php
session_start();
require_once ('inc/check.php');
require_once('inc/connect.ini.php');
require_once('inc/functions.ini.php');



if ((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
    //Check if the file is JPEG image and it's size is less than 350Kb
    $filename = basename($_FILES['image']['name']);


$allowedExts = array("jpg", "jpeg", "gif", "png");
$extension = end(explode(".", $_FILES["image"]["name"]));
if ((($_FILES["image"]["type"] == "image/gif")
|| ($_FILES["image"]["type"] == "image/jpeg")
|| ($_FILES["image"]["type"] == "image/png")
|| ($_FILES["image"]["type"] == "image/pjpeg"))
&& ($_FILES["image"]["size"] < 350000)
&& in_array($extension, $allowedExts))
{





        //Determine the path to which we want to save this file
        $URL = $website.'/'.$location.'/admin/upload/' . $filename;
        $newname = dirname(__FILE__) . '/upload/' . $filename;
        //Check if the file with the same name is already exists on the server
        if (!file_exists($newname)) {
            //Attempt to move the uploaded file to it's new place
            if ((move_uploaded_file($_FILES['image']['tmp_name'], $newname))) {
                if (isset($_SESSION['upload']))
                    unset($_SESSION['upload']);
                $_SESSION['upload'] = $URL;
                header("Location: new.php");
                // echo "It's done! The file has been saved as: ".$URL;
            } else {
                echo "<script>alert('Error: A problem occurred during file upload!');</script>";
                echo "";
                echo '<input type="button" value="Go Back" onclick="window.history.back()" />';
            }
        } else {
            if (isset($_SESSION['alert']))
                unset($_SESSION['alert']);
            $msg = "Error: File " . $_FILES["image"]["name"] . " already exists";
            $_SESSION['alert'] = $msg;
            header("Location: new.php");
            //echo '<input type="button" value="Go Back" onclick="window.history.back()" />';
        }
    } else {
        if (isset($_SESSION['alert']))
            unset($_SESSION['alert']);
        $msg = "Error: Only .jpg, .jpeg or .gif images under 350Kb are accepted for upload";
        $_SESSION['alert'] = $msg;
        header("Location: new.php");
    }
} else {
    //
    if (isset($_SESSION['alert']))
        unset($_SESSION['alert']);
    $msg = "Error: No file uploaded";
    $_SESSION['alert'] = $msg;
    header("Location: new.php");
}
?>