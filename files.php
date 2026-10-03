<?php
# pulls in the shared.php file to use the reusable functions and constants defined there
require_once 'shared.php';
# checks if the user is logged in; if not, redirects to the login page
require_login();

# retrieves the logged-in user's username from the session and gets their corresponding storage directory using the get_user_dir function. If the user's directory does not exist, it creates it with appropriate permissions (0770) and allows for recursive creation of directories if needed.
$username = $_SESSION['username'];
$user_dir = get_user_dir($username); #this function we defined in shared.php

if (!is_dir($user_dir)) {
    mkdir($user_dir, 0770, true); #the owner and group have read, write, and execute permissions (0770), while others have no permissions (0). 
}

# retrieves the list of files in the user's directory using scandir and filters out the entries '.' and '..' using array_diff.
$files = array_diff(scandir($user_dir), array('.', '..'));
$shared_files = get_shares_for_user($username);
?>

<!-- switch to html mode to display the user's files and provide options for uploading new files or deleting existing ones. -->
<!DOCTYPE html>
<html>
<head><title>My Files</title></head>
<body>
    <!-- displays a welcome message, showing their username in a safe manner using htmlentities. It also provides a link for the user to log out of the application. -->
<h1>Welcome, <?php echo htmlentities($username); ?></h1>  <!--htmlentities ensures that any special characters in the username are properly escaped to prevent XSS attacks -->
<p><a href="logout.php">Log out</a></p> <!-- provides logout buttom link-->

<h2>Your Files</h2>
<ul>
<!-- checks if the user has any files in their directory.-->
 <!--Iterates through the list of files and displays each file's name along with options to view or delete the file.-->
 <!--The file names are safely displayed using htmlentities, and the links for viewing and deleting files include URL-encoded file names to ensure proper handling of special characters. -->
<?php foreach ($files as $file): ?>
    <li> <!--list item for each file-->
<?php echo htmlentities($file); ?>
<!-- provides links for viewing and deleting each file. -->
 <!--The "View" link directs the user to a file_action.php page with the action set to "view" and the file name passed as a URL parameter. The "Delete" link also directs to the same page but with the action set to "delete". -->
        - <a href="file_action.php?action=view&name=<?php echo urlencode($file); ?>">View</a>
        - <a href="file_action.php?action=delete&name=<?php echo urlencode($file); ?>" onclick="return confirm('Delete this file?');">Delete</a>
        <form action="file_action.php" method="POST" style="display:inline;">
            <input type="hidden" name="action" value="share" />
            <input type="hidden" name="name" value="<?php echo htmlentities($file); ?>" />
            <input type="text" name="target_user" placeholder="Share with username" size="15" />
            <input type="submit" value="Share" />
        </form>
    </li>
<?php endforeach; ?> <!--end of the foreach loop that iterates through the user's files  -->
</ul>

<h2>Shared With You</h2>
<ul>
<?php if (empty($shared_files)): ?>
    <li>No files have been shared with you.</li>
<?php else: ?>
    <?php foreach ($shared_files as $share): ?>
        <li>
            <?php echo htmlentities($share['filename']); ?>
            (from <?php echo htmlentities($share['owner']); ?>)
            - <a href="file_action.php?action=view&name=<?php echo urlencode($share['filename']); ?>&owner=<?php echo urlencode($share['owner']); ?>">View</a>
        </li>
    <?php endforeach; ?>
<?php endif; ?>
</ul>

<!-- provides a form for users to upload new files. -->
<h2>Upload a File</h2>
<!-- uses the post method; the enctype attribute is set to "multipart/form-data" to allow file uploads. -->
<form enctype="multipart/form-data" action="file_action.php" method="POST">
    <!--follows the structure shown on the PHP wikki-->
    <input type="hidden" name="action" value="upload" />
    <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
    <label for="uploadfile_input">Choose a file:</label>
    <input name="uploadedfile" type="file" id="uploadfile_input" />
    <input type="submit" value="Upload File" />
</form>

</body>
</html>