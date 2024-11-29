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
    showQueryOptions("Student ranking");
    echo "- tanulók rangsorolása iskolai és osztály szinten, tantárgyanként és összesítve, kiemelve a 3 legjobb és a 3 leggyengébb tanulót<br><br>
            Legyen lehetőség elmenteni a lekérdezések eredményét külön-külön egy .csv fájlba egy 'export' nevű mappába. <br>Mentés előtt vizsgálja meg, hogy a mappa létezik-e és ha nem, akkor hozza létre. A .csv fájlok tartalmazzák az oszlopfejléceket.";
    echo showCumulativeClassRankings();
    //echo '<pre>'; print_r($_SESSION['students']); echo '</pre>';
}
elseif (isset($_GET['bestAndWorstClasses'])) {
    showQueryOptions("Best and worst classes");
    showCumulativeBWClass();
    showDistBWClass();
}