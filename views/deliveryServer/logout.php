<?php
session_start();
session_unset();//problem becaue deconnect from the api session
// unset($_SESSION["subject"]);
header("location: index.php");
?>