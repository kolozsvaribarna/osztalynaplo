<?php
/**
* @author Kolozsvári Barnabás
*/

require_once "html-functions.php";

htmlHead();

if (!isset($_GET['class'])){
    showClassList();
}

if (isset($_GET['subjectAverages'])) {
    showQueryOptions("Subject averages");
    showSchoolAvgsTable();
    showClassAvgsTable();
}
elseif (isset($_GET['studentRanking'])) {
    showQueryOptions();
    showStudentRankingOptions();
}
elseif (isset($_GET['bestAndWorstClasses'])) {
    showQueryOptions("Best and worst classes");
    showCumulativeBWClass();
    showDistBWClass();
}

if (isset($_GET['rankClasses'])) {
    showQueryOptions("Student averages - Classes");
    showStudentRankingOptions();
    showCumulativeClassRankings();
}
elseif (isset($_GET['rankSchool'])) {
    showQueryOptions("Student averages - Whole school");
    showStudentRankingOptions();
    showSchoolRanking();
}