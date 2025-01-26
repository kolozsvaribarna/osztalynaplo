<?php

require_once "classroom-data.php";

function generateSingleStudent() {
    $data = $_SESSION['data'];
    $maleFirstnames = $data['firstnames']['men'];
    $femaleFirstnames = $data['firstnames']['women'];

    if (random_int(0, 1) == 0) {
        $gender = 'M';
        $firstname = $maleFirstnames[random_int(0, count($maleFirstnames)-1)];
    }
    else {
        $gender = 'F';
        $firstname = $femaleFirstnames[random_int(0, count($femaleFirstnames)-1)];
    }
    return [
        'gender'=>$gender,
        'firstname'=>$firstname,
        'lastname'=>$data['lastnames'][random_int(0, count($data['lastnames'])-1)],
    ];
}
function generateGrades() {
    for ($i = 0; $i < random_int(3, 5); $i++) {
        $grades[] = random_int(1, 5);
    }
    return $grades;
}