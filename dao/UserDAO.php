<?php

require_once("ConnectionFactory.php");
require_once("../models/User.php");

class UserDAO {

    private $connection;
    
    public function __construct() {
        $this->connection = ConnectionFactory::connect();
    }

    public function login($email, $userpassword) {

        try {            
            $sql = "SELECT * FROM users WHERE email = :email";
            $rs = $this->connection->prepare($sql);
            $rs->bindParam(":email", $email);
            $rs->execute();
            if($rs->rowCount() > 0) {
                $row = $rs->fetch(PDO::FETCH_OBJ);
                if(password_verify($userpassword, $row->userpassword)) {
                    $user = new User();
                    $user->setId($row->id);
                    $user->setUsername($row->username);
                    $user->setPicture($row->picture);
                    return $user;
                }
            }
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;

    }

    public function register(User $user) {

        try {            
            $sql = "SELECT * FROM users WHERE email = :email";
            $rs = $this->connection->prepare($sql);
            $rs->bindValue(":email", $user->getEmail());
            $rs->execute();
            if($rs->rowCount() > 0) {
                return false;
            }
            else {
                $sql = "INSERT INTO users (username, email, userpassword) VALUES (:username, :email, :userpassword)";
                $rs = $this->connection->prepare($sql);
                $rs->bindValue(":username", $user->getUsername());
                $rs->bindValue(":email", $user->getEmail());
                $rs->bindValue(":userpassword", $user->getUserpassword());
                $rs->execute();
                if($rs->rowCount() > 0) {
                    return true;
                }
            }
        }
        catch(PDOException $e) {
            echo($e->getMessage());
        }
        return null;

    }

    public function getPicture($id) {
        try {            
            $sql = "SELECT picture FROM users WHERE id = :id";
            $rs = $this->connection->prepare($sql);
            $rs->bindParam(":id", $id);
            $rs->execute();
            if($rs->rowCount() > 0) {
                $row = $rs->fetch(PDO::FETCH_OBJ);
                return $row->picture;
            }
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;
    }

    public function updatePicture(User $user) {
        try {            
            $sql = "UPDATE users SET picture = :picture WHERE id = :id";
            $rs = $this->connection->prepare($sql);
            $rs->bindValue(":picture", $user->getPicture());
            $rs->bindValue(":id", $user->getId());
            $rs->execute();            
            return $user->getPicture();
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;
    }

    public function getProfile($id) {
        try {            
            $sql = "SELECT about FROM users WHERE id = :id";
            $rs = $this->connection->prepare($sql);
            $rs->bindParam(":id", $id);
            $rs->execute();
            if($rs->rowCount() > 0) {
                $row = $rs->fetch(PDO::FETCH_OBJ);                
                $user = new User();
                $user->setAbout($row->about);
                return $user;
            }
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;
    }

    public function updateProfile(User $user) {
        try {            
            $sql = "UPDATE users SET about = :about WHERE id = :id";
            $rs = $this->connection->prepare($sql);
            $rs->bindValue(":about", $user->getAbout());
            $rs->bindValue(":id", $user->getId());
            $rs->execute();
            return $user;
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;
    }

    public function search($query, $id) {
        $users = array();
        try {
            $sql = "SELECT id, username, picture FROM users WHERE username LIKE :username AND id <> :id";
            $rs = $this->connection->prepare($sql);
            $rs->bindValue(":username", "%".$query."%");
            $rs->bindValue(":id", $id);
            $rs->execute();
            if($rs->rowCount() > 0) {
                while($row = $rs->fetch(PDO::FETCH_OBJ)) {
                    $user = new User();
                    $user->setId($row->id);
                    $user->setUsername($row->username);
                    $user->setPicture($row->picture);
                    array_push($users, $user);
                }
            }
            return $users;
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;
    }

    public function readProfile($id, $session_id) {
        try {            
            $sql = "SELECT id, username, picture, about FROM users WHERE id = :id";
            $rs = $this->connection->prepare($sql);
            $rs->bindParam(":id", $id);
            $rs->execute();
            if($rs->rowCount() > 0) {
                $row = $rs->fetch(PDO::FETCH_OBJ);                
                $user = new stdClass;
                $user->id = $row->id;
                $user->username = $row->username;
                $user->picture = $row->picture;
                $user->about = $row->about;
                $sql = "SELECT sent_id, received_id, accepted FROM friendship_invitations 
                        WHERE sent_id = :sent_session_id AND received_id = :received_id
                        OR sent_id = :received_session_id AND received_id = :sent_id";
                $rs = $this->connection->prepare($sql);
                $rs->bindParam(":sent_session_id", $session_id);
                $rs->bindParam(":received_id", $id);
                $rs->bindParam(":received_session_id", $id);
                $rs->bindParam(":sent_id", $session_id);
                $rs->execute();
                $friendship = new stdClass;
                if($rs->rowCount() > 0) {
                    $row = $rs->fetch(PDO::FETCH_OBJ);                
                    $friendship->sent_id = $row->sent_id;
                    $friendship->received_id = $row->received_id;
                    $friendship->accepted = $row->accepted;
                }
                else {
                    $friendship = null;
                }                
                $user->friendship = $friendship;
                return $user;
            }
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;
    }

    public function invite($id, $session_id) {
        try {
            $sql = "SELECT sent_id, received_id FROM friendship_invitations 
            WHERE sent_id = :sent_id AND received_id = :received_id";
            $rs = $this->connection->prepare($sql);
            $rs->bindValue(":sent_id", $session_id);
            $rs->bindValue(":received_id", $id);
            $rs->execute();
            if($rs->rowCount() > 0) {
                return null;
            }
            else {
                $sql = "INSERT INTO friendship_invitations (sent_id, received_id) VALUES (:sent_id, :received_id)";
                $rs = $this->connection->prepare($sql);
                $rs->bindValue(":sent_id", $session_id);
                $rs->bindValue(":received_id", $id);
                $rs->execute();
                if($rs->rowCount() > 0) {
                    return true;
                }
            }
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;
    }

    public function acceptInvite($id, $session_id) {
        try {
            $sql = "UPDATE friendship_invitations SET accepted = 'YES' WHERE sent_id = :sent_id 
            AND received_id = :received_id";
            $rs = $this->connection->prepare($sql);
            $rs->bindValue(":sent_id", $id);
            $rs->bindValue(":received_id", $session_id);
            $rs->execute();
            if($rs->rowCount() > 0) {
                return true;
            }
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;
    }

    public function cancelInvitation($id, $session_id) {
        try {
            $sql = "DELETE FROM friendship_invitations WHERE sent_id = :sent_id 
            AND received_id = :received_id AND accepted = 'NO'";
            $rs = $this->connection->prepare($sql);
            $rs->bindValue(":sent_id", $session_id);
            $rs->bindValue(":received_id", $id);
            $rs->execute();
            if($rs->rowCount() > 0) {
                return true;
            }
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;
    }

    public function listFriendships($id) {
        $list = new stdClass;
        $invites_accepted = array();
        $invites_sent = array();
        $invites_received = array();
        try {
            $sql = "SELECT u.id, u.username, u.picture FROM users u
                    WHERE u.id IN  
                    (
                        SELECT f.sent_id
                        FROM friendship_invitations f
                        WHERE f.accepted = 'YES' AND f.received_id = :id1
                    UNION ALL
                        SELECT f.received_id
                        FROM friendship_invitations f
                        WHERE f.accepted = 'YES' AND f.sent_id = :id2
                    )";
            $rs = $this->connection->prepare($sql);
            $rs->bindValue(":id1", $id);
            $rs->bindValue(":id2", $id);
            $rs->execute();
            if($rs->rowCount() > 0) {
                while($row = $rs->fetch(PDO::FETCH_OBJ)) {
                    $user = new stdClass;
                    $user->id = $row->id;
                    $user->username = $row->username;
                    $user->picture = $row->picture;
                    array_push($invites_accepted, $user);
                }
            }
            $list->invites_accepted = $invites_accepted;

            $sql = "SELECT f.sent_id, u.id, u.username, u.picture FROM friendship_invitations f
            INNER JOIN users u ON f.received_id = u.id 
            WHERE f.accepted = 'NO' AND f.sent_id = :id";
            $rs = $this->connection->prepare($sql);
            $rs->bindValue(":id", $id);
            $rs->execute();
            if($rs->rowCount() > 0) {
                while($row = $rs->fetch(PDO::FETCH_OBJ)) {
                    $user = new stdClass;
                    $user->id = $row->id;
                    $user->username = $row->username;
                    $user->picture = $row->picture;
                    array_push($invites_sent, $user);
                }
            }
            $list->invites_sent = $invites_sent;

            $sql = "SELECT f.received_id, u.id, u.username, u.picture FROM friendship_invitations f
            INNER JOIN users u ON f.sent_id = u.id 
            WHERE f.accepted = 'NO' AND f.received_id = :id";
            $rs = $this->connection->prepare($sql);
            $rs->bindValue(":id", $id);
            $rs->execute();
            if($rs->rowCount() > 0) {
                while($row = $rs->fetch(PDO::FETCH_OBJ)) {
                    $user = new stdClass;
                    $user->id = $row->id;
                    $user->username = $row->username;
                    $user->picture = $row->picture;
                    array_push($invites_received, $user);
                }
            }
            $list->invites_received = $invites_received;

            return $list;
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;
    }

    public function listFriendshipsHomePage($id) {
        $friendships = array();
        try {
            $sql = "SELECT u.id, u.username, u.picture FROM users u
                    WHERE u.id IN  
                    (
                        SELECT f.sent_id
                        FROM friendship_invitations f
                        WHERE f.accepted = 'YES' AND f.received_id = :id1
                    UNION ALL
                        SELECT f.received_id
                        FROM friendship_invitations f
                        WHERE f.accepted = 'YES' AND f.sent_id = :id2
                    ) LIMIT 4";
            $rs = $this->connection->prepare($sql);
            $rs->bindValue(":id1", $id);
            $rs->bindValue(":id2", $id);
            $rs->execute();
            if($rs->rowCount() > 0) {
                while($row = $rs->fetch(PDO::FETCH_OBJ)) {
                    $user = new stdClass;
                    $user->id = $row->id;
                    $user->username = $row->username;
                    $user->picture = $row->picture;
                    array_push($friendships, $user);
                }
                return $friendships;
            }
        }
        catch(PDOException $exception) {
            echo($exception->getMessage());
        }
        return null;
    }

}