<?php

require_once "classroom-data.php";
require_once "data-functions.php";

session_start();

if(!isset($_SESSION['data'])){
    $_SESSION['data'] = getData();
}

function initDB() {
    $mysqli = new mysqli("localhost", "root", "");

    $mysqli->query("DROP DATABASE classroom");

    $mysqli->query("CREATE DATABASE classroom CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
    $mysqli->query("USE classroom;");

    // CLASSES TABLE
    $mysqli->query("CREATE TABLE classes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    class_name VARCHAR(3) NOT NULL);
    ");
    // SUBJECTS TABLE
    $mysqli->query("CREATE TABLE subjects (
                    id INT PRIMARY KEY NOT NULL,
                    subject_name VARCHAR(20) NOT NULL);
    ");
    // STUDENTS TABLE
    $mysqli->query("CREATE TABLE students (
                    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
                    class_id INT NOT NULL,
                    firstname VARCHAR(30) NOT NULL,
                    lastname VARCHAR(30) NOT NULL,
                    gender VARCHAR(1) NOT NULL);       
    ");
    // GRADES TABLE
    $mysqli->query("CREATE TABLE grades (
                    student_id INT NOT NULL,
                    subject_id INT NOT NULL,
                    grade INT NOT NULL,
                    date DATE);
    ");
    $mysqli->close();
}
function uploadDB() {
    $mysqli = new mysqli("localhost", "root", "");
    $mysqli->query("USE classroom;");

    // CLASSES TABLE
    foreach ($_SESSION['data']['classes'] as $class) {
        $mysqli->query("INSERT INTO classes (class_name) VALUES ('$class');");
    }

    // SUBJECTS TABLE
    $subjects = $_SESSION['data']['subjects'];
    for ($i = 0; $i < count($subjects); $i++) {
        $mysqli->query("INSERT INTO subjects (id, subject_name)
                                    VALUES ('$i', '$subjects[$i]');
        ");
    }

    // STUDENTS TABLE
    $classIDs = ['11a' => 1,
                '11b' => 2,
                '11c' => 3,
                '12a' => 4,
                '12b' => 5,
                '12c' => 6,];
    $students = generateStudents();

    for ($i = 0; $i < count($students); $i++) {
        $class_id = $classIDs[$students[$i]['class']];
        $firstname = $students[$i]['firstname'];
        $lastname = $students[$i]['lastname'];
        $gender = $students[$i]['gender'];

        $mysqli->query("INSERT INTO students (class_id, firstname, lastname, gender)
                        VALUES ('$class_id', '$firstname', '$lastname', '$gender');
        ");
    }

    // GRADES TABLE
    /*$mysqli->query("INSERT INTO grades (student_id, subject_id, grade, date)
                            VALUES ('1', '0', '5', null),
                                   ('2', '4', '3', '2024-10-10');
    ");*/

    $mysqli->close();
}

function getAllStudents() {
    $mysqli = new mysqli("localhost", "root", "");
    $mysqli->query("USE classroom;");

    // returns (student_id, firstname, lastname, gender, class) as assoc array
    $allStudents = $mysqli->query("SELECT s.id, s.firstname, s.lastname, s.gender, c.class_name
                          FROM students s
                          JOIN classes c
                          ON s.class_id=c.id;
    ");
    $mysqli->close();
    return $allStudents;
}