<?php

function db_connect (){
    try {
        $hostname = 'IS-HAY04.ischool.uw.edu';
        $port = 1433;
        $dbname = 'RRTest2';
        $username = 'info445';
        $pw = 'GoHuskies!';
        $dbh = new PDO ("dblib:host=$hostname:$port;dbname=$dbname","$username","$pw");
        $dbh->setAttribute(PDO :: ATTR_DEFAULT_FETCH_MODE, PDO :: FETCH_ASSOC);

        return ($dbh);

    } catch (PDOException $e) {
        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
        exit;
    }

}

?>