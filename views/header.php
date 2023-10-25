<?php    
    session_start();
    if(!isset($_SESSION["name"])) {
        header("Location: ../index.php");
    }
?>

<div class="header">
    <span class="title">
        <i class="fa-solid fa-globe"></i> Social Network
    </span>
    <img src="../fotos/<?= $_SESSION["picture"] ?>" class="avatar" />    
    <span style="float: right; margin-top: 10px; margin-right: 20px; font-size: 18px;">
        <?= $_SESSION["name"] ?>
    </span>
</div>