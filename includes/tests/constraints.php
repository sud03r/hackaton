<?php

require_once(__DIR__ . "/../parseQuery.php");

echo "\n\n====== DATE CONSTRAINTS ======\n\n";
$queriesDate = array(
    "something before 1959 something",
    "something after '84 something",
    "something between 1978 and '84 something",
    "something from 1978 to '84 something",
);

foreach($queriesDate as $q) {
    echo "$q:\n";
    $cons = Pq::findDateRangeConstraints($q);
    foreach ($cons as $con)
        echo $con->getSQLCondition() . "\n";
    echo "$q:\n";
}


echo "\n\n====== LENGTH CONSTRAINTS ======\n\n";
$queriesLength = array(
    "something longer than 10 minutes something",
    "something shorter than 240 minutes something",
    "something shorter than 100mins something",
);

foreach($queriesLength as $q) {
    echo "$q:\n";
    $cons = Pq::findLengthConstraints($q);
    foreach ($cons as $con)
        echo $con->getSQLCondition() . "\n";
    echo "$q:\n";
}


echo "\n\n====== GENRE CONSTRAINTS ======\n\n";
$queriesGenre = array(
    "something not comedy or horror and drama something",
    "something romance comedy something",
    "something not family movie not animation something",
);

foreach($queriesGenre as $q) {
    echo "$q:\n";
    $cons = Pq::findGenreConstraints($q);
    foreach ($cons as $con)
        echo $con->getSQLCondition() . "\n";
    echo "$q:\n";
}


?>