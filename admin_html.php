<?php

function htmlHead() {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' href='style.css' type='text/css'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
        <title>Classroom</title>
    </head>";
}
function htmlStart() { echo "<body>"; }
function htmlEnd() { echo "</body></html>"; }
function displayReturnMainBtn() {
    echo "<form method='POST'>
        <input class='btn btn-query' type='submit' name='return' value='Return to main'></form>";
}
function displayEditCategoriesForm() {
    echo "<form method='POST'>
        <input class='btn btn-query' type='submit' name='edit_category' value='Classes'>
        <input class='btn btn-query' type='submit' name='edit_category' value='Students'>
        <input class='btn btn-query' type='submit' name='edit_category' value='Subjects'></form>";
}

function displayClassEdit() {
    $classes = getAllClassIDs();
    displayClassEditTable($classes);
}
function displaySubjectEdit() {
    $subjects = getSubjectsFromDB();
    displaySubjectEditTable($subjects);
}

function displayClassEditTable($classes) {
    echo "<form method='POST'><table><tr><td>#</td><td>Class</td><td>Year</td><td class='td-invisible' colspan='2'><button type='submit' name='btn-add-class' value='add' class='btn-save btn-add-class'><i class='fa fa-plus' aria-hidden='true'></i></button></td></tr>";
    foreach ($classes as $class_) {
        $id = $class_['id'];

        echo "<tr>
        <td>$id</td>
        <td>".$class_['class']."</td>
        <td>".$class_['year']."</td>
        <td class='min-width td-invisible'>
            <button type='submit' name='btn-edit-class' value='$id' class='btn-query btn-edit'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button>
        </td>
        <td class='min-width td-invisible'>
            <button type='submit' name='btn-delete-class' value='$id' class='btn-reset btn-del'><i class='fa fa-trash' aria-hidden='true'></i></button>
        </td></tr>";
    }
    echo "</table></form>";
}
function displaySubjectEditTable($subjects) {
    echo "<form method='POST'><table><tr><td>#</td><td>Name</td><td class='td-invisible' colspan='2'><button type='submit' name='btn-add-subject' value='add' class='btn-save btn-add-class'><i class='fa fa-plus' aria-hidden='true'></i></button></td></tr>";
    foreach ($subjects as $subject) {
        $id = $subject['id'];

        echo "<tr><td>$id</td><td>".$subject['name']."</td>

        <td class='min-width td-invisible'>
            <button type='submit' name='btn-edit-subject' value='$id' class='btn-query btn-edit'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button>
        </td>
        <td class='min-width td-invisible'>
            <button type='submit' name='btn-delete-subject' value='$id' class='btn-reset btn-del'><i class='fa fa-trash' aria-hidden='true'></i></button>
        </td></tr>";
    }
    echo "</table></form>";
}
function displayEditClassForm($classData) {
    $id = $classData[0]["id"];
    $className = $classData[0]["class"];
    $year = $classData[0]["year"];

    echo "<form method='POST'>
    <section style='border: 1px solid black; border-radius: 10px; width: fit-content; padding: 30px; margin: 0 auto;'>
        <label for='classID'>#</label>
        <input type='text' name='classID' value='$id' readonly>   
        <label for='className'>Class name:</label>
        <input type='text' name='className' value='$className'>
        <label for='classYear'>Class year:</label>
        <input type='text' name='classYear' value='$year' readonly>
    </section>
    <section style='border: 1px solid black; border-radius: 10px; width: fit-content; padding: 30px; margin: 15px auto;'>
        <input class='btn btn-save' type='submit' name='edit-class-form' value='OK'>
        <input class='btn btn-reset' type='submit' name='edit-class-form' value='Cancel'>
    </section>
    </form>";
}

function displayEditSubjectForm($subjectData) {
    $id = $subjectData[0]["id"];
    $name = $subjectData[0]["subject_name"];

    echo "<form method='POST'>
    <section style='border: 1px solid black; border-radius: 10px; width: fit-content; padding: 30px; margin: 0 auto;'>
        <label for='subjectID'>#</label>
        <input type='text' name='subjectID' value='$id' readonly>   
        <label for='subjectName'>Subject name:</label>
        <input type='text' name='subjectName' value='$name'>
    </section>
    <section style='border: 1px solid black; border-radius: 10px; width: fit-content; padding: 30px; margin: 15px auto;'>
        <input class='btn btn-save' type='submit' name='edit-subject-form' value='OK'>
        <input class='btn btn-reset' type='submit' name='edit-subject-form' value='Cancel'>
    </section>
    </form>";
}

function displayAddClassForm() {
    echo "<form method='POST'>
    <section style='border: 1px solid black; border-radius: 10px; width: fit-content; padding: 30px; margin: 0 auto;'>
        <label for='classID'>#</label>
        <input type='text' name='classID' value='' disabled>   
        <label for='className'>Class name:</label>
        <input type='text' name='className' value=''>
        <label for='classYear'>Class year:</label>
        <input type='text' name='classYear' value=''>
    </section>
    <section style='border: 1px solid black; border-radius: 10px; width: fit-content; padding: 30px; margin: 15px auto;'>
        <input class='btn btn-save' type='submit' name='add-class-form' value='OK'>
        <input class='btn btn-reset' type='submit' name='add-class-form' value='Cancel'>
    </section>
    </form>";
}

function displayAddSubjectForm() {
    echo "<form method='POST'>
    <section style='border: 1px solid black; border-radius: 10px; width: fit-content; padding: 30px; margin: 0 auto;'>
        <label for='subjectID'>#</label>
        <input type='text' name='subjectID' value='' disabled>   
        <label for='subjectName'>Subject name:</label>
        <input type='text' name='subjectName' value=''>
    </section>
    <section style='border: 1px solid black; border-radius: 10px; width: fit-content; padding: 30px; margin: 15px auto;'>
        <input class='btn btn-save' type='submit' name='add-subject-form' value='OK'>
        <input class='btn btn-reset' type='submit' name='add-subject-form' value='Cancel'>
    </section>
    </form>";
}

function redirectToAdmin($category) {
    echo '<form id="redirectForm" action="admin.php" method="POST">';
    echo "<input type='hidden' name='edit_category' value='$category'>";
    foreach ($_POST as $key => $value) {
        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
    }
    echo '</form>';
    echo '<script>document.getElementById("redirectForm").submit();</script>';
    exit;
}