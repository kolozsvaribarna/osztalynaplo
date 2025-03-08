<?php

require_once "db.php";
require_once "admin_html.php";
require_once "admin_request.php";

htmlHead();
htmlStart();
echo "<h2>Admin page</h2>";
displayReturnMainBtn();

handle();

if (!isset($_POST["edit_category"])) displayEditCategoriesForm();

htmlEnd();