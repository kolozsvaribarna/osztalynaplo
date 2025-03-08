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
        <title>Add subject</title>
    </head><body>";

echo "<h2 style='text-align: center;'>Add subject</h2>";
displayAddSubjectForm();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add-subject-form"])) {
    switch ($_POST["add-subject-form"]) {
        default:
        case "Cancel":
            redirectToAdmin("Subjects");
            break;

        case "OK":
            if ($_POST["subjectName"] == NULL) {
                echo "Please fill out all the fields";
                return;
            }

            if (!dbSubjectExists($_POST["subjectName"])) {
                $subjectName = $_POST["subjectName"];
                $id = getMaxID("subjects")[0]["max"] + 1;

                $sql = "INSERT INTO `subjects` (id, subject_name) VALUES ('$id', '$subjectName');";
                $res = updateRecord($sql);
                if ($res) {
                    redirectToAdmin("Subjects");
                }
                else { echo "Error adding subject"; }
            }
            else { echo "Subject already exists!"; }
    }
}

echo "</body></html>";