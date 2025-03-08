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
        <title>Add class</title>
    </head><body>";

echo "<h2 style='text-align: center;'>Add class</h2>";
displayAddClassForm();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-class-form"])) {
    switch ($_POST["add-class-form"]) {
        default:
        case "Cancel":
            redirectToAdmin("Classes");
            break;

        case "OK":
            if ($_POST["className"] == NULL || $_POST["classYear"] == NULL) {
                echo "Please fill out all the fields";
                return;
            }

            // check if class exists!!
            $classYear = $_POST["classYear"];
            $className = $_POST["className"];
            if (dbClassExists($className, $classYear)) {
                echo "Class $className for the year $classYear already exists, please add a new class!";
                return;
            }
            $sql = "INSERT INTO `classes` (`class_name`, `year`) VALUES ('$className', '$classYear');";
            $res = updateRecord($sql);
            if ($res) {
                redirectToAdmin("Classes");
            }
            else { echo "Error adding class"; }
            break;
    }
}

echo "</body></html>";