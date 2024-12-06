CREATE DATABASE IF NOT EXISTS cosn;
USE cosn;


CREATE TABLE IF NOT EXISTS members (
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
CREATE TABLE IF NOT EXISTS member_privacy (
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

drop table join_requests;

-- the extra s at the end of groups IS NOT A TYPO ; groups is a keyword in MySQL
CREATE TABLE IF NOT EXISTS groupss (
    group_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    owner_id INT NOT NULL,
    interest VARCHAR(100) NOT NULL,
    FOREIGN KEY (owner_id) REFERENCES members(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS group_members (
    group_id INT NOT NULL,
    member_id INT NOT NULL,
    role ENUM('Owner', 'Member') DEFAULT 'Member',
    PRIMARY KEY (group_id, member_id),
    FOREIGN KEY (group_id) REFERENCES groupss(group_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

ALTER TABLE group_members
ADD COLUMN can_comment BOOLEAN DEFAULT TRUE,
ADD COLUMN can_add_content BOOLEAN DEFAULT TRUE;
-- VERY IMPORTANT

CREATE TABLE IF NOT EXISTS group_posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    member_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groupss(group_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);
-- VERY IMPORTANT
ALTER TABLE group_posts
ADD COLUMN status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
ADD COLUMN accessible_by TEXT DEFAULT 'all', -- Comma-separated group IDs or 'all' for all groups
ADD COLUMN visibility ENUM('Private', 'Group', 'Public') DEFAULT 'Group';
ALTER TABLE group_posts 
ADD COLUMN file_path VARCHAR(255) DEFAULT NULL;




CREATE TABLE IF NOT EXISTS join_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    member_id INT NOT NULL,
    status ENUM('Pending', 'Accepted', 'Declined') DEFAULT 'Pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groupss(group_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS friends (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL, -- User who initiated the request
    friend_id INT NOT NULL, -- Friend user
    status ENUM('Pending', 'Accepted') DEFAULT 'Pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (friend_id) REFERENCES members(id) ON DELETE CASCADE
);


-- blocks table is related to friends table above
CREATE TABLE IF NOT EXISTS blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blocker_id INT NOT NULL, -- The member who blocks
    blocked_id INT NOT NULL, -- The member being blocked
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (blocker_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (blocked_id) REFERENCES members(id) ON DELETE CASCADE,
    UNIQUE(blocker_id, blocked_id) -- Prevent duplicate blocks
);


CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    event_date DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    organizer_username VARCHAR(255),
    group_id INT,
    FOREIGN KEY (organizer_username) REFERENCES members(username) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES groupss(group_id) ON DELETE CASCADE
);
-- related to table above events
CREATE TABLE IF NOT EXISTS event_suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    suggested_by INT NOT NULL,
    suggested_date DATETIME NULL,
    suggested_location VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (suggested_by) REFERENCES members(id) ON DELETE CASCADE
);


drop table event_suggestions;

-- related to table above event_suggestions
CREATE TABLE IF NOT EXISTS suggestion_votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    suggestion_id INT NOT NULL,
    voted_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (suggestion_id) REFERENCES event_suggestions(id) ON DELETE CASCADE,
    FOREIGN KEY (voted_by) REFERENCES members(id) ON DELETE CASCADE,
    UNIQUE(suggestion_id, voted_by) -- Ensure a user can vote for a suggestion only once
);
drop table suggestion_votes;


CREATE TABLE senior_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    requested_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES members(id) ON DELETE CASCADE
);

CREATE TABLE wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

CREATE TABLE gifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    wishlist_id INT NOT NULL,
    giver_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (wishlist_id) REFERENCES wishlists(id) ON DELETE CASCADE,
    FOREIGN KEY (giver_id) REFERENCES members(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    member_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES group_posts(post_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);




INSERT INTO events (event_name, description, event_date, location, organizer_username, group_id)
VALUES 
('Group Event 1', 'This is an event for Group 6', '2024-12-15 10:00:00', 'Community Center', 'admin', 6),
('Group Event 2', 'This is another event for Group 7', '2024-12-20 14:00:00', 'Park', 'RatUser', 7);

DROP TABLE events;

DROP TABLE friends;

DROP TABLE blocks;

SELECT group_id, name FROM groupss WHERE owner_id = 1;

SELECT e.*
FROM events e
LEFT JOIN group_members gm ON gm.group_id = e.group_id
WHERE (e.organizer_username = 'admin' OR gm.member_id = 3) AND e.event_date > NOW();




