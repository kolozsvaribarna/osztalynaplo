<?php
/**
* @author Kolozsvári Barnabás
*/

require_once "html-functions.php";
require_once "database-setup.php";

htmlHead();

showClassList();

// show button if db does not exist
$mysqli = new mysqli("localhost", "root", "");
if (1 != count(mysqli_fetch_all($mysqli->query("SHOW DATABASES LIKE 'classroom';")))) {
    showDBInitBtn();
}

// displaying data when a class is selected
if (isset($_GET['class'])) {

    $class = $_GET['class'];
    $classes = $_SESSION['data']['classes'];

    // every class selected
    if ($class == '*') {
        echo "<h2>All classes</h2>";
        foreach ($classes as $class) {
            displayTable($class);
        }
    }
    // valid class is selected
    elseif (in_array($class, $classes) !== false) {
        displayTable($class);
    }
    // class not found
    else echo "<p class='msg'>No class found</p>";
}