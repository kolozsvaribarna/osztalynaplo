<?php

require_once "db.php";
require_once "admin_html.php";

echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' href='style.css' type='text/css'>
        <title>Add student</title>
    </head><body>";

echo "<h2 style='text-align: center;'>Add student</h2>";
displayAddStudentForm();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-student-form"])) {
    $class = $_POST["class"];
    $year = $_POST["year"];
    switch ($_POST["add-student-form"]) {
        default:
        case "Cancel":
            redirectToAdmin("Students");
            break;

        case "OK":
            if ($_POST["firstname"] == NULL || $_POST["lastname"] == NULL || $_POST["gender"] == NULL) {
                echo "Please fill out all the fields";
                return;
            }

            $classID = getClassIdFromDB($class, $year)->fetch_assoc()['id'];;
            $firstname = $_POST["firstname"];
            $lastname = $_POST["lastname"];
            $gender = $_POST["gender"];

            $sql = "INSERT INTO `students` (`class_id`, `firstname`, `lastname`, `gender`) VALUES ('$classID', '$firstname', '$lastname', '$gender');";
            $res = updateRecord($sql);
            if ($res) {
                redirectToAdmin("Students");
            }
            else echo "Error adding student";
    }
}


echo "</body></html>";