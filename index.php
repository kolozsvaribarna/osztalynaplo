<?php
/**
* @author Kolozsvári Barnabás
*/

require_once "html-functions.php";
require_once "database-setup.php";

htmlHead();

if (isset($_GET['initDB'])) {
    header("Location: index.php");
    initDB();
    uploadDB();
}
else if (!isset($_GET['initDB'])) {
    showDBInitBtn();
}

foreach (getAllStudents() as $student) {
    echo '<pre>'; print_r($student); echo '</pre>';
}