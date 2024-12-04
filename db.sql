CREATE DATABASE IF NOT EXISTS cosn;
USE cosn;


CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY ,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    CONSTRAINT email_domain_check CHECK (
        email LIKE '%@proton.me' OR
        email LIKE '%@protonmail.com' OR
        email LIKE '%@pm.me' OR
        email LIKE '%@protonmail.ch'
    ),
    password VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    age INT NOT NULL, 
    profession VARCHAR(100) NOT NULL, 
    region VARCHAR(100) NOT NULL, 
    privilege ENUM('Admin', 'Senior', 'Member') DEFAULT 'Member' NOT NULL,
    status ENUM('Active', 'Inactive', 'Suspended') DEFAULT 'Active' NOT NULL,
    dob DATE NOT NULL
    
);
CREATE TABLE member_privacy (
    member_id INT PRIMARY KEY,
    hide_firstname BOOLEAN DEFAULT FALSE,
    hide_lastname BOOLEAN DEFAULT FALSE,
    hide_email BOOLEAN DEFAULT FALSE,
    hide_address BOOLEAN DEFAULT FALSE,
    hide_age BOOLEAN DEFAULT FALSE,
    hide_profession BOOLEAN DEFAULT FALSE,
    hide_region BOOLEAN DEFAULT FALSE,
    hide_dob BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

DROP TABLE member_privacy;
 
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


CREATE TABLE join_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    member_id INT NOT NULL,
    status ENUM('Pending', 'Accepted', 'Declined') DEFAULT 'Pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(group_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

CREATE TABLE friends (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL, -- User who initiated the request
    friend_id INT NOT NULL, -- Friend user
    status ENUM('Pending', 'Accepted') DEFAULT 'Pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (friend_id) REFERENCES members(id) ON DELETE CASCADE
);


-- blocks table is related to friends table above
CREATE TABLE blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blocker_id INT NOT NULL, -- The member who blocks
    blocked_id INT NOT NULL, -- The member being blocked
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (blocker_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (blocked_id) REFERENCES members(id) ON DELETE CASCADE,
    UNIQUE(blocker_id, blocked_id) -- Prevent duplicate blocks
);


DROP TABLE friends;

DROP TABLE blocks;

SELECT group_id, name FROM groups WHERE owner_id = 1;


