<?php

require_once "classroom-data.php";
require_once "data-functions.php";

session_start();

const SERVER = "localhost";
const USERNAME = "root";
const PASSWORD = "admin";
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
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD);
    $mysqli->query("CREATE DATABASE " . DATABASE . " ;");
    $mysqli->query("USE " . DATABASE. " ;");

    // CLASSES TABLE
    $mysqli->query("CREATE TABLE classes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    class_name VARCHAR(3) NOT NULL,
                    year INT NOT NULL);");

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
    uploadDB();
}
function uploadDB() {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $mysqli->query("USE " . DATABASE .";");

    // CLASSES TABLE
    $year = 2022;
    for ($i = 0; $i < 3; $i++) {
        foreach ($_SESSION['data']['classes'] as $class) {
            $mysqli->query("INSERT INTO classes (class_name, year) VALUES ('$class', '$year');");
        }
        $year++;
    }

    // SUBJECTS TABLE
    foreach ($_SESSION['data']['subjects'] as $subject) {
        $mysqli->query("INSERT INTO subjects (subject_name) VALUES ('$subject');");
    }

    // STUDENTS TABLE
    $classes = $mysqli->query("SELECT class_name, year, id FROM classes;")->fetch_all(MYSQLI_ASSOC);
    foreach ($classes as $class) {
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
                $date = "202".random_int(3, 4)."-".random_int(1, 12)."-".random_int(1, 30);
                $mysqli->query("INSERT INTO grades (student_id, subject_id, grade, date)
                                                VALUES ('". $i + 1 ."', '".$j+1 ."', '$grade', '$date');");
            }
        }
    }
    $mysqli->close();
}

/* functions using the database */
function getStudentsFromDB($classID) {
    $mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT s.id as id, CONCAT(lastname, ' ', firstname) as name, s.gender as gender, class_name as class
                                        FROM students s 
                                        JOIN classes c ON s.class_id = c.id 
                                        WHERE s.class_id = $classID
                                ORDER BY 2 ASC;");
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
function getStudentAvgBySubject($id, $subjectID) {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT ROUND(AVG(grades.grade), 2) as 'avg' 
                                FROM students
                                JOIN grades ON grades.student_id=students.id
                                WHERE grades.subject_id=$subjectID AND students.id=$id;")
        ->fetch_assoc()['avg'];
    return $res;
}
function getClassIdFromDB($class, $year) {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT id FROM classes WHERE class_name= '$class' AND `year` = $year ;");
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
                                WHERE students.class_id=$classID ;")
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

function getBestClassID() {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT c.id
FROM classes c JOIN students s ON s.class_id = c.id
JOIN grades g ON g.student_id = s.id
GROUP BY c.class_name, c.year
ORDER BY AVG(g.grade) DESC
LIMIT 1;");
    $mysqli->close();
    return $res->fetch_assoc()['id'];
}
function getBestClass($id = null) {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    if (!$id) {
        $id = getBestClassID();
    }
    $res = $mysqli->query("SELECT class_name as class, `year` FROM classes WHERE id = $id;");
    $mysqli->close();
    return $res->fetch_assoc();
}

function get10BestStudents() {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT CONCAT(s.lastname, ' ', s.firstname) as name, c.class_name as class, c.year, ROUND(AVG(g.grade), 2) as avg
FROM students s JOIN grades g ON g.student_id = s.id JOIN classes c ON s.class_id = c.id
GROUP BY 1
ORDER BY AVG(g.grade) DESC
LIMIT 10
;");
    $mysqli->close();
    return $res->fetch_all(MYSQLI_ASSOC);
}

function get10BestStudentsByYear($year) {
    $mysqli = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
    $res = $mysqli->query("SELECT CONCAT(s.lastname, ' ', s.firstname) as name, c.class_name as class, ROUND(AVG(g.grade), 2) as avg
FROM students s JOIN grades g ON g.student_id = s.id JOIN classes c ON s.class_id = c.id
WHERE c.year = $year
GROUP BY 1
ORDER BY AVG(g.grade) DESC
LIMIT 10
;");
    $mysqli->close();
    return $res->fetch_all(MYSQLI_ASSOC);
}