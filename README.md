Group Member names:
  1) (Leader) Rahath Ahmed - ENCS Account: a_rahath@login.encs.concordia.ca 
  2) Zarren Ali  - ENCS Account: a_zarren@login.encs.concordia.ca
  3) Ahmed Beltagi - ENCS Account: a_beltag@login.encs.concordia.ca
  4) Joshua Onazi - ENCS Account:  j_onazi@login.encs.concordia.ca

Note: Passwords are not to be given out under any circumstances. 
We do not understand why we are being asked for our personal account 
passwords

Group Name: COMP 353_group_10

Group ID: 10
Group Account Username: opc353_2 
Group Password: EngineerLikenessLines51

MySQL Database Account Username: opc353_2 
MySQL Database Account Password: EngineerLikenessLines51

URL for the project (local server): http://localhost:3000/index.php   ---> Assuming you are running off a local XAMPP set-up
URL for the project (ENCS web server): https://opc353.encs.concordia.ca/index.php ---> Assuming you are running off an online ENCS web server 

Directories:

 - config/
    db.php

 - css/
    style.css

 - uploads/
    1733511187_mario_test.jpg
    1733512495_mario_test.jpg

SQL Files:
  -  db.sql

PHP Files:

  -  accept_friend_request.php
  -  accept_senior_request.php
  -  add_remove_member_in_group.php
  -  add_to_wishlist.php
  -  admin_delete_member.php
  -  admin_manage_privilege_status.php
  -  admin_update_member.php
  -  approve_post.php
  -  block_member.php
  -  blocked_list.php
  -  chat.php
  -  create_group.php
  -  decline_friend_request.php
  -  decline_senior_request.php
  -  delete_account.php
  -  delete_event.php
  -  delete_group.php
  -  edit_profile.php
  -  event_suggestions.php
  -  events.php
  -  friends.php
  -  get_event_details.php
  -  gift_registry.php
  -  give_gift.php
  -  groups.php
  -  home.php
  -  index.php
  -  leave_group.php
  -  login.php
  -  logout.php
  -  manage_join_request.php
  -  messages.php
  -  posts.php
  -  privacy_settings.php
  -  process_create_event.php
  -  process_group_action.php
  -  process_group_delete.php
  -  process_leave_group.php
  -  process_manage_join_request.php
  -  process_request_join_group.php
  -  profile.php
  -  register.php
  -  request_join_group.php
  -  request_senior.php
  -  search.php
  -  send_friend_request.php
  -  show_groups.php
  -  unblock_member.php


Visual bug fixed in register.php: The navbar was displaying the wrong contents, fixed to correct contents.


Installing on a LAMP system
  Installing XAMPP:
  Go to: XAMPP Installers and Downloads for Apache Friends
  Install appropriate application for your device. 
  Follow steps listed from the link above.
  
  Running XAMPP:
  Open XAMPP Control Panel
  Click on Start for Apache and MySQL respectively
  You will see in the control panel the ports for each.
  Ideally you should not have to change anything.
  
  Running the website (locally through XAMPP):
  
  Find where the directory named xampp is located.
  Most likely found in your C: Drive
  Inside xampp located the htdocs directory.
  You should now be in …/xampp/htdocs . 
  In this directory drag and downloaded and UNZIPPED folder of our project.
  You may also create a folder and clone our public repo.
  Assuming you have Visual Studio Code installed, open the folder from there and serve the php file index.php


  Details of the AITS-Demo installation
    1. User Accounts and Credentials
    The following accounts and credentials were used to access the ENCS server and the project
    Environment:
    Personal Accounts (no password provided, not to be shared under any circumstances) 
    All team members ENCS accounts
    Rahath Ahmed: a_rahath@login.encs.concordia.ca
    Zarren Ali: a_zarren@login.encs.concordia.ca
    Ahmed Beltagi: a_beltag@login.encs.concordia.ca
    Joshua Onazi: j_onazi@login.encs.concordia.ca
    Group Account: 
    Username: `opc353_2`, Password: EngineerLikenessLines51
    MySQL Database Account: 
    Username: `opc353_2`, Password: EngineerLikenessLines51
    2. Server Access Steps (use your own info)
    SSH Connection:
    Command: `ssh a_beltag@login.encs.concordia.ca` (enter your own ENCS Account login)
    Enter the ENCS password to access the server.
    Navigating to Project Directory:
    Directory: `/www/groups/o/op_comp353_2`
    Verified contents using: `ls -l`
    Uploading Files:
    Used VSCode's Remote-SSH and SCP for file uploads.
    SCP Command: `scp /path/to/local/file.php
    a_beltag@login.encs.concordia.ca:/www/groups/o/op_comp353_2/`
    Setting Permissions:
    Command: `chmod 644 file.php`
    Testing Files:
    Accessed files via: `https://opc353.encs.concordia.ca/file.php`

Note: The files are all placed in there, if any issues arise, you may need to enable proper file/directory permissions.

3. MySQL Database Setup
    Database Access:
    Command: `mysql -h opc353.encs.concordia.ca -u opc353_2 -p opc353_2`
    Password: EngineerLikenessLines51
    Verifying Database:
    SQL Command: `SHOW TABLES;`
    4. Challenges Encountered
    VSCode Timeout: Resolved by adding `ServerAliveInterval 60` to SSH config.
    File Upload Issues: Used SCP as an alternative.
    Permissions Errors: Resolved with `chmod 644`.
    5. Solutions Applied
    Adjusted SSH settings to prevent timeouts.
    Verified uploads using terminal commands.
    Applied proper file permissions.



