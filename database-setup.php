<?php

require_once "classroom-data.php";
require_once "data-functions.php";

session_start();

if(!isset($_SESSION['data'])){
    $_SESSION['data'] = getData();
}
function initDB() {
    $mysqli = new mysqli("localhost", "root", "");

    // TODO remove later
    $mysqli->query("DROP DATABASE IF EXISTS classroom;");

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
    $mysqli-> query("USE classroom;");

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
    $result = $mysqli->query("SELECT class_name, id FROM classes;");
    $classIDs = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $classIDs[$row['class_name']] = $row['id'];
        }
    }

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
    for ($i = 0; $i < count($students); $i++) {
        $grades = $students[$i]['grades'];
        for ($j = 0; $j < count($subjects); $j++) {
            foreach($grades[$subjects[$j]] as $grade) {
                $date = "2025-".random_int(1, 12)."-".random_int(1, 30);
                $mysqli->query("INSERT INTO grades (student_id, subject_id, grade, date)
                                                VALUES ('". $i+1 ."', '$j', '$grade', '$date');
                ");
            }
        }
    }

    $mysqli->close();
}
function deleteDB() {
    $mysqli = new mysqli("localhost", "root", "");
    $mysqli->query("DROP DATABASE IF EXISTS classroom");
    $mysqli->close();
}