<?php

    header("Content-Type: application/json; charset=utf-8");
    require_once("../dao/UserDAO.php");

    $query = filter_input(INPUT_POST, "query");
    
    if($query === "login") {
        $email = filter_input(INPUT_POST, trim("email"));
        $userpassword = filter_input(INPUT_POST, "userpassword");
        $csrf_token = filter_input(INPUT_POST, "csrf_token");        
        $userDAO = new UserDAO();
        $user = $userDAO->login($email, $userpassword);
        if($user != null && $user->getId() != null) {
            if(!isset($_SESSION)) {
                session_start();                
            }            
            if($csrf_token == $_SESSION['csrf_token']) {
                $_SESSION["id"] = $user->getId();                
                $_SESSION["name"] = $user->getUsername();
                $_SESSION["picture"] = $user->getPicture();
                echo json_encode($user);
            }
            else {
                echo json_encode(null);        
            }
        }
        else {
            echo json_encode(null);
        }    
    }
    if($query === "register") {
        $username = filter_input(INPUT_POST, trim("username"));
        $email = filter_input(INPUT_POST, trim("email"));
        $userpassword = filter_input(INPUT_POST, trim("userpassword"));
        $csrf_token = filter_input(INPUT_POST, "csrf_token");
        $userDAO = new UserDAO();
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setUserpassword(password_hash($userpassword, PASSWORD_DEFAULT));
        echo json_encode($userDAO->register($user));        
    }
    if($query === "updatePicture") {
        if(isset($_FILES["picture"])) {
            session_start();
            $id = $_SESSION["id"];
            $picture = $_FILES["picture"];
            $userDAO = new UserDAO();
            $user = new User();
            if($picture != null) {
                if(in_array($picture['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
                    $currentPicture = $userDAO->getPicture($id);
                    if($currentPicture !== "default.jpg") {
                        unlink("../fotos/".$fotoAtual);
                    }
                    $newPictureName = md5('picture'.md5(time().rand(0, 100000))).'.jpg';
                    $user->setId($id);
                    $user->setPicture($newPictureName);
                    move_uploaded_file($_FILES['picture']['tmp_name'], "../fotos/".$newPictureName);
                    $_SESSION["picture"] = $user->getPicture();
                    echo json_encode($userDAO->updatePicture($user));
                }
            }
        }
    }
    if($query === "getProfile") {
        session_start();
        $id = $_SESSION["id"];
        $userDAO = new UserDAO();
        echo json_encode($userDAO->getProfile($id));
    }
    if($query === "updateProfile") {
        session_start();
        $id = $_SESSION["id"];
        $about = filter_input(INPUT_POST, trim("about"));
        $userDAO = new UserDAO();
        $user = new User();
        $user->setId($id);   
        $user->setAbout($about);
        echo json_encode($userDAO->updateProfile($user));
    }
    if($query === "search") {
        session_start();
        $id = $_SESSION["id"];
        $search = filter_input(INPUT_POST, trim("search"));
        $userDAO = new UserDAO();
        echo json_encode($userDAO->search($search, $id));
    }
    if($query === "readProfile") {
        session_start();
        $id = filter_input(INPUT_POST, trim("id"));
        if($id !== $_SESSION["id"]) {
            $userDAO = new UserDAO();
            echo json_encode($userDAO->readProfile($id, $_SESSION["id"]));
        }
        else {
            echo json_encode(null);
        }
    }
    if($query === "sendInvite") {
        session_start();
        $id = filter_input(INPUT_POST, trim("id"));
        if($id !== $_SESSION["id"]) {
            $userDAO = new UserDAO();
            echo json_encode($userDAO->invite($id, $_SESSION["id"]));
        }
        else {
            echo json_encode(null);
        }
    }
    if($query === "aceitarConvite") {
        session_start();
        $id = filter_input(INPUT_POST, trim("id"));
        if($id !== $_SESSION["id"]) {
            $userDAO = new UserDAO();
            echo json_encode($userDAO->acceptInvite($id, $_SESSION["id"]));
        }
        else {
            echo json_encode(null);
        }
    }
    if($query === "cancelarConvite") {
        session_start();
        $id = filter_input(INPUT_POST, trim("id"));
        if($id !== $_SESSION["id"]) {
            $userDAO = new UserDAO();
            echo json_encode($userDAO->cancelInvitation($id, $_SESSION["id"]));
        }
        else {
            echo json_encode(null);
        }
    }
    if($query === "listFriendsRequests") {
        session_start();
        $id = $_SESSION["id"];
        $userDAO = new UserDAO();
        echo json_encode($userDAO->listFriendships($id));
    }
    if($query === "listFriendshipsHomePage") {
        session_start();
        $id = $_SESSION["id"];
        $userDAO = new UserDAO();
        echo json_encode($userDAO->listFriendshipsHomePage($id));
    }