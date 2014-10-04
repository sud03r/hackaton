<?php
namespace parsing;

echo "Going into constraint test\n";

require_once(__DIR__ . "/../parsing/constraint.php");

echo "Returned to constraint test\n";

error_reporting(E_ALL);
echo "Error reporting turned on\n";

echo "\n\n====== DATE CONSTRAINTS ======\n\n";
$queriesDate = array(
    "something before 1959 something",
    "something after '84 something",
    "something between 1978 and '84 something",
    "something from 1978 to '84 something",
);

foreach($queriesDate as $q) {
    echo "$q:\n";
    $cons = Constraint::findDateRangeConstraints($q);
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
    $cons = Constraint::findLengthConstraints($q);
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
    $cons = Constraint::findGenreConstraints($q);
    foreach ($cons as $con)
        echo $con->getSQLCondition() . "\n";
    echo "$q:\n";
}


echo "\n\n====== FAMILY CONSTRAINTS ======\n\n";
$queriesFamily = array(
    "something rated PG-13 something",
    "something X something",
    "something rated R something",
);

foreach($queriesFamily as $q) {
    echo "$q:\n";
    $cons = Constraint::findFamilyConstraints($q);
    foreach ($cons as $con)
        echo $con->getSQLCondition() . "\n";
    echo "$q:\n";
}



?>
