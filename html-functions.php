<?php
/**
 * @author Kolozsvári Barnabás
 * desc: code responsible for the site's HTML
*/

require_once "data-functions.php";
require_once "query-functions.php";

/**
 * Displays the HTML header
 * @return void
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
    </head>
    <body>
    
    </body>
    </html>";
}
/**
 * Display form containing class selector/rest/save/query buttons
 * @return void
 */
function showClassList() {
    $classes = $_SESSION['data']['classes'];

    // form & buttons
    echo "<form method='GET'>
        <input class='btn' type='submit' name='class' value='*'>";
    foreach ($classes as $class) {
        echo "<input class='btn' type='submit' name='class' value='$class'>";
    }
    echo "<input class='btn btn-reset' type='submit' name='reset' value='Reset students'>";
    if (isset($_GET['class'])) {
        echo "<input class='btn btn-save' type='submit' name='save' value='Save'>";
    }
    echo "<input class='btn btn-query' type='submit' name='query' value='Ranking'></form>";

    // states for buttons
    if (isset($_GET['reset'])) {
        session_destroy();
        showMessagebox("Student successfully reset");
    }
    if (isset($_GET['query'])) showQueryOptions();
}
/**
 * Displays a table containing class's students, their grades and overall individual average
 * @param $class string which class's table to display
 * @return void
 */
function displayTable($class) {
    $students = $_SESSION['students'];
    $subjects = $_SESSION['data']['subjects'];

    // header
    echo "<table><tr><td class='table-class bold'>$class</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>$subject</td>";
    }
    echo "<td class='bold'>Average</td>";
    echo "</tr>";
    
    $i = 1;
    foreach ($students as $student) {
        if ($student['class'] == $class) {
            // display student's index, full name, gender
            echo "<tr><td class='bold first-col'>$i. ".$student["lastname"]." ".$student["firstname"]." (".$student["gender"].")</td>";
            
            // display student's grades
            foreach ($subjects as $subject)
            {
                if (isset($student['grades'][$subject]))
                {
                    echo "<td>".join(", ",$student['grades'][$subject])."</td>";
                }
            }
            echo "<td>".$student['average']."</td>";
            echo "</tr>";
            $i++;
        }
    }
    echo "</table>";
}
/**
 * Shows a messagebox with a custom message
 * @param $msg string the message to be displayed
 * @param $type string "error" to add error msg style
 * @return void
 */
function showMessagebox($msg, $type="") {
    echo "<div class='messagebox $type'>
         <p>$msg</p>
        </div>";
}
/**
 * Displays the form with more query options, and a "Rank by: $msg"
 * @param $msg string the message to displayed
 * @return void
 */
function showQueryOptions($msg="--select--") {
    echo "<form method='GET'>
        <input class='btn btn-query' type='submit' name='subjectAverages' value='Subject averages'>
        <input class='btn btn-query' type='submit' name='studentRanking' value='Student ranking'>
        <input class='btn btn-query' type='submit' name='bestAndWorstClasses' value='Best and worst classes'></form>
        <p class='msg'>Rank by: $msg</p>";
}
/**
 * Displays cumulative school averages based on subjects
 * @return void
 */
function showSchoolAvgsTable() {
    $subjects = $_SESSION['data']['subjects'];
    
    // headers
    echo "<table><tr><td class='table-title' rowspan=2>School averages</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>$subject</td>";
    }
    echo "<td class='bold'>Average</td></tr><tr class='hover-disable'>";
    // data fields
    for ($i = 0; $i < count($subjects); $i++) {
        echo "<td>". getSchoolSubjectAvgs()[$subjects[$i]] ."</td>";
    }
    echo "<td>". round(array_sum(getSchoolSubjectAvgs()) / count(getSchoolSubjectAvgs()), 2) ."</td></tr></table>";
    echo "<form method='get'><input class='btn btn-save' type='submit' value='Save' name='saveSchoolAvgs'></form>";
}
/**
 * Displays class's subject/overall averages
 * @return void
 */
function showClassAvgsTable() {
    $subjects = $_SESSION['data']['subjects'];
    $classes = $_SESSION['data']['classes'];

    // header
    echo "<table><tr><td class='table-title'>Class averages</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>$subject</td>";
    }
    echo "<td class='bold'>Average</td></tr>";

    // data fields
    foreach ($classes as $class) {
        echo "<tr><td class='bold allcaps'>$class</td>";
        for ($i = 0; $i < count($subjects); $i++) {
            echo "<td>". getClassSubjectAvgs($class)[$subjects[$i]] ."</td>";
        }
        echo "<td>". getCumulativeClassAvg($class) ."</td></tr>";
    }
    echo "</table>";
    echo "<form method='get'><input class='btn btn-save' type='submit' value='Save' name='saveClassAvgs'></form>";
}
/**
 * Displays table containing the best and worst classes and their averages by subject/overall
 * @return void
 */
