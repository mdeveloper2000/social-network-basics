<?php

    header("Content-Type: application/json; charset=utf-8");
    require_once("../models/Post.php");
    require_once("../dao/PostDAO.php");

    $query = filter_input(INPUT_POST, "query");
    
    if($query === "list") {
        session_start();
        $user_id = $_SESSION["id"];
        $postDAO = new PostDAO();
        echo json_encode($postDAO->list($user_id));            
    }
    if($query === "get") {
        session_start();
        $id = filter_input(INPUT_POST, trim("id"));
        $postDAO = new PostDAO();        
        echo json_encode($postDAO->get($id));            
    }
    if($query === "save") {
        session_start();
        $post_text = filter_input(INPUT_POST, trim("post_text"));
        $user_id = $_SESSION["id"];
        $post = new Post();
        $post->setPost_text($post_text);
        $post->setUser_id($user_id);
        $postDAO = new PostDAO();
        echo json_encode($postDAO->save($post));
    }