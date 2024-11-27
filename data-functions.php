<?php
/**
 * @author Kolozsvári Barnabás
 * disc: functions responsible for generating and saving data & managing the session
*/
session_start();

require_once "classroom-data.php";
require_once "query-functions.php";

if (!isset($_SESSION['data'])) {
    $_SESSION["data"] = getData();
}

if (!isset($_SESSION['students'])){
    generateStudents();
}

// save current class to session
if (!isset($_GET["save"]) && !isset($_GET['reset']) && isset($_GET['class'])){
    $_SESSION['activeClass'] = $_GET['class'];
}
// call saveData when save button clicked
elseif (isset($_GET["save"])) {
    saveData();
}

// generates students and saves it to session
function generateStudents() {
    $data = $_SESSION['data'];
    $classes = $data['classes'];
    $maleFistnames = $data['firstnames']['men'];
    $femaleFirstnames = $data['firstnames']['women'];

    // generate data for each class's students with random class length
    foreach ($classes as $class) {
        for ($i = 0; $i < random_int(10, 15); $i++) {
            if (random_int(0, 1) == 0) {
                $gender = 'M';
                $firstname = $maleFistnames[random_int(0, count($maleFistnames)-1)];
            }
            else {
                $gender = 'F';
                $firstname = $femaleFirstnames[random_int(0, count($femaleFirstnames)-1)];
            }
            $grades = getGrades();
            // adds all data to an array
            $students[] = [
                'class'=>"$class",
                'gender'=>$gender,
                'firstname'=>$firstname,
                'lastname'=>$data['lastnames'][random_int(0, count($data['lastnames'])-1)],
                'grades'=> $grades,
                'average' => getSingleStudentAverage($grades),
            ];
        }
    }
    $_SESSION['students'] = $students;
}

// returns array with generated grades
function getGrades() {
    $subjects = $_SESSION['data']['subjects'];

    // generate random number of grades grades between 1-5 for each subject
    foreach ($subjects as $subject) {
        if (random_int(0, 5) != 0) {
            for ($i = 0; $i < random_int(1, 5); $i++) {
                $grades[$subject][] = random_int(1, 5);
            }
        }
        // if zero grades are to be generated
        else $grades[$subject][] = "";
    }
    return $grades;
}

// write header & selected class to csv
function saveData() {
    $classes = $_SESSION['data']['classes'];
    $class = $_SESSION['activeClass'];
    
    // switch page to active class
    header("Location: index.php?class=$class");

    if (isset($class) && $class == "*") {
        $class = "all";
    }
    else if (!in_array($class, $classes)) return;

    // check 'export' folder
    if (!is_dir("export")) {
        mkdir("export");
    }

    $filename = "$class-".date("Y-m-d_Hi");
    $file = fopen("export\\$filename.csv", 'w');

    $header = ['ID','Name', 'Firstname', 'Lastname', 'Gender', 'Math', 'History', 'Biology', 'Chemistry', 'Physics', 'Informatics', 'Alchemy', 'Astrology'];      
    fputcsv($file,$header,";");

    if ($class == "all") {
        foreach ($classes as $c) {
            saveClassData($file, $c);
        }
        return;
    }
    saveClassData($file, $class);
}

// write class data to csv with no header
function saveClassData($file, $class) {
    $students = $_SESSION['students'];
    $subjects = $_SESSION['data']['subjects'];
    $j = 0;
    for ($i = 0; $i < count($students); $i++) {
        if ($class == $students[$i]['class']) {
            // sort data into arrays
            $lineData = ["$class-$j"
                        ,join(' ',[$students[$i]['lastname'],$students[$i]['firstname']])
                        ,$students[$i]['firstname']
                        ,$students[$i]['lastname']
                        ,$students[$i]['gender']
                    ];
            
            $tempGrades = [];
            $studentGrades = $students[$i]['grades'];
            for ($k = 0; $k < count($studentGrades); $k++) {
                $tempGrades[] = join(',',$studentGrades[$subjects[$k]]);
            }
            // write data to csv
            fputcsv($file,$lineData,';', eol:";");
            fputcsv($file, $tempGrades, ';');
            $j++;
        }
    }
}