<?php
# this file is used for shared functions and constants used across several files for keeping things concise. 
session_start();
# creates a constant storage and user file directory that cant be changed; nobody should be able to change these values or access them
define('STORAGE_DIR', '/srv/module2group');
define('USERS_FILE', '/srv/module2group/users.txt');

#reusable function to make sure the username is valid and only contains letters, numbers, underscores, and hyphens; rejects usernames with spaces, slashes, dots, etc. with or anything unusual
function is_valid_username($username) {
    return preg_match('/^[\w\-]+$/', $username);
}

#reusable function to make sure the filename is valid and only contains letters, numbers, underscores, hyphens, and dots; rejects filenames with spaces, slashes, or anything unusual, same as above
function is_valid_filename($filename) {
    return preg_match('/^[\w\.\-]+$/', $filename);
}


# first, makes sure that the USERS_FILE exists. 
#if it does, it is a reusable function to check if a username already exists in the users.txt file; 
#returns true if the username exists, false otherwise
function username_exists($username) {
    if (!file_exists(USERS_FILE)) {
        return false;
    }
    $h = fopen(USERS_FILE, "r");
    while (!feof($h)) {
        $line = trim(fgets($h)); #each line is trimmed to remove white space 
        if ($line === $username) {
            fclose($h);
            return true; #loop stops when found
        }
    }
    fclose($h);
    return false;
}

# reusable function to check if a user is logged in; if not, redirects to the login page
# extremely important to prevent non-logged-in users from accessing files
function require_login() {
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
}

# reusable function to get the user's storage directory based on their username; 
# returns the path to the user's directory for housekeeping 
function get_user_dir($username) {
    return sprintf("%s/%s", STORAGE_DIR, $username);
}
# reusable function to get the path to the shares.txt file, which is used to store shared files information
define('SHARES_FILE', '/home/nalin/data/shares.txt');

#checks if a specifc file is shared with a specific user;
function share_exists($owner, $filename, $target_user){
    if(!file_exists(SHARES_FILE)){
        return false;
    }
    $h = fopen(SHARES_FILE, "r");
    while(!feof($h)){
        $line = trim(fgets($h));
        if($line === "$owner:$filename:$target_user"){
            fclose($h);
            return true;
        }
    }
    fclose($h);
    return false;
}

function add_share($owner, $filename, $target_user){
    if(share_exists($owner, $filename, $target_user)){
        return ; // share already exists
    }
    $h = fopen(SHARES_FILE, "a");
    fwrite($h, "$owner:$filename:$target_user\n");
    fclose($h);
}

//return array of users that a file is shared with
function get_shares_for_users($current_user){
    $results = array();
    if(!file_exists(SHARES_FILE)){
        return $results;
    }
    $h = fopen(SHARES_FILE, "r");
    while(!feof($h)){
        $line = trim(fgets($h));
        if($line === ''){
            continue;
        }
        $parts = explode(":", $line);
        if(count($parts) === 3 && $parts[2] === $current_user){
            $results[] = array('owner' => $parts[0], 'filename' => $parts[1]);
        }
    }
    fclose($h);
    return $results;
}

?>