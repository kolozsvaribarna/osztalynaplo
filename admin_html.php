<?php
require_once "db.php";
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
        <input class='btn btn-query' type='submit' name='return' value='Return to main'></form><br>";
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

function showClassListNav() {
    $classes = getClassesFromDB();
    echo "<br><form method='POST' action='admin.php'>
            <select name='year'>
              <option value='' selected disabled hidden>Évfolyam</option>
                <option value='2022' onselect=''>2022</option>
                <option value='2023'>2023</option>
                <option value='2024'>2024</option>
            </select>";

    foreach ($classes as $class) {
        echo "<input class='btn' type='submit' name='class' value='" . $class[0] . "'>";
    }
    echo "</form>";
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
function displayEditStudentForm($studentData) {
    $id = $studentData[0]['id'];
    $firstName = $studentData[0]['firstName'];
    $lastName = $studentData[0]['lastName'];
    $class = $studentData[0]['class'];
    $gender = $studentData[0]['gender'];

    echo "<form method='POST'>
    <section style='border: 1px solid black; border-radius: 10px; width: fit-content; padding: 30px; margin: 0 auto;'>
        <label for='studentID'>#</label>
        <input type='text' name='studentID' value='$id' readonly>
        <label for='class'>Class:</label>
        <input type='text' name='class' value='$class' readonly>  
        <label for='firstname'>Firstname:</label>
        <input type='text' name='firstname' value='$firstName'>   
        <label for='lastname'>Lastname:</label>
        <input type='text' name='lastname' value='$lastName'>
        <label for='gender'>Gender:</label>";
        if ($gender == "M") { echo "
            <label for='g1'>M</label>
            <input type='radio' id='g1' name='gender' value='M' checked>
            <input type='radio' id='g2' name='gender' value='F'>
            <label for='g2'>F</label>";
        }
        else { echo "
            <label for='g1'>M</label>
            <input type='radio' id='g1' name='gender' value='M'>
            <input type='radio' id='g2' name='gender' value='F' checked>
            <label for='g2'>F</label>";
        }
        echo "</section>
        <section style='border: 1px solid black; border-radius: 10px; width: fit-content; padding: 30px; margin: 15px auto;'>
            <input class='btn btn-save' type='submit' name='edit-student-form' value='OK'>
            <input class='btn btn-reset' type='submit' name='edit-student-form' value='Cancel'>
        </section>
        </form>";
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

function displayAddStudentForm() {
    $class = $_SESSION["class"];
    $year = $_SESSION["year"];

    echo "<form method='POST'>
    <section style='border: 1px solid black; border-radius: 10px; width: fit-content; padding: 30px; margin: 0 auto;'>
        <label for='studentID'>#</label>
        <input type='text' name='studentID' value='' readonly>
        <label for='class'>Class:</label>
        <input type='text' name='class' value='$class' readonly>  
        <label for='year'>Year:</label>
        <input type='text' name='year' value='$year' readonly>
        <label for='firstname'>Firstname:</label>
        <input type='text' name='firstname' value=''>   
        <label for='lastname'>Lastname:</label>
        <input type='text' name='lastname' value=''>
        <label for='gender'>Gender:</label>
        <label for='g1'>M</label>
        <input type='radio' id='g1' name='gender' value='M'>
        <input type='radio' id='g2' name='gender' value='F'>
        <label for='g2'>F</label>
    </section>
    <section style='border: 1px solid black; border-radius: 10px; width: fit-content; padding: 30px; margin: 15px auto;'>
        <input class='btn btn-save' type='submit' name='add-student-form' value='OK'>
        <input class='btn btn-reset' type='submit' name='add-student-form' value='Cancel'>
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
function displayClassTable($class, $year) {
    $classID = getClassIdFromDB($class, $year)->fetch_assoc()['id'];
    $students = getStudentsFromDB($classID);

    echo "<h2>$class - $year</h2>";
    echo "<form method='POST'><table class='class-table'><tr><td>#</td><td>name</td><td class='td-invisible min-width' colspan='2'><button type='submit' name='btn-add-student' value='add' class='btn-save'><i class='fa fa-plus' aria-hidden='true'></i></button></td></tr>";
    foreach ($students as $student) {
        echo "<td>".$student['id']."</td><td>".$student["name"]." (".$student["gender"].")</td>
            <td class='td-invisible min-width'>
                <button type='submit' name='btn-edit-student' value='".$student['id']."' class='btn-query btn-edit'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button>
            </td>
            <td class='td-invisible min-width'>
                <button type='submit' name='btn-delete-student' value='".$student['id']."' class='btn-reset btn-del'><i class='fa fa-trash' aria-hidden='true'></i></button>
            </td>
            <tr>";
    }
    echo "</table></form>";
}