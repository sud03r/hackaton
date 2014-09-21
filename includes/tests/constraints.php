<?php

require_once(__DIR__ . "/../parseQuery.php");

$queries = array(
    "something before 1959 something",
    "something after '84 something",
    "something between 1978 and '84 something",
    "something from 1978 to '84 something",
);

foreach($queries as $q) {
    echo "$q:\n";
    var_dump(Pq::findDateRangeConstraints($q));
    echo "$q:\n";
}

?>