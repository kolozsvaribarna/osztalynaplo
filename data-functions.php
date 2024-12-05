<?php

require_once "classroom-data.php";

function generateStudents() {
    $data = $_SESSION['data'];
    $classes = $data['classes'];
    $maleFirstnames = $data['firstnames']['men'];
    $femaleFirstnames = $data['firstnames']['women'];

    // generate data for each class's students with random class length
    foreach ($classes as $class) {
        for ($i = 0; $i < random_int(10, 15); $i++) {
            if (random_int(0, 1) == 0) {
                $gender = 'M';
                $firstname = $maleFirstnames[random_int(0, count($maleFirstnames)-1)];
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
            ];
        }
    }
    return $students;
}
function getGrades() {
    $subjects = $_SESSION['data']['subjects'];

    // generate random number of grades between 1-5 for each subject
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