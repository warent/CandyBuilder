<?php

use Symfony\Component\Yaml\Yaml;
require_once("vendor/autoload.php");
require_once("CandyBundler.php");

class CandyBuilder {

	public $locale, $candyBundles;

	// Load up our locale automagically for each raw candy
	private function WrapLocale(&$toWrap, $candyName) {
		if (file_exists("./raw/locale/".$this->locale."/".$candyName.".yaml")) {
			$toWrap = array_merge($toWrap, Yaml::parse(file_get_contents("./raw/locale/".$this->locale."/".$candyName.".yaml")));
		}
	}

	// Use the actual LightnCandy with our specified data
	public function FN_WRAP($candy, $values) {

		$rawCandy = file_get_contents("./raw/candy/" . $candy);
		$processedCandy = LightnCandy::compile($rawCandy, ['flags' => LightnCandy::FLAG_NOESCAPE]);
		$wrappedCandy = LightnCandy::prepare($processedCandy);

		return $wrappedCandy($values);

	}

	/*
		Repeat a specific candy for the number of given argument arrays
	*/
	public function FN_REPEAT($args) {

		$candyName = $args[0];
		$args = $args[1];

		$processed = "";
		foreach ($args as $content) {
			$processed .= $this->build([$candyName => $content], true);
		}

		return $processed;
	}


	function __construct($candyBundles) {

		$this->candyBundles = $candyBundles;

	}

	private function processDynamicContent($candyReplacements, &$replace) {
		foreach ($candyReplacements as $toReplace => $replaceConfig) {
			if (array_key_exists("fn", $replaceConfig)) {
				// Perform each function to get the desired dynamic result
				$fn = $replaceConfig["fn"];
				$args = $replaceConfig["args"];

				if (substr($fn, 0, 2) == "::") {
					$fn = substr($fn, 2);
					$replace[$toReplace] = $this->$fn($args);
				} else {
					$replace[$toReplace] = call_user_func_array($fn, $args);
				}
				
			} else if (array_key_exists("var", $replaceConfig)) {
				// Variable or constant result
				$replace[$toReplace] = $replaceConfig["var"];
			} else if (array_key_exists("bundle", $replaceConfig)) {
				// Nested bundle for recursion
				$replace[$toReplace] = $this->build($replaceConfig["bundle"], true);
			}
		}
	}

	public function build($candyBundle, $raw=false) {

		$built = "";
		if (!$raw) $candyBundle = $this->candyBundles[$candyBundle]['raws'];

		// We scan all of our page's configured "Raw candies".
		// These are the HTML templates for parsing by our LightnCandy wrapper
		foreach ($candyBundle as $candyName=>$candyReplacements) {

			// Our {{handlebars}} to be replaced by LightnCandy are stored here
			$replace = array();

			/* First we check to see if there's even anything to replace at all.
			 * The only time anything should ever be replaced is if it's a dynamic variable.
			 * If the string is static, we must define it in config.php $J_LOCALE
			 */
			if (count($candyReplacements) > 0) $this->processDynamicContent($candyReplacements, $replace);
			// If we have a locale set, go ahead and process it here
			if (isset($this->locale)) $this->WrapLocale($replace, $candyName);

			// Wrap up our raw candy and push it for output
			$built .= $this->FN_WRAP($candyName.".candy", $replace);
		}

		return $built;
	}

	###################################################################################
	#
	# NOTICE
	#
	# The bundler provides custom error handling, additional verbosity, and guaranteed backwards compatibility
	# However, this comes at the expense of significant resources
	# If trying to use resources sparingly, consider referencing $candyBundles directly
	#
	###################################################################################
	public function Bundler() {
		return new CandyBundler($this);
	}

}

?>