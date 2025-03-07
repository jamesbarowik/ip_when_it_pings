<?php
// refactored code to put all the work into one page for adding an admin

session_start();  // connect to session if one has started

require_once 'admin_functions.php';  // include the admin functions
require_once '../dbconnect/db_connect_master.php';  // include once the db connect functions
require_once '../common_functions.php';  // include the main functions

if (!isset($_SESSION['admin_ssnlogin']) || $_SESSION['priv']!='SUPER'){
    $_SESSION['ERROR'] = "Admin not logged in / not enough privileges.";
    header("Location: admin_login.php");
    exit; // Stop further execution
}

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {  // if it's a post method
    // used to check correct format of email address

    if ($_POST['priv'] == "SUPER" and super_checker(dbconnect_select())) {
        $_SESSION['ERROR'] = "Super admin already exists, go login";
        header("Location: admin_index.php");
        exit; // Stop further execution
    } elseif (!pwrd_checker($_POST['password'], $_POST['cpassword'])) {  //calls function to check password complexity
        $_SESSION['ERROR'] = "Password related issue, try again";
        header("Location: add_admin.php");
        exit; // Stop further execution
    } else {
// this code runs if the previous checks are ok
        try {
            $short_task = $_POST['priv']."REG";
            $long_task = $_POST['username']." registered as a ".$_POST['priv'];
            if(reg_admin(dbconnect_insert(),$_POST) && auditor(dbconnect_insert(), $_POST['username'], $short_task, $long_task)) { // Assuming $conn is your database connection
                $_SESSION['SUCCESS'] = $_POST['priv']." ADMIN REGISTERED";
                header("Location: admin_index.php");
                exit; // Stop further execution
            } else {
                $_SESSION['ERROR'] = "ADD ADMIN FAIL, UNKNOWN ERROR";
                header("Location: admin_index.php");
                exit; // Stop further execution
            }
        }
        catch(Exception $e) {
            // Handle database error within reg_admin or here.
            $_SESSION['ERROR'] = "SUPER REG ERROR: ". $e->getMessage();
            header("Location: admin_index.php");
            exit; // Stop further execution
        }
    }

    }
else {

    echo "<!DOCTYPE html>";

    echo "<html lang='en'>";

    echo "<head>";
    echo "<title> RZL Add Admin Page</title>";
    echo "<link rel='stylesheet' href='admin_styles.css'>";
    echo "</head>";

    echo "<body>";

    echo "<div id='container'>";

    echo "<div id='title'>";

    echo "<h3 id='banner'>RZL Add Admin</h3>";

    echo "</div>";

    include 'admin_nav.php';

    echo "<div id='content'>";

    echo "<h4> Add New Admin </h4>";

    echo "<br>";

    echo admin_error($_SESSION);

    echo "<br>";

    echo "<form method='post' action='add_admin.php'>";

    echo "<input type='text' name='username' placeholder='Username' required><br>";

    echo "<input type='password' name='password' placeholder='Password' required><br>";

    echo "<input type='password' name='cpassword' placeholder='Confirm Password' required><br>";

    echo "<input type='text' name='fname' placeholder='First Name' required><br>";

    echo "<input type='text' name='sname' placeholder='Surname' required><br>";

    echo "<input type='text' name='email' placeholder='Email' required><br>";

    echo "<label for='user-role'>Select User Role:</label>";
    echo "<select name='priv'>";
    echo "<option value='CREATOR'>Creator</option>";
    echo "<option value='EDITOR'>Editor</option>";
    echo "</select><br>";

    echo "<input type='submit' name='submit' value='Register'>";

    echo "<br><br>";

    echo "</div>";

    echo "</div>";

    echo "</body>";

    echo "</html>";
}