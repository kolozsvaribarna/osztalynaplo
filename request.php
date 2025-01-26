<?php
/**
 * @author Kolozsvári Barnabás
 */

function requestHandle() {

    if (isset($_GET['initDB'])) {
        Header("Location: index.php?class=*");
        dropDB();
        createDB();
        uploadDB();
    }
    elseif (isset($_GET['dropDB'])) {
        Header("Location: index.php");
        dropDB();
    }

    if (isset($_GET['class'])) {
        $class = $_GET['class'];
        $classes = $_SESSION['data']['classes'];

        if ($class == '*') {
            echo "<h2>All classes</h2>";
            foreach ($classes as $class) {
                displayTable($class);
            }
        }
        elseif (in_array($class, $classes)) {
            displayTable($class);
        }
        // class not found
        else echo "<p class='msg-error'>No class found</p>";
    }

    if (isset($_GET['statistics'])) {
        showStatisticsForm();
        switch ($_GET['statistics']) {
            case "Subject Averages":
                displaySubjectAverages();
                break;
            case "Student Rankings":
                echo "Student ranking";
                break;
            case "Class Rankings":
                echo "Class ranking";
                break;
            default:
                break;
        }
    }

}