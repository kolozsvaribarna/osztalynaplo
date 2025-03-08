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
        <title>Edit student</title>
    </head><body>";

if (isset($_GET["studentID"])) {
    echo "<h2 style='text-align: center'>Edit student</h2>";
    displayEditStudentForm(getStudentDataFromID($_GET['studentID']));
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit-student-form"])) {
    switch ($_POST["edit-student-form"]) {
        default:
        case "Cancel":
            redirectToAdmin("Students");
            break;

        case "OK":
            if ($_POST["firstname"] == NULL || $_POST["lastname"] == NULL || $_POST["gender"] == NULL) return;

            $id = $_POST["studentID"];
            $firstName = $_POST["firstname"];
            $lastName = $_POST["lastname"];
            $gender = $_POST["gender"];

            $sql = "UPDATE `students` SET `firstname` = '$firstName', `lastname` = '$lastName', `gender` = '$gender' WHERE `id` = $id;";
            $res = updateRecord($sql);
            echo "ok";
            if ($res) {
                redirectToAdmin("Students");
            }
            else { echo "Error updating student"; }
            break;
    }
}

echo "</body></html>";
