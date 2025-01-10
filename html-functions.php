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
    </head>
    <body>
    
    </body>
    </html>";
}
function showDBInitBtn() {
    echo "<form method='GET' action='$_SERVER[PHP_SELF]'>
        <input type='submit' class='btn btn-save' value='Create database' name='initDB'>
        </form>";

    if (isset($_GET['initDB'])) {
        initDB();
        uploadDB();
    }
}
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
        deleteDB();
        initDB();
        uploadDB();
        echo "<p class='msg'>database (students) reset successfully :-)<p/>";
    }
    if (isset($_GET['query'])) echo "query selected :-)";
}

function displayTable($class) {
    $mysqli = new mysqli('localhost', 'root', '', 'classroom');

    $students = $mysqli->query("SELECT s.id as id, s.firstname as firstname, s.lastname as lastname, s.gender as gender, class_name as class
                                        FROM students s 
                                        JOIN classes c ON s.class_id = c.id 
                                        WHERE c.class_name = '$class';");

    $subjects = $mysqli->query("SELECT subject_name AS name, id FROM subjects");

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

        // display student's grades and average
        $gradesAvg = [];
        foreach ($subjects as $subject)
        {
            $grades = $mysqli->query("SELECT g.grade
                                        FROM grades g
                                        JOIN students st ON g.student_id = st.id
                                        JOIN subjects su ON g.subject_id = su.id
                                        WHERE g.student_id=".$student['id']." AND g.subject_id= ".$subject['id'].";");

            if (array_sum($grades->fetch_assoc()) > 0)
            {
                echo "<td>";
                foreach ($grades as $grade)
                {
                    echo $grade['grade'] ." ";
                    $gradesAvg[] = $grade['grade'];
                }
                echo "</td>";
            }
            else echo "<td>-</td>";
        }
        // cumulative average
        echo "<td>".round(array_sum($gradesAvg) / count($gradesAvg),2)."</td></tr>";
        $i++;
    }
    echo "</table>";
    $mysqli->close();
}