<?php

use Symfony\Component\Yaml\Yaml;
require_once("vendor/autoload.php");

class CandyBuilder {

	public $locale, $candyConfig;

	// Use the actual LightnCandy with our specified data
	public static function Wrap($candy, $values) {

		$rawCandy = file_get_contents("./raw/candy/" . $candy);
		$processedCandy = LightnCandy::compile($rawCandy, ['flags' => LightnCandy::FLAG_NOESCAPE]);
		$wrappedCandy = LightnCandy::prepare($processedCandy);

		return $wrappedCandy($values);

	}

	// Load up our locale automagically for each raw candy
	private function WrapLocale(&$toWrap, $candyName) {
		if (file_exists("./raw/locale/".$this->locale."/".$candyName.".yaml")) {
			$toWrap = array_merge($toWrap, Yaml::parse(file_get_contents("./raw/locale/".$this->locale."/".$candyName.".yaml")));
		}
	}

	/*
		Argument 1: Candy to loop
		Argument 1+n: Content to replace with
	*/
	public static function FN_LOOP() {
		$args = func_get_args();

		$candyName = $args[0];
		$args = $args[1];

		$processed = "";
		foreach ($args as $content) {
			$processed .= CandyBuilder::Wrap($candyName.".candy", $content);
		}

		return $processed;
	}

	function __construct($candyConfig) {

		$this->candyConfig = $candyConfig;

	}

	function build($candyBundle) {

		$built = "";

		// We scan all of our page's configured "Raw candies".
		// These are the HTML templates for parsing by our LightnCandy wrapper
		foreach ($this->candyConfig[$candyBundle]['raws'] as $candyName=>$candyReplacements) {

			// Our {{handlebars}} to be replaced by LightnCandy are stored here
			$replace = array();

			/* First we check to see if there's even anything to replace at all.
			 * The only time anything should ever be replaced is if it's a dynamic variable.
			 * If the string is static, we must define it in config.php $J_LOCALE
			 */
			if (count($candyReplacements) > 0) {
				foreach ($candyReplacements as $toReplace => $replaceVal) {
					if (array_key_exists("fn", $replaceVal)) {
					// Perform each function to get the desired dynamic result
					// and define it as the replacement for the handlebars {{$toReplace}}
					$fn = $replaceVal["fn"];
					$args = $replaceVal["args"];

					if (substr($fn, 0, 2) == "::") {
						$fn = "CandyBuilder" . $fn;
					}

					$replace[$toReplace] = call_user_func_array($fn, $args);
					} else {
						$replace[$toReplace] = $replaceVal["var"];
					}
				}
			}

			// If we have a locale set, go ahead and process it here
			if (isset($this->locale)) $this->WrapLocale($replace, $candyName);

			// Wrap up our raw candy and push it for output
			$built .= CandyBuilder::Wrap($candyName.".candy", $replace);
		}

		return $built;

	}

}


?>