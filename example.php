<?php

require_once("CandyConfig.php");
require_once("CandyBuilder.php");

$TestBuilder = new CandyBuilder($candy_config);
$TestBuilder->locale = "en-us";
echo $TestBuilder->build("home");

?>