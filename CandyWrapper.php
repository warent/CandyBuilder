<?php

require_once("vendor/autoload.php");

class CandyWrapper {

	public static function Wrap($candy, $values) {

		$rawCandy = file_get_contents("./raw/candy/" . $candy);
		$processedCandy = LightnCandy::compile($rawCandy, ['flags' => LightnCandy::FLAG_NOESCAPE]);
		$wrappedCandy = LightnCandy::prepare($processedCandy);

		return $wrappedCandy($values);

	}

}

?>