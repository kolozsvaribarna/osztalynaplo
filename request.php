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
            case "Subject Averages":
                displaySubjectAverages(getSubjectsFromDB(), getSubjectAvgsSchool());
                break;
            case "Student Rankings":
                displayStudentRanking();
                break;
            case "Class Rankings":
                echo "<h2>Best and worst classes</h2>";
                displayBestWorstClasses(getClassRankingByAvg());
                displayBestWorstClassesBySubject(getClassRakingBySubjectAverage(), getClassRakingBySubjectAverage("ASC"), getSubjectsFromDB());
                break;
            default:
                break;
        }
    }
}