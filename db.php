<?php

require_once "classroom-data.php";
require_once "data-functions.php";

session_start();

const SERVER = "localhost";
const USERNAME = "root";
const PASSWORD = "";
const DATABASE = "classroom";

$_SESSION['data'] = $_SESSION['data'] ?? getData();
$_SESSION['database'] = $_SESSION['database'] ?? DATABASE;

function dbExists($dbName = DATABASE): bool {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD);
    $res = $mysqli->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".DATABASE."';");
    $mysqli->close();
    return $res->num_rows < 1;
}
function dropDB($dbName = DATABASE) {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD);
    $mysqli->query("DROP DATABASE IF EXISTS ".$dbName.";");
    $mysqli->close();
}
function createDB() {
    dropDB();
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD);
    $mysqli->query("CREATE DATABASE " . DATABASE . " CHARACTER SET utf8 COLLATE UTF8_HUNGARIAN_CI;");
    $mysqli->query("USE " . DATABASE. ";");

    // CLASSES TABLE
    $mysqli->query("CREATE TABLE classes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    class_name VARCHAR(3) NOT NULL);");

    // SUBJECTS TABLE
    $mysqli->query("CREATE TABLE subjects (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    subject_name VARCHAR(20) NOT NULL);");

    // STUDENTS TABLE
    $mysqli->query("CREATE TABLE students (
                    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
                    class_id INT NOT NULL,
                    firstname VARCHAR(30) NOT NULL,
                    lastname VARCHAR(30) NOT NULL,
                    gender VARCHAR(1) NOT NULL);");

    // GRADES TABLE
    $mysqli->query("CREATE TABLE grades (
                    student_id INT NOT NULL,
                    subject_id INT NOT NULL,
                    grade INT NOT NULL,
                    date DATE);");

    $mysqli->close();
}
function uploadDB() {
    if (!dbExists()) {
        createDB();
    }
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $mysqli->query("USE " . DATABASE .";");

    // CLASSES TABLE
    foreach ($_SESSION['data']['classes'] as $class) {
        $mysqli->query("INSERT INTO classes (class_name) VALUES ('$class');");
    }

    // SUBJECTS TABLE
    foreach ($_SESSION['data']['subjects'] as $subject) {
        $mysqli->query("INSERT INTO subjects (subject_name) VALUES ('$subject');");
    }

    // STUDENTS TABLE
    foreach ($mysqli->query("SELECT class_name, id FROM classes;") as $class) {
        for ($i = 0; $i < random_int(10, 15); $i++) {
            $student = generateSingleStudent();
            $mysqli->query("INSERT INTO students (class_id, firstname, lastname, gender)
                    VALUES ('".$class["id"]."','".$student['firstname']."','".$student['lastname']."','".$student['gender']."');");
        }
    }

    // GRADES TABLE
    $subjectsCount = $mysqli->query("SELECT COUNT(*) FROM subjects;")->fetch_row()[0];
    $studentCount = $mysqli->query("SELECT COUNT(*) FROM students;")->fetch_row()[0];

    for ($i = 0; $i < $studentCount; $i++) {
        for ($j = 0; $j < $subjectsCount; $j++) {
            foreach(generateGrades() as $grade) {
                $date = "2025-".random_int(1, 12)."-".random_int(1, 30);
                $mysqli->query("INSERT INTO grades (student_id, subject_id, grade, date)
                                                VALUES ('". $i + 1 ."', '".$j+1 ."', '$grade', '$date');");
            }
        }
    }
    $mysqli->close();
}

/* functions using the database */
function getStudentsFromDB($class) {
    $mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT s.id as id, s.firstname as firstname, s.lastname as lastname, s.gender as gender, class_name as class
                                        FROM students s 
                                        JOIN classes c ON s.class_id = c.id 
                                        WHERE c.class_name = '$class';");
    $mysqli->close();
    return $res;
}
function getSubjectsFromDB() {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT subject_name AS name, id FROM subjects");
    $mysqli->close();
    return $res;
}
function getSubjectGrades($studentID, $subjectID) {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT g.grade
                                FROM grades g
                                JOIN students st ON g.student_id = st.id
                                JOIN subjects su ON g.subject_id = su.id
                                WHERE g.student_id=$studentID AND g.subject_id=$subjectID;");
    $mysqli->close();
    return $res;
}
function getStudentAvg($id) {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT ROUND(AVG(grade), 2) as 'avg'  FROM `grades` WHERE student_id=$id;");
    $mysqli->close();
    return $res->fetch_assoc()['avg'];
}
function getClassIdFromDB($class) {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT id FROM classes WHERE class_name='$class';")->fetch_assoc()['id'];
    $mysqli->close();
    return $res;
}
function getSubjectAvg($classID, $subjectID) {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT ROUND(AVG(grades.grade), 2) as 'avg' 
                                FROM students
                                JOIN grades ON grades.student_id=students.id
                                WHERE grades.subject_id=$subjectID AND students.class_id=$classID;")
                  ->fetch_assoc()['avg'];
    $mysqli->close();
    return $res;
}
function getClassAvg($classID) {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT ROUND(AVG(grades.grade), 2) as 'avg' 
                                FROM grades
                                JOIN students ON grades.student_id=students.id
                                JOIN classes ON students.class_id=classes.id
                                WHERE students.class_id=$classID;")
        ->fetch_assoc()['avg'];
    $mysqli->close();
    return $res;
}
function getSubjectAvgsSchool() {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $subjects = $mysqli->query("SELECT subject_name as name, id FROM subjects;");
    foreach ($subjects as $subject) {
        $avgs[] = $mysqli->query("SELECT ROUND(AVG(grades.grade), 2) as 'avg' FROM grades
                                            WHERE grades.subject_id=".$subject['id'].";")->fetch_assoc()['avg'];
    }
    mysqli_close($mysqli);
    return $avgs;
}
function getClassRankingByAvg() {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT c.class_name as class, ROUND(AVG(g.grade), 2) AS g_avg
                    FROM classes c
                    JOIN students s ON c.id = s.class_id
                    JOIN grades g ON s.id = g.student_id
                    GROUP BY c.id, c.class_name
                    ORDER BY g_avg DESC;");
    $mysqli->close();
    return $res->fetch_all(MYSQLI_ASSOC);
}
function getClassRakingBySubjectAverage($mode = "DESC") {
    $subjects = getSubjectsFromDB();
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    foreach ($subjects as $subject) {
        $res[$subject['name']] = $mysqli->query("SELECT c.class_name as class, ROUND(AVG(g.grade), 2) AS avg_grade
                                            FROM classes c
                                            JOIN students s ON c.id = s.class_id
                                            JOIN grades g ON s.id = g.student_id
                                            WHERE g.subject_id = ".$subject['id']."
                                            GROUP BY c.id, c.class_name
                                            ORDER BY avg_grade ".$mode."
                                            LIMIT 1;")->fetch_assoc();
    }
     $mysqli->close();
     return $res;
}
/*
- tanulók rangsorolása iskolai és osztály szinten, tantárgyanként és összesítve, kiemelve a 3 legjobb és a 3 leggyengébb tanulót
*/