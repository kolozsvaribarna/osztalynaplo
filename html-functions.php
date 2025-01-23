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
    showClassListNav();

    if (DBexists($_SESSION['database'])) {
        echo "<p class='msg-error msg-right'>No active database connection!</p>";
        echo "<form method='GET'><input type='submit' class='btn btn-save' value='Create database' name='initDB'></form>";
    }
    else {
        echo "<p class='msg msg-right'>Connected to '" . DATABASE . "'</p>";
        echo "<form method='GET'><input type='submit' class='btn btn-reset btn-right' value='Drop database' name='dropDB'></form>";
    }
}
function showClassListNav() {
    $classes = $_SESSION['data']['classes'];

    echo "<form method='GET'>
        <input class='btn' type='submit' name='class' value='*'>";
    foreach ($classes as $class) {
        echo "<input class='btn' type='submit' name='class' value='$class'>";
    }
    //echo "<input class='btn btn-query' type='submit' name='statistics' value='Statistics'>";

    if (isset($_GET['statistics'])) showStatisticsForm();
}

function showStatisticsForm() {
    echo "<form method='GET' action='$_SERVER[PHP_SELF]'>
        <input class='btn btn-query' type='submit' name='statistic' value='Subject Averages'>
        <input class='btn btn-query' type='submit' name='statistic' value='Student Rankings'>
        <input class='btn btn-query' type='submit' name='statistic' value='Class Rankings'>
        ";
    // get requests for queries go here
    echo "</form>";
}

function displayTable($class) {
    $students = getStudentsFromDB($class);
    $subjects = getSubjectsFromDB();

    // header
    echo "<table><tr><td class='table-class bold'>$class</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>".$subject['name']."</td>";
    }
    echo "<td class='bold'>Average</td>";
    echo "</tr>";

    $i = 1;
    foreach ($students as $student) {
        // display student's index, full name, gender
        echo "<tr><td class='bold first-col'>$i. ".$student["lastname"]." ".$student["firstname"]." (".$student["gender"].")</td>";
        $i++;

        // display student's grades and calculate average
        $gradesAvg = [];
        foreach ($subjects as $subject) {
            $grades = getSubjectGrades($student['id'], $subject['id']);

            // TODO test if grades is empty
            if (count($grades->fetch_assoc()) > 0)
            {
                echo "<td>";
                foreach ($grades as $grade)
                {
                    if ($grade['grade'] != 0)
                    {
                        $gradesAvg[] = $grade['grade'];
                        echo $grade['grade'] ." ";
                    }
                }
                echo "</td>";
            }
        }
        echo "<td></td></tr>";
        //echo "<td>". getStudentAvg($student['id']) ."</td></tr>";
    }
    echo "</table>";
}