<?php
# this file is used for the login page. 
# pulls in the shared.php file to use the reusable functions  defined there
require_once 'shared.php';
# initializes an error variable to store any error messages that may occur during the login process
$error = "";

# checks if the form has been submitted by checking if the 'username' field is set in the POST request
if (isset($_POST['username'])) {
    $username = trim($_POST['username']);

    # three checks: format check, existence check, and if both pass, sets the session variable and redirects to files.php
    if (!is_valid_username($username)) {
        $error = "Invalid username format."; # sets an error message if the username format is invalid
    } elseif (!username_exists($username)) {
        $error = "Username not found."; # sets an error message if the username does not exist in the users.txt file
    } else {
        $_SESSION['username'] = $username; #sets the session variable for the logged-in user
        header("Location: files.php");
        exit;
    }
}
?>

<!--swithces into HTML mode to display the login form and any error messages that may have occurred during the login process-->
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
<h1>File Sharing Login</h1>

<?php if ($error): ?>
    <p style="color:red;"><?php echo htmlentities($error); ?></p> <!--displays the error message in red-->
<?php endif; ?>

<!-- standard HTML form for user login, which submits the username to the same page (login.php) using the POST method. The form includes a text input for the username and a submit button. -->
<form method="POST" action="login.php"> <!--method is post because we are sending data to the server. action is the php file above.-->
    Username: <input type="text" name="username" />
    <input type="submit" value="Log In" /> <!--submit button to send the form.-->
</form>

</body>
</html>