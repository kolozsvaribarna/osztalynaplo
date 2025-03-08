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
        <title>Edit class</title>
    </head><body>";

if (isset($_GET["class"])) {
    echo "<h2 style='text-align: center'>Edit class</h2>";
    displayEditClassForm(getClassDataFromID($_GET['class']));
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit-class-form"])) {
    switch ($_POST["edit-class-form"]) {
        default:
        case "Cancel":
            redirectToAdmin("Classes");
            break;

        case "OK":
            if ($_POST["className"] == NULL || $_POST["className"] == NULL) return;

            $className = $_POST["className"];
            $classID = $_POST["classID"];

            $sql = "UPDATE `classes` SET `class_name` = '$className' WHERE `classes`.`id` = $classID;";
            $res = updateRecord($sql);
            if ($res) {
                redirectToAdmin("Classes");
            }
            else { echo "Error updating class"; }
            break;
    }
}

echo "</body></html>";
