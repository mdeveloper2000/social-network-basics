<?php

require_once("ConnectionFactory.php");
require_once("../models/Post.php");

class PostDAO {

    private $connection;
    
    public function __construct() {
        $this->connection = ConnectionFactory::connect();
    }

    public function list($user_id) {

        $posts = array();
        try {            
            $sql = "SELECT * FROM posts WHERE user_id = :user_id ORDER BY post_date DESC";
            $rs = $this->connection->prepare($sql);
            $rs->bindParam(":user_id", $user_id);
            $rs->execute();
            if($rs->rowCount() > 0) {
                while($row = $rs->fetch(PDO::FETCH_OBJ)) {
                    $post = new Post();
                    $post->setId($row->id);
                    $post->setPost_text($row->post_text);
                    $post->setPost_date(date("d/m/Y H:i:s", strtotime($row->post_date)));
                    $post->setUser_id($row->user_id);
                    array_push($posts, $post);
                }
            }
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return $posts;

    }

    public function save(Post $post) {

        try {            
            $sql = "INSERT INTO posts (post_text, user_id) VALUES (:post_text, :user_id)";
            $rs = $this->connection->prepare($sql);
            $rs->bindValue(":post_text", $post->getPost_text());
            $rs->bindValue(":user_id", $post->getUser_id());                
            $rs->execute();
            if($rs->rowCount() > 0) {
                $last_id = $this->connection->lastInsertId();
                return $last_id;
            }            
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;

    }

    public function get($id) {
        
        try {            
            $sql = "SELECT * FROM posts WHERE id = :id";
            $rs = $this->connection->prepare($sql);
            $rs->bindParam(":id", $id);
            $rs->execute();
            if($rs->rowCount() > 0) {
                $row = $rs->fetch(PDO::FETCH_OBJ);
                $post = new Post();
                $post->setId($row->id);
                $post->setPost_text($row->post_text);
                $post->setPost_date(date("d/m/Y H:i:s", strtotime($row->post_date)));
                $post->setUser_id($row->user_id);
                return $post;
            }
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;

    }

}