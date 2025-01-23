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

    return $mysqli->query("SELECT g.grade
                                        FROM grades g
                                        JOIN students st ON g.student_id = st.id
                                        JOIN subjects su ON g.subject_id = su.id
                                        WHERE g.student_id=$studentID AND g.subject_id=$subjectID;");
}