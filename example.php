<?php

require_once("CandyConfig.php");
require_once("CandyBuilder.php");

$TestBuilder = new CandyBuilder($candy_config);
$TestBuilder->locale = "en-us";
$TestBuilder->Bundler()
			->Select("home")
				->SelectRaw("example.home")
				->SetToValue("version-num", $CB_VER);

echo $TestBuilder->build("home");

?>