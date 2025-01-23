<?php
/**
 * @author Kolozsvári Barnabás
 */

function requestHandle() {

    if (isset($_GET['initDB'])) {
        Header("Location: index.php");
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

        // every class selected
        if ($class == '*') {
            echo "<h2>All classes</h2>";
            foreach ($classes as $class) {
                displayTable($class);
            }
        }
        // single class selected
        elseif (in_array($class, $classes)) {
            // TODO - FIX HEIGHT
            echo "<h2>.</h2>";
            displayTable($class);
        }
        // class not found
        else echo "<p class='msg-error'>No class found</p>";
    }
}