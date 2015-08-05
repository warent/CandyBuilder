<?php

use Symfony\Component\Yaml\Yaml;

require_once("CandyConfig.php");

class CandyBuilder {

	public $targetCandyBundle, $functions = [], $locale;

	/*
	 * Essentially a glorified sprintf
	 * except that you can repeat the iteration
	 * to create self-similar replicated data.
	 *
	 * First argument is always the input string, using sprintf syntax
	 * Additional arguments accepts an indefinite number of arrays as the format
	 */
	public static function ShortWrap() {
		$args = func_get_args();
		$wrapWith = $args[0];
		$args = array_pop($args);
		$output = "";

		foreach ($args as $dataToWrap) {
			if (!is_array($dataToWrap)) $dataToWrap = array($dataToWrap);
			$output .= vsprintf($wrapWith, $dataToWrap);
		}

		return $output;
	}

	// Load up our locale automagically for each raw candy
	private function WrapLocale(&$toWrap, $candyName) {
		if (file_exists("./raw/locale/".$this->locale."/".$candyName.".yaml")) {
			$toWrap = array_merge($toWrap, Yaml::parse(file_get_contents("./raw/locale/".$this->locale."/".$candyName.".yaml")));
		}
	}

	function __construct($page, $directPageAccess = false) {

		$this->page = $page;
		$this->directPageAccess = $directPageAccess;
	}

	function __toString() {

		global $CANDY_LOCALE, $CANDY_LANGUAGE, $CANDY_PAGE_CONFIG, $J_LOCALE, $J_LANGUAGE;

		$built = "";

		// We scan all of our page's configured "Raw candies".
		// These are the HTML templates for parsing by CandyWrapper our LightnCandy Wrapper
		foreach ($CANDY_PAGE_CONFIG[$this->page]['raws'] as $candyName=>$candyReplacements) {

			
			// If the "raw" is "fn" then we just append the results of the defined function instead of wrapping a whole candy
			// TODO: Create a dynamic function array so functions can be defined outside of this class
			if ($candyName == "fn") {
				$built .= $this->$candyReplacements();
				continue;
			}

			// Our {{handlebars}} to be replaced by LightnCandy are stored here
			$replace = array();

			/* First we check to see if there's even anything to replace at all.
			 * The only time anything should ever be replaced is if it's a dynamic variable.
			 * If the string is static, we must define it in config.php $J_LOCALE
			 */
			if (count($candyReplacements) > 0) {
				foreach ($candyReplacements as $toReplace => $replaceFunction) {
					// Perform each function to get the desired dynamic result
					// and define it as the replacement for the handlebars {{$toReplace}}
					$replace[$toReplace] = call_user_func_array($replaceFunction[0], $replaceFunction[1]);
				}
			}

			$this->WrapLocale($replace, $candyName);

			// Wrap up our raw candy and push it for output
			if (isset($this->locale)) $built .= CandyWrapper::Wrap($candyName.".candy", $replace);
		}

		return $built;

	}

}


?>