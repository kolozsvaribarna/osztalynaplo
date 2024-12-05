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
    showQueryOptions("Student averages");
    showStudentRankingOptions();
}
elseif (isset($_GET['bestAndWorstClasses'])) {
    showQueryOptions("Best and worst classes");
    showBestWorstClass();
}

if (isset($_GET['rankClasses'])) {
    showQueryOptions("Student averages - Classes");
    showStudentRankingOptions();
    // table
    showCumulativeClassRankings();
}
elseif (isset($_GET['rankSchool'])) {
    showQueryOptions("Student averages - Whole school");
    showStudentRankingOptions();
    showSchoolRanking();
}