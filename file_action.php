<?php
# actions should the logic behind actions called in different files, such as uplodding, viewing, and deelting files
# pulls in the shared.php file to use the reusable functions and constants defined there
require_once 'shared.php';
# checks if the user is logged in; if not, redirects to the login page
require_login();

# retrieves the logged-in user's username from the session and validates it using the is_valid_username function. If the username is invalid, it displays an error message and exits the script.
$username = $_SESSION['username'];
if (!is_valid_username($username)) {
    echo "Invalid username";
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

# removes any directory info the brownser might have sent and keeps the file name itself. then validates the filename
if ($action === 'upload') {
    
    $filename = basename($_FILES['uploadedfile']['name']);
    if (!is_valid_filename($filename)) {
        echo "Invalid filename";
        exit;
    }
        $user_dir = get_user_dir($username);
        # checks if the user's directory exists; if not, it creates it with appropriate permissions (0770) and allows for recursive creation of directories if needed. 
        # Then, it constructs the full path for the uploaded file and moves the uploaded file from its temporary location to the user's directory
        # Finally, it redirects the user back to files.php after a successful upload.
    if (!is_dir($user_dir)) {
        mkdir($user_dir, 0770, true);
    }
    $full_path = sprintf("%s/%s", $user_dir, $filename);
    move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $full_path);
    header("Location: files.php");
    exit;
}
# checks if the action is 'view' and retrieves the filename from the GET request 
# It validates the filename, constructs the full path to the file in the user's directory, and checks if the file exists 
# If it does, it displays the file using readfile and redirects the user back to files.php. 
# If the filename is invalid or the file does not exist, it displays an appropriate error message.
elseif ($action === 'view') {
    $filename = $_GET['name'];
    if (!is_valid_filename($filename)) {
        echo "Invalid filename";
        exit;
    }

    if(!isvalid_name($owner)){
        echo "Invalid owner";
        exit;
    }

    #check permsissions
    if($owner !== $username && !is_shared_with($owner, $filename, $username)){
        echo "You do not have permission to view this file";
        exit;
    }

    $full_path = sprintf("%s/%s", get_user_dir($username), $filename);
    if (!file_exists($full_path)) {
        echo "File not found";
        exit;
    }

    # gets the file info like image vs txt and sets the appropriate headers for the file type and disposition.
    # It then reads the file and sends it to the browser for viewing. 
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    header("Content-Type: " . $finfo->file($full_path));
    header('Content-Disposition: inline; filename="' . $filename . '"');
    readfile($full_path);
    exit;
}

# checks if the action is 'delete', retrieves the filename from the GET request, validates it, and deletes the file if it exists.
elseif ($action === 'delete') {
    $filename = $_GET['name'];
    if (!is_valid_filename($filename)) {
        echo "Invalid filename";
        exit;
    }
    $full_path = sprintf("%s/%s", get_user_dir($username), $filename);
    if (file_exists($full_path)) {
        unlink($full_path);
    }
    header("Location: files.php");
    exit;
}

elseif($action === 'share') {
    $filename = isset($_POST['name']) ? $_POST['name'] : (isset($_GET['name']) ? $_GET['name'] : '');
    $target_user = isset($_POST['target_user']) ? trim($_POST['target_user']) : '';

    if (!is_valid_filename($filename)) {
        echo "Invalid filename";
        exit;
    }

    if (!is_valid_username($target_user)) {
        echo "Invalid target username";
        exit;
    }
    if(!username_exists($target_user)){
        echo "Target user does not exist";
        exit;
    }

    if(target_user === $username){
        echo "You cannot share a file with yourself";
        exit;
    }

    #make sure file actually belongs to the current user
    $full_path = sprintf("%s/%s", get_user_dir($username), $filename);
    if (!file_exists($full_path)) {
        echo "File not found";
        exit;
    }

    add_share($username, $filename, $target_user);
    header("Location: files.php");
    exit;
    
}

# checks if the action is 'delete' and retrieves the filename from the GET request.
else {
    echo "Invalid action";
    exit;
}



?>