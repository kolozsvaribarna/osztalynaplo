<?php
/**
 * @author Kolozsvári Barnabás
 * desc: code responsible for the site's HTML
*/

/**
 * Displays the HTML header
 * @return void
 */
function htmlHead() {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' href='style.css' type='text/css'>
        <title>Classroom</title>
    </head>
    <body>
    
    </body>
    </html>";
}

function showDBInitBtn() {
    echo "<form method='GET'><input type='submit' class='btn btn-save' value='Create database' name='initDB'></form>";
}