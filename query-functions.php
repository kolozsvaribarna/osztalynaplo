<?php
/**
 * @author Kolozsvári Barnabás
 * desc: functions responsible for calculating averages and queries, and returning the html
*/

// displayed next to subjects in main table
function getSingleStudentAverage($grades) {
    $subjects = $_SESSION['data']['subjects'];
    $averages = [];
    $c = 0;
    foreach ($subjects as $subject) {
        if ($grades[$subject] != "") {
            // averages by subjects
            $averages[] = array_sum($grades[$subject]) / count($grades[$subject]);
            $c++;
        }
    }
    return round(array_sum($averages) / $c,2);
}
// class averages by subjects
function getClassSubjectAvgs($class) {
    $students = $_SESSION['students'];
    $subjects = $_SESSION['data']['subjects'];
    
    $gradeSum = [];
    $gradeCountSum = [];
    foreach ($students as $student) {
        if ($student['class'] == $class) {
            foreach($subjects as $subject) {
                if ($student['grades'][$subject][0] != '') {
                    // add the sum of grades for each student
                    $tempGrades[$subject][] = array_sum($student['grades'][$subject]);
                    $gradeSum[$subject] = array_sum($tempGrades[$subject]);
                    // increment and sum grades counter
                    $tempGradesCount[$subject][] = count($student['grades'][$subject]);
                    $gradeCountSum[$subject] = array_sum($tempGradesCount[$subject]);
                }
            }
        }
    }
    // calculate averages for each subject
    $classAvgs = [];
    foreach ($subjects as $subject) {
        $classAvgs[$subject] = round($gradeSum[$subject] / $gradeCountSum[$subject], 2);
    }
    return $classAvgs;  
}
// school averages by subjects (used in school averages table)
function getSchoolSubjectAvgs() {
    $subjects = $_SESSION['data']['subjects'];
    $classes = $_SESSION['data']['classes'];

    $schoolAvgs = [];
    foreach ($classes as $class) {
        foreach ($subjects as $subject) {
            $tempGrades[$subject][] = getClassSubjectAvgs($class)[$subject];
            $schoolAvgs[$subject] = round(array_sum($tempGrades[$subject]) / count($tempGrades[$subject]), 2);
        }
    }
    return $schoolAvgs;
}
function getCumulativeClassAvg($class) {
    $subjects = $_SESSION['data']['subjects'];

    foreach($subjects as $subject) {
        $avgs[] = getClassSubjectAvgs($class)[$subject];
    }
    return round(array_sum($avgs) / count($avgs), 2);
}
function getBestClassByAvg(){
    $classes = $_SESSION['data']['classes'];
    $max = 5;
    for ($i = 0; $i < count($classes); $i++) {
        $class = $classes[$i];
        $classAvgSum = array_sum(getClassSubjectAvgs($class));
        if ($max < $classAvgSum) {
            $max = $classAvgSum;
            $maxClass = $class;
        }
    }
    return $maxClass;
}
function getWorstClassByAvg(){
    $classes = $_SESSION['data']['classes'];

    $minClass = $classes[0];
    $min = array_sum(getClassSubjectAvgs($minClass));
    for ($i = 0; $i < count($classes); $i++) {
        $class = $classes[$i];
        $classAvgSum = array_sum(getClassSubjectAvgs($class));
        if ($min > $classAvgSum) {
            $min = $classAvgSum;
            $minClass = $class;
        }
    }
    return $minClass;
}
function getBestClassBySubjectAvg($subject) {
    $classes = $_SESSION['data']['classes'];
    foreach($classes as $class) {
        $avg[$class] = getClassSubjectAvgs($class)[$subject];
    }
    return array_keys($avg, max($avg))[0];
}
function getBestClassAvgBySubject($subject) {
    $classes = $_SESSION['data']['classes'];
    foreach($classes as $class) {
        $avg[$class] = getClassSubjectAvgs($class)[$subject];
    }
    return max($avg);
}
function getWorstClassBySubjectAvg($subject) {
    $classes = $_SESSION['data']['classes'];
    foreach($classes as $class) {
        $avg[$class] = getClassSubjectAvgs($class)[$subject];
    }
    return array_keys($avg, min($avg))[0];
}
function getWorstClassAvgBySubject($subject) {
    $classes = $_SESSION['data']['classes'];
    foreach($classes as $class) {
        $avg[$class] = getClassSubjectAvgs($class)[$subject];
    }
    return min($avg);
}
function getOrderedStudents($class) {
    $students = $_SESSION['students'];

    for ($i = 0; $i < count($students); $i++) {
        if ($students[$i]['class'] == $class) {
            $classStudents[$students[$i]['lastname'] . " " . $students[$i]['firstname']] = getSingleStudentAverage($students[$i]['grades']);
        }
    }
    arsort($classStudents);
    return $classStudents;
}

function getOrderedClassBySubject($class, $subject) {
    $students = $_SESSION['students'];

    for ($i = 0; $i < count($students); $i++) {
        if ($students[$i]['class'] == $class) {
            if ($students[$i]['averages'] != "") {
                $temp[$students[$i]['lastname'] . " " . $students[$i]['firstname']] = $students[$i]['averages'][$subject];
            }
            else {
                $temp[$students[$i]['lastname'] . " " . $students[$i]['firstname']] = 0;
            }
        }
    }
    arsort($temp);
    return $temp;
}

function getOrderedSchool() {
    $students = $_SESSION['students'];

    for ($i = 0; $i < count($students); $i++) {
        $studentAvgs[$students[$i]['class'].", ".$students[$i]['lastname']." ".$students[$i]['firstname']] = $students[$i]['average'];
    }
    arsort($studentAvgs);
    return $studentAvgs;
}

function getOrderedSchoolBySubject($subject) {
    $students = $_SESSION['students'];

    for ($i = 0; $i < count($students); $i++) {
        $subjectRank[$students[$i]['class'].", ".$students[$i]['lastname']." ".$students[$i]['firstname']] = $students[$i]['averages'][$subject];
    }
    arsort($subjectRank);
    return $subjectRank;
}