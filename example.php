<?php

require_once("CandyBuilder.php");

$TestBuilder = new CandyBuilder("home");
$TestBuilder->locale = "en-us";
echo $TestBuilder;

?>