<?php
/**
* @author Kolozsvári Barnabás
*/

include_once "html.php";
include_once "db.php";
include_once "request.php";

htmlHead();
htmlStart();

displayNav();
requestHandle();

htmlEnd();