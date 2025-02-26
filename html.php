<?php
/**
 * @author Kolozsvári Barnabás
 * desc: code responsible for the site's HTML
*/
require_once "db.php";

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
    if (DBexists()) {
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
            <select name='year'>
              <option value='' selected disabled hidden>Évfolyam</option>
                <option value='2022' onselect=''>2022</option>
                <option value='2023'>2023</option>
                <option value='2024'>2024</option>
            </select>";

        echo "<input class='btn' type='submit' name='class' value='*'>";
        foreach ($classes as $class) {
            echo "<input class='btn' type='submit' name='class' value='$class'>";
        }

    echo "</form>";

    echo "<form method='GET'><input class='btn btn-query' type='submit' name='statistics' value='Statistics'></form>";
}
function showStatisticsForm() {
    echo "<form method='GET'>
        <input class='btn btn-query' type='submit' name='statistics' value='Hall of Fame'>
        <input class='btn btn-query' type='submit' name='statistics' value='Best students by year'></form>";
}
function displayTable($class, $year) {
    $subjects = getSubjectsFromDB();
    $classID = getClassIdFromDB($class, $year)->fetch_assoc()['id'];
    $students = getStudentsFromDB($classID);

    // header
    echo "<table class='class-table'><tr><td class='table-class bold'>$class</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold' colspan='2'>".$subject['name']."</td>";
    }
    echo "<td class='bold'>Average</td>";
    echo "</tr>";

    $i = 1;
    foreach ($students as $student) {
        echo "<tr><td class='bold first-col'>$i. ".$student["name"]." (".$student["gender"].")</td>";
        $i++;

        foreach ($subjects as $subject) {
            $grades = getSubjectGrades($student['id'], $subject['id']);
            echo "<td>";
            foreach ($grades as $grade)
            {
                echo $grade['grade'] ." ";
            }
            echo "</td>";

            echo "<td class='italic half-width'>".getStudentAvgBySubject($student['id'], $subject['id'])."</td>";
        }
        echo "<td class='half-width italic'>". getStudentAvg($student['id']) ."</td></tr>";
    }
    echo "<tr><td class='td-invisible'></td>";
    foreach ($subjects as $subject) {
        echo "<td colspan='2'>". getSubjectAvg($classID ,$subject['id'])."</td>";
    }
    echo "<td class='bold td-highlight italic'>".getClassAvg($classID)."</td>";
    echo "</tr></table>";
}

function displayHallOfFame() {
    $bestClass = getBestClass();
    $students = get10BestStudents();

    echo "<h2>Hall of Fame</h2>";

    echo "<h3>Best class of all time: ".$bestClass['class']." (".$bestClass['year'].")</h3>";

    echo "<h3>Best 10 students</h3>";
    echo "<table class='class-table'><tr><td>Name</td><td>Class</td><td>Year</td><td>Average</td></tr>";
    $i = 1;
    foreach ($students as $student) {
        echo "<tr><td class='first-col'>$i. ".$student['name']."</td><td>".$student['class']."</td><td>".$student['year']."</td><td class='italic'>".$student['avg']."</td></tr>";
        $i++;
    }
    echo "</table>";
}

function displayBest10StudentsByYear() {
    $years = [2022, 2023, 2024];
    foreach ($years as $year) {
        $students = get10BestStudentsByYear($year);
        echo "<h3>$year</h3>";
        echo "<table class='class-table'><tr><td>Name</td><td>Class</td><td>Average</td></tr>";
        $i = 1;
        foreach ($students as $student) {
            echo "<tr><td class='first-col'>$i. ".$student['name']."</td><td>".$student['class']."</td><td class='italic'>".$student['avg']."</td></tr>";
            $i++;
        }
        echo "</table>";
    }
}
