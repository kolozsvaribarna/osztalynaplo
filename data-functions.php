<?php
/**
 * @author Kolozsvári Barnabás
 * desc: functions responsible for generating and saving data & managing the session
*/
session_start();

require_once "classroom-data.php";
require_once "query-functions.php";
require_once "state-actions.php";

/**
 * Generates student data and saves it to $_SESSION['students'] <br>
 * data: class, gender, firstname, lastname, grades[], averages[], average
 * @return void
 */
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
                'averages' => getStudentDistinctAverages($grades),
                'average' => getSingleStudentAverage($grades),
            ];
        }
    }
    $_SESSION['students'] = $students;
}
/**
 * Generates 0..5 grades between 1..5 for all subjects
 * @return array containing grades
 */
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
/**
 * @param array $grades containing one student's grades
 * @return array containing averages by subjects
 */
function getStudentDistinctAverages($grades) {
    $subjects = $_SESSION['data']['subjects'];

    foreach ($subjects as $subject) {
        if (count($grades[$subject]) > 1) {
            $averages[$subject] = round(array_sum($grades[$subject]) / count($grades[$subject]), 2);
        }
        else $averages[$subject] = 0;
    }
    return $averages;
}
/**
 * Writes $_SESSION['activeClass']'s student data to a .csv file with header
 * @return void
 */
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
/**
 * Helper-function for saveData().
 * Writes class data to a .csv file with no header
 * @param resource $file .csv file
 * @param string $class selected class's name
 * @return void
 */
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
            fputcsv($file, $lineData, ';', eol:";");
            fputcsv($file, $tempGrades, ';');
            $j++;
        }
    }
}
/**
 * Saves the 'School averages' table to a .csv file
 * @return void
 */
function saveSchoolAverages() {
    header("Location: index.php?subjectAverages=Subject+averages");

    if (!is_dir("export")) {
        mkdir("export");
    }

    $subjects = $_SESSION['data']['subjects'];
    $header = ['math', 'history', 'biology', 'chemistry', 'physics', 'informatics', 'alchemy', 'astrology', 'average'];
    $file = fopen("export\\schoolAverages-".date("Y-m-d_Hi").".csv", 'w');
    fputcsv($file, $header, ';');

    for ($i = 0; $i < count($subjects); $i++) {
        $avgs[] = getSchoolSubjectAvgs()[$subjects[$i]];
    }
    $avgs[] = round(array_sum(getSchoolSubjectAvgs()) / count(getSchoolSubjectAvgs()), 2);
    fputcsv($file, $avgs, ';');
}
/**
 *  Saves the 'Class averages' table to a .csv file
 * @return void
 */
function saveClassAverages() {
    header("Location: index.php?subjectAverages=Subject+averages");

    if (!is_dir("export")) {
        mkdir("export");
    }

    $classes = $_SESSION['data']['classes'];
    $subjects = $_SESSION['data']['subjects'];
    $header = ['class', 'math', 'history', 'biology', 'chemistry', 'physics', 'informatics', 'alchemy', 'astrology', 'average'];
    $file = fopen("export\\classAverages-".date("Y-m-d_Hi").".csv", 'w');
    fputcsv($file, $header, ';');

    foreach ($classes as $class) {
        $lineData = [];
        $lineData[] = $class;
        for ($i = 0; $i < count($subjects); $i++) {
            $lineData[] = getClassSubjectAvgs($class)[$subjects[$i]];
        }
        $lineData[] = getCumulativeClassAvg($class);
        fputcsv($file, $lineData, ';');
    }
}
/**
 * Saves the 'Best and worst classes' table to a .csv file
 * @return void
 */
function saveBeWoClass() {
    header("Location: index.php?bestAndWorstClasses=Best+and+worst+classes");

    if (!is_dir("export")) {
        mkdir("export");
    }

    $subjects = $_SESSION['data']['subjects'];
    $header = ['type', 'overall', 'overallAvg', 'math', 'mathAvg', 'history', 'historyAvg', 'biology', 'biologyAvg', 'chemistry', 'chemistryAvg', 'physics', 'physicsAvg', 'informatics', 'informaticsAvg',  'alchemy', 'alchemyAvg', 'astrology', 'astrologyAvg'];
    $file = fopen("export\\bestWorstClasses-".date("Y-m-d_Hi").".csv", 'w');
    fputcsv($file, $header, ';');

    // BEST CLASSES
    $lineData = [];
    $lineData += ['best', getBestClassByAvg(), getCumulativeClassAvg(getBestClassByAvg())];
    foreach ($subjects as $subject) {
        $lineData[] = getBestClassBySubjectAvg($subject);
        $lineData[] = getBestClassAvgBySubject($subject);
    }
    fputcsv($file, $lineData, ';');

    // WORST CLASSES
    $lineData = [];
    $lineData += ['worst', getWorstClassByAvg(), getCumulativeClassAvg(getWorstClassByAvg())];
    foreach ($subjects as $subject) {
        $lineData[] = getWorstClassBySubjectAvg($subject);
        $lineData[] = getWorstClassAvgBySubject($subject);
    }
    fputcsv($file, $lineData, ';');
}
/**
 * Saves all students ranked by overall/subjects in a .csv file
 * @return void
 */
function saveSchoolRanking() {
    header("Location: index.php?rankSchool=Rank+whole+school");

    if (!is_dir("export")) {
        mkdir("export");
    }

    $header = ['rank', 'overall', 'math', 'history', 'biology', 'chemistry', 'physics', 'informatics', 'alchemy', 'astrology'];
    $file = fopen("export\\schoolRanking-".date("Y-m-d_Hi").".csv", 'w');
    fputcsv($file, $header, ';');

    $subjects = $_SESSION['data']['subjects'];
    $orderedSchool = getOrderedSchool();
    for ($i = 0; $i < count($orderedSchool); $i++) {
        $lineData = [];
        $lineData += [$i+1 ,array_keys($orderedSchool)[$i]];
        foreach ($subjects as $subject) {
            $lineData[] = array_keys(getOrderedSchoolBySubject($subject))[$i];
        }
    fputcsv($file, $lineData, ';');
    }
}
/**
 * Saves all students based on class ranked by overall/subjects in a .csv file
 * @return void
 */
function saveClassRanking() {
    header("Location: index.php?rankClasses=Rank+by+classes");

    if (!is_dir("export")) {
        mkdir("export");
    }

    $header = ['class', 'rank', 'overall', 'math', 'history', 'biology', 'chemistry', 'physics', 'informatics', 'alchemy', 'astrology'];
    $file = fopen("export\\classRankings-".date("Y-m-d_Hi").".csv", 'w');
    fputcsv($file, $header, ';');

    $classes = $_SESSION['data']['classes'];
    $subjects = $_SESSION['data']['subjects'];
    foreach ($classes as $class) {
        $orderedStudents = getOrderedStudents($class);
        $j = 0;
        $lineData[] = $class;
        for ($i = 0; $i < count($orderedStudents); $i++) {
            $lineData = [];
            $lineData += [$class, $j+1 ,array_keys($orderedStudents)[$i]];
            foreach ($subjects as $subject) {
                $lineData[] = array_keys(getOrderedClassBySubject($class, $subject))[$j];
            }
            $j++;
            fputcsv($file, $lineData, ';');
        }
    }
    showMessagebox("export/classRankings-".date("Y-m-d_Hi").".csv");
}