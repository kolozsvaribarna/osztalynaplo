<?php
/**
 * @author Kolozsvári Barnabás
 */

function requestHandle() {

    if (isset($_GET['initDB'])) {
        Header("Location: index.php?class=*");
        createDB();
    }
    elseif (isset($_GET['dropDB'])) {
        Header("Location: index.php");
        dropDB();
    }

    if (isset($_GET['class']) && isset($_GET['year'])) {
        $classes = $_SESSION['data']['classes'];
        $class = $_GET['class'];
        $year = $_GET['year'];

        if ($class == '*') {
            echo "<h2>All classes ($year)</h2>";
            foreach ($classes as $class) {
                displayTable($class, $year);
            }
        }
        elseif (in_array($class, $classes)) {
            echo "<h2>$year</h2>";
            displayTable($class, $year);
        }
        // class not found
        else echo "<p class='msg-error'>No class found</p>";
    }

    if (isset($_GET['statistics'])) {
        showStatisticsForm();
        switch ($_GET['statistics']) {
            case "Hall of Fame":
                displayHallOfFame();
                break;
            case "Best students by year":
                displayBest10StudentsByYear();
                break;
            default:
                break;
        }
    }
}