function showBestWorstClass() {
    $subjects = $_SESSION['data']['subjects'];
    $bestClass = getBestClassByAvg();
    $worstClass = getWorstClassByAvg();

    echo "<table><tr><td class='table-class'></td><td class='bold'>Overall</td>";
    foreach($subjects as $subject) {
        echo "<td class='bold'>$subject</td>";
    }
    // BEST
    echo "</tr><tr class='best-class'><td class='bold' rowspan='2'>Best</td><td class='bold allcaps'>$bestClass</td>";
    foreach($subjects as $subject) {
        echo "<td class='bold allcaps'>".getBestClassBySubjectAvg($subject)."</td>";
    }
    echo "</tr><tr class='best-class'><td class='italic'>".getCumulativeClassAvg($bestClass)."</td>";
    foreach($subjects as $subject) {
        echo "<td class='italic'>".getBestClassAvgBySubject($subject)."</td>"; 
    }
    // WORST
    echo "</tr><tr class='worst-class'><td class='bold' rowspan='2'>Worst</td><td class='bold allcaps'>$worstClass</td>";
    foreach($subjects as $subject) {
        echo "<td class='bold allcaps'>".getWorstClassBySubjectAvg($subject)."</td>";
    }
    echo "</tr><tr class='worst-class'><td>".getCumulativeClassAvg($worstClass)."</td>";
    foreach($subjects as $subject) {
        echo "<td class='italic'>".getWorstClassAvgBySubject($subject)."</td>"; 
    }
    echo "</tr></table>";
    echo "<form method='get'><input class='btn btn-save' type='submit' value='Save' name='saveBeWoClass'></form>";
}
/**
 * Displays all class's table containing student ranking based on subjects/overall
 * @return void
 */
function showCumulativeClassRankings() {
    $students = $_SESSION['students'];
    $subjects = $_SESSION['data']['subjects'];
    $classes = $_SESSION['data']['classes'];

    echo "<form method='get'><input class='btn btn-save' type='submit' value='Save all' name='saveClassRanking'></form>";
    foreach ($classes as $class) {
        $classRanks[$class] = getOrderedStudents($class);

        // table header
        echo "<h2 class='class-title'>$class</h2>
            <table class='class-ranking-table'><tr><td class='bold table-title'>Rank</td><td class='bold'>Overall</td>";
        foreach ($subjects as $subject) {
            echo "<td class='bold'>$subject</td>";
        }
        echo "</tr>";

        // data fields
        $j = 0;
        for ($i = 0; $i < count($students); $i++) {
            if ($students[$i]['class'] == $class) {
                echo "<tr><td class='bold'>". $j+1 .".</td><td>". array_keys($classRanks[$class])[$j]."</td>";
                foreach ($subjects as $subject) {
                    echo "<td>". array_keys(getOrderedClassBySubject($class, $subject))[$j] ."</td>";
                }
                $j++;
                echo"</tr>";
            }
        }
        echo "</table>";
    }
}
/**
 * Displays all students and their classes, ranked by subjects/overall
 * @return void
 */
function showSchoolRanking() {
    $students = $_SESSION['students'];
    $subjects = $_SESSION['data']['subjects'];

    $orderedSchool = getOrderedSchool();
    echo "<form method='get'><input class='btn btn-save' type='submit' value='Save' name='saveSchoolRanking'></form>";
    // table header
    echo "<h2 class='class-title'>All students</h2>
        <table class='class-ranking-table'><tr><td class='bold table-title'>Rank</td><td class='bold'>Overall</td>";
    foreach ($subjects as $subject) {
        echo "<td class='bold'>$subject</td>";
    }
    echo "</tr>";

    // data fields
    for ($i = 0; $i < count($orderedSchool); $i++) {
        // all grades
        echo "<tr><td class='bold'>". $i+1 .".</td><td>". array_keys($orderedSchool)[$i] ."</td>";

        // by subjects
        foreach ($subjects as $subject) {
            echo "<td>". array_keys(getOrderedSchoolBySubject($subject))[$i] ."</td>";
        }
        echo"</tr>";
    }
    echo "</table>";
}
/**
 * Displays the form containing further query options for ranking students (by class/whole school)
 * @return void
 */
function showStudentRankingOptions() {
    echo "<form method='GET'>
        <input class='btn btn-query' type='submit' name='rankClasses' value='Rank by classes'>
        <input class='btn btn-query' type='submit' name='rankSchool' value='Rank whole school'>
    </form>";
}