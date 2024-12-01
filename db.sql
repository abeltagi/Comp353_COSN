CREATE DATABASE IF NOT EXISTS cosn;
USE cosn;

-- Create Member Table
CREATE TABLE Member (
    MemberID INT AUTO_INCREMENT PRIMARY KEY,
    Password VARCHAR(255) NOT NULL,
    Name VARCHAR(255) NOT NULL,
    Email VARCHAR(255) UNIQUE NOT NULL CHECK (Email LIKE '%@protonmail.com'),
    Status ENUM('Active', 'Inactive', 'Suspended') NOT NULL,
    Privilege ENUM('Admin', 'Senior', 'Junior') NOT NULL
);

-- Create Groups Table (renamed to avoid reserved word issues)
CREATE TABLE Groups (
    GroupID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Description TEXT,
    OwnerID INT NOT NULL,
    FOREIGN KEY (OwnerID) REFERENCES Member(MemberID)
);

-- Create Post Table
CREATE TABLE Post (
    PostID INT AUTO_INCREMENT PRIMARY KEY,
    Type ENUM('Text', 'Image', 'Video') NOT NULL,
    OwnerID INT NOT NULL,
    GroupID INT,
    Permissions ENUM('View-only', 'View-and-comment', 'View-and-add') NOT NULL,
    FOREIGN KEY (OwnerID) REFERENCES Member(MemberID),
    FOREIGN KEY (GroupID) REFERENCES Groups(GroupID)
);

-- Create Messages Table (renamed to avoid ambiguity)
CREATE TABLE Messages (
    MessageID INT AUTO_INCREMENT PRIMARY KEY,
    SenderID INT NOT NULL,
    ReceiverID INT NOT NULL,
    Timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    Content TEXT NOT NULL,
    FOREIGN KEY (SenderID) REFERENCES Member(MemberID),
    FOREIGN KEY (ReceiverID) REFERENCES Member(MemberID)
);

-- Create Member_Groups Table
CREATE TABLE Member_Groups (
    MemberID INT NOT NULL,
    GroupID INT NOT NULL,
    Role ENUM('Owner', 'Member') NOT NULL,
    PRIMARY KEY (MemberID, GroupID),
    FOREIGN KEY (MemberID) REFERENCES Member(MemberID),
    FOREIGN KEY (GroupID) REFERENCES Groups(GroupID)
);

-- Create Friendship Table
CREATE TABLE Friendship (
    MemberID1 INT NOT NULL,
    MemberID2 INT NOT NULL,
    Type ENUM('Family', 'Friend', 'Colleague') NOT NULL,
    PRIMARY KEY (MemberID1, MemberID2),
    FOREIGN KEY (MemberID1) REFERENCES Member(MemberID),
    FOREIGN KEY (MemberID2) REFERENCES Member(MemberID)
);
-- COMMENT TRUNCATE TABLE users;   --> deletes ALL table data and resets id counter back to 1


-- Insert sample members
INSERT INTO Member (Password, Name, Email, Status, Privilege)
VALUES 
('password123', 'Alice Smith', 'alice@protonmail.com', 'Active', 'Admin'),
('password456', 'Bob Johnson', 'bob@protonmail.com', 'Active', 'Senior'),
('password789', 'Charlie Brown', 'charlie@protonmail.com', 'Active', 'Junior');

-- Insert sample groups
INSERT INTO Groups (Name, Description, OwnerID)
VALUES 
('Photography Club', 'A group for photography enthusiasts', 1),
('Cooking Lovers', 'A place to share recipes and cooking tips', 2);

-- Insert sample posts
INSERT INTO Post (Type, OwnerID, GroupID, Permissions)
VALUES 
('Text', 1, 1, 'View-and-comment'),
('Image', 2, 1, 'View-and-add'),
('Video', 3, 2, 'View-only');

-- Insert sample messages
INSERT INTO Messages (SenderID, ReceiverID, Content)
VALUES 
(1, 2, 'Hi Bob, welcome to the platform!'),
(2, 3, 'Hey Charlie, join our Cooking Lovers group!'),
(3, 1, 'Thanks for the invite, Alice.');

-- Insert member-group relationships
INSERT INTO Member_Groups (MemberID, GroupID, Role)
VALUES 
(1, 1, 'Owner'),
(2, 1, 'Member'),
(2, 2, 'Owner'),
(3, 2, 'Member');

-- Insert friendships
INSERT INTO Friendship (MemberID1, MemberID2, Type)
VALUES 
(1, 2, 'Friend'),
(1, 3, 'Colleague'),
(2, 3, 'Family');




-- 1. Retrieve all posts in the 'Photography Club'
SELECT PostID, Type, Permissions
FROM Post
JOIN Groups ON Post.GroupID = Groups.GroupID
WHERE Groups.Name = 'Photography Club';

-- 2. List all members of the 'Cooking Lovers' group
SELECT Member.Name, Member.Email, Member_Groups.Role
FROM Member
JOIN Member_Groups ON Member.MemberID = Member_Groups.MemberID
JOIN Groups ON Member_Groups.GroupID = Groups.GroupID
WHERE Groups.Name = 'Cooking Lovers';

-- 3. Fetch all messages sent by 'Alice Smith'
SELECT Messages.MessageID, Messages.Content, Member.Name AS ReceiverName
FROM Messages
JOIN Member ON Messages.ReceiverID = Member.MemberID
WHERE Messages.SenderID = (SELECT MemberID FROM Member WHERE Name = 'Alice Smith');

-- 4. List all groups and their owners
SELECT Groups.Name AS GroupName, Member.Name AS OwnerName
FROM Groups
JOIN Member ON Groups.OwnerID = Member.MemberID;

-- 5. Retrieve friendship relationships for 'Bob Johnson'
SELECT M1.Name AS Member1, M2.Name AS Member2, Friendship.Type
FROM Friendship
JOIN Member AS M1 ON Friendship.MemberID1 = M1.MemberID
JOIN Member AS M2 ON Friendship.MemberID2 = M2.MemberID
WHERE M1.Name = 'Bob Johnson' OR M2.Name = 'Bob Johnson';