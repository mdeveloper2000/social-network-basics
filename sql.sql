CREATE DATABASE socialnetwork;

USE socialnetwork;

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    email VARCHAR(50) NOT NULL,
    userpassword VARCHAR(60) NOT NULL,
    username VARCHAR(30) NOT NULL,
    picture VARCHAR(100) NOT NULL DEFAULT 'default.jpg',    
    about VARCHAR(150) NOT NULL DEFAULT '',
    PRIMARY KEY(id),
    UNIQUE(email)
);

CREATE TABLE posts (
    id INT NOT NULL AUTO_INCREMENT,
    post_text VARCHAR(200) NOT NULL,
    post_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_id INT NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY(user_id) REFERENCES users(id)
);

CREATE TABLE friendship_invitations (
    id INT NOT NULL AUTO_INCREMENT,
    sent_id INT NOT NULL,
    received_id INT NOT NULL,
    accepted ENUM('YES', 'NO') NOT NULL DEFAULT 'NO',
    PRIMARY KEY(id),
    FOREIGN KEY(sent_id) REFERENCES users(id),
    FOREIGN KEY(received_id) REFERENCES users(id)
);

CREATE TABLE messages (
    id INT NOT NULL AUTO_INCREMENT,
    message_text VARCHAR(100) NOT NULL,
    from_id INT NOT NULL,
    to_id INT NOT NULL,
    message_date TIMESTAMP NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY(from_id) REFERENCES users(id),
    FOREIGN KEY(to_id) REFERENCES users(id)
);