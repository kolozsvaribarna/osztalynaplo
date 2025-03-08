<?php

require_once "admin.php";
require_once "admin_html.php";

function handle()
{
    if (isset($_POST["return"])) {
        echo "return";
        header("Location: index.php");
    }

    if (isset($_POST["edit_category"])) {
        displayEditCategoriesForm();
        switch ($_POST["edit_category"]) {
            case "Classes":
                displayClassEdit();
                break;
            case "Students":
                echo "edit student";
                break;
            case "Subjects":
                displaySubjectEdit();
                break;
            default:
                break;
        }
    }

    // class edit button
    if (isset($_POST["btn-edit-class"])) {
        header("Location: edit_class.php?class=".$_POST["btn-edit-class"]);
    }
    // class delete button
    if (isset($_POST["btn-delete-class"])) {
        $sql = "DELETE FROM classes WHERE id=".$_POST["btn-delete-class"];
        $success = updateRecord($sql);

        if (!$success) echo "Error deleting class";
    }
    // class add button
    if(isset($_POST["btn-add-class"])){
        header("Location: add_class.php");
    }

    // subject edit button
    if (isset($_POST["btn-edit-subject"])) {
        header("Location: edit_subject.php?subjectID=".$_POST["btn-edit-subject"]);
    }
    // subject delete button
    if (isset($_POST["btn-delete-subject"])) {
        $sql = "DELETE FROM subjects WHERE id=".$_POST["btn-delete-subject"];
        $success = updateRecord($sql);

        if (!$success) echo "Error deleting subject";
    }
    // subject add button
    if(isset($_POST["btn-add-subject"])){
        header("Location: add_subject.php");
    }
}
