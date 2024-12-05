<?php
/**
* @author Kolozsvári Barnabás
* desc: conditional states observing requests
*/
require_once "classroom-data.php";
require_once "data-functions.php";

if (!isset($_SESSION['data'])) {
    $_SESSION["data"] = getData();
}

if (!isset($_SESSION['students'])){
    generateStudents();
}

// displaying data when a class is selected
if (isset($_GET['class'])) {

    if (isset($_SESSION['students'])){
        $students = $_SESSION['students'];
    }

    $class = $_GET['class'];
    $classes = $_SESSION['data']['classes'];
    showClassList();

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
    else showMessagebox("No class found", "error");
}
// save current class to session
if (isset($_GET['class'])){
    $_SESSION['activeClass'] = $_GET['class'];
}
// call saveData when save button clicked
elseif (isset($_GET["save"])) {
    saveData();
}

if (isset($_GET['saveSchoolAvgs'])) {
    saveSchoolAverages();
}
else if (isset($_GET['saveClassAvgs'])) {
    saveClassAverages();
}
else if (isset($_GET['saveBeWoClass'])) {
    saveBeWoClass();
}
else if (isset($_GET['saveSchoolRanking'])) {
    saveSchoolRanking();
}
else if (isset($_GET['saveClassRanking'])) {
    saveClassRanking();
}