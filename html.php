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

    //echo "<form method='GET'><input class='btn btn-query' type='submit' name='statistics' value='Statistics'></form>";
}
function showStatisticsForm() {
    echo "<form method='GET'>
        <input class='btn btn-query' type='submit' name='statistics' value='Subject Averages'>
        <input class='btn btn-query' type='submit' name='statistics' value='Student Rankings'>
        <input class='btn btn-query' type='submit' name='statistics' value='Class Rankings'></form>";
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

function displayStudentRanking() {
    $subjects = getSubjectsFromDB();

    echo "<h2>Student Ranking</h2>";
    echo "<table class=''><tr><td class='bold'>Rank</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>".$subject['name']."</td>";
    }
    echo "<td class='bold'>Cumulative</td>";
    echo "</tr>";

    // TODO - CUMULATIVE VALUE CHECK
    $cumulativeTopFive = getCumulativeStudentRanking();
    for ($i = 1; $i <= 5; $i++) {
        echo "<tr><td class='bold'>$i.</td>";
        foreach ($subjects as $subject) {
            $topFive = getStudentRanking($subject['id']);
            if (isset($topFive[$i - 1])) {
                $student = $topFive[$i - 1];
                echo "<td>".$student['name']." ".$student['avg']."</td>";
            }
        }

        if (isset($cumulativeTopFive[$i - 1])) {
            $cumulative = $cumulativeTopFive[$i - 1];
            echo "<td>".$cumulative['name']." (".$cumulative['avg'].")</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
}