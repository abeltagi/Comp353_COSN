CREATE DATABASE IF NOT EXISTS cosn;
USE cosn;


CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY ,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    age INT NOT NULL, 
    profession VARCHAR(100) NOT NULL, 
    region VARCHAR(100) NOT NULL, 
    privilege ENUM('Admin', 'Senior', 'Junior') DEFAULT 'Junior' NOT NULL,
    status ENUM('Active', 'Inactive', 'Suspended') DEFAULT 'Active' NOT NULL,
    dob DATE NOT NULL
    
);
 
DROP TABLE members;
DROP TABLE groups;
DROP TABLE group_members;
DROP TABLE group_posts;

CREATE TABLE groups (
    group_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    owner_id INT NOT NULL,
    interest VARCHAR(100) NOT NULL,
    FOREIGN KEY (owner_id) REFERENCES members(id) ON DELETE CASCADE
);

CREATE TABLE group_members (
    group_id INT NOT NULL,
    member_id INT NOT NULL,
    role ENUM('Owner', 'Member') DEFAULT 'Member',
    PRIMARY KEY (group_id, member_id),
    FOREIGN KEY (group_id) REFERENCES groups(group_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

CREATE TABLE group_posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    member_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(group_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);





