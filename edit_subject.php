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
        <title>Edit subject</title>
    </head><body>";


echo "<h2 style='text-align: center'>Edit subject</h2>";
displayEditSubjectForm(getSubjectDataByID($_GET["subjectID"]));


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit-subject-form"])) {
    switch ($_POST["edit-subject-form"]) {
        default:
        case "Cancel":
            redirectToAdmin("Subjects");
            break;

        case "OK":
            if ($_POST["subjectName"] == NULL) return;

            $subjectName = $_POST["subjectName"];
            $subjectID = $_POST["subjectID"];

            $sql = "UPDATE `subjects` SET `subject_name` = '$subjectName' WHERE `subjects`.`id` = $subjectID;";
            $res = updateRecord($sql);
            if ($res) {
                redirectToAdmin("Subjects");
            }
            else { echo "Error updating subject"; }
            break;
    }
}

echo "</body></html>";
