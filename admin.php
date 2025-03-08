<?php

require_once "db.php";
require_once "admin_html.php";
require_once "admin_request.php";

htmlHead();
htmlStart();
displayReturnMainBtn();

echo "<h2>Admin page</h2>";

handle();

htmlEnd();