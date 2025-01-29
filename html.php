<?php
/**
 * @author Kolozsvári Barnabás
 * desc: code responsible for the site's HTML
*/

function htmlHead() {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' href='style.css' type='text/css'>
        <title>Classroom</title>
    </head>";
}
function htmlStart() { echo "<body>"; }
function htmlEnd() { echo "</body></html>"; }
function displayNav() {
    if (DBexists($_SESSION['database'])) {
        echo "<div class='msg-error msg-right'>No active database connection!</div>";
        echo "<form method='GET'><input type='submit' class='btn btn-save' value='Create database' name='initDB'></form>";
        return;
    }
    else {
        echo "<div class='msg msg-right'>Connected to '" . DATABASE . "'</div>";
        echo "<form method='GET'><input type='submit' class='btn btn-reset btn-right' value='Drop database' name='dropDB'></form>";
    }
    showClassListNav();

}
function showClassListNav() {
    $classes = $_SESSION['data']['classes'];

    echo "<form method='GET'>
        <input class='btn' type='submit' name='class' value='*'>";
    foreach ($classes as $class) {
        echo "<input class='btn' type='submit' name='class' value='$class'>";
    }
    echo "<input class='btn btn-query' type='submit' name='statistics' value='Statistics'>";
}
function showStatisticsForm() {
    echo "<form method='GET'>
        <input class='btn btn-query' type='submit' name='statistics' value='Subject Averages'>
        <input class='btn btn-query' type='submit' name='statistics' value='Student Rankings'>
        <input class='btn btn-query' type='submit' name='statistics' value='Class Rankings'></form>";
}

function displayTable($class) {
    $students = getStudentsFromDB($class);
    $subjects = getSubjectsFromDB();
    $classID = getClassIdFromDB($class);

    // header
    echo "<table class='class-table'><tr><td class='table-class bold'>$class</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>".$subject['name']."</td>";
    }
    echo "<td class='td-invisible'></td>";
    echo "</tr>";

    $i = 1;
    foreach ($students as $student) {
        echo "<tr><td class='bold first-col'>$i. ".$student["lastname"]." ".$student["firstname"]." (".$student["gender"].")</td>";
        $i++;

        $gradesAvg = [];
        foreach ($subjects as $subject) {
            $grades = getSubjectGrades($student['id'], $subject['id']);

            if (count($grades->fetch_assoc()) > 0)
            {
                echo "<td>";
                foreach ($grades as $grade)
                {
                    $gradesAvg[] = $grade['grade'];
                    echo $grade['grade'] ." ";
                }
                echo "</td>";
            }
        }
        echo "<td>". getStudentAvg($student['id']) ."</td></tr>";
    }
    echo "<tr><td class='td-invisible'></td>";
    foreach ($subjects as $subject) {
        echo "<td>". getSubjectAvg($classID ,$subject['id'])."</td>";
    }
    echo "<td class='bold td-highlight'>".getClassAvg($classID)."</td>";
    echo "</tr></table>";
}
function displaySubjectAverages($subjects, $averages) {
    echo "<h2>School Subject Averages</h2>";

    echo "<table class='table-hover-disable'><tr><td class='table-title'>Subject</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>".$subject['name'],"</td>";
    }
    echo "</tr><tr><td class='table-title'>Average</td>";
    foreach ($averages as $average) {
        echo "<td>".$average."</td>";
    }
    echo "</tr></table>";
}

function displayBestWorstClasses($classRank) {
    echo "<h2>Cumulative</h2>";

    echo "<table class='table-hover-disable'><tr><td class='table-title'>Class</td>";
    foreach ($classRank as $class) {
        echo "<td class='bold'>".$class['class']."</td>";
    }
    echo "</tr><tr><td>Avg</td>";
    foreach ($classRank as $class) {
        echo "<td>".$class['g_avg']."</td>";
    }
    echo "</tr></table>";
}

function displayBestWorstClassesBySubject($bestRanking, $worstRaking, $subjects) {

    echo "<h2>By subject</h2><h3>Best</h3>";

    echo "<table class='table-hover-disable'><tr><td class='table-title'>Subject</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>".$subject['name']."</td>";
    }
    echo "</tr><tr><td class='table-title'>Class</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>".$bestRanking[$subject['name']]['class']."</td>";
    }
    echo "</tr><tr><td class='table-title'>Average</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>".$bestRanking[$subject['name']]['avg_grade']."</td>";
    }
    echo "</tr></table>";

    echo "<h3>Worst</h3>";

    echo "<table class='table-hover-disable'><tr><td class='table-title'>Subject</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>".$subject['name']."</td>";
    }
    echo "</tr><tr><td class='table-title'>Class</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>".$worstRaking[$subject['name']]['class']."</td>";
    }
    echo "</tr><tr><td class='table-title'>Average</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>".$worstRaking[$subject['name']]['avg_grade']."</td>";
    }
    echo "</tr></table>";
}