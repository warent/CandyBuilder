<?php

###################################################################################
#
# NOTICE
#
# The bundler provides custom error handling, additional verbosity, and guaranteed backwards compatibility
# However, this comes at the expense of significant resources
# If trying to use resources sparingly, consider referencing $candyBundles directly
#
###################################################################################

class CandyBundler {

	private $builder, $selectedBundle = [], $workingBundlesCopy, $workingBundle;

	function __construct($builder) {
		$this->builder = &$builder;
	}

	private function updateWorkingBundle() {

		/* We can't work with a direct reference of $this->builder->candyBundle
		 * So all of our actions will be performed on a copy of the bundles
		 * This copy will then replace the $this->builder->candyBundle altogether at the end
		 */
		$this->workingBundlesCopy = $this->builder->candyBundles;

		// If we haven't selected a bundle, the default is all bundles
		if (count($this->selectedBundle) == 0) $this->workingBundle = &$this->workingBundlesCopy;
		else {
			/* Our selected bundle is stored within $this->selectedBundle
			 * Example: If you wanted to select $this->builder->candyBundles["home"]["raws"]["example.home"]
			 * Then $this->selectedBundle = ["home", "raws", "example.home"]
			 * We begin by creating an array of references named candyBundleRefs to the workingBundlesCopy
			 * Starting with the top level (In this case "home") we'll add a reference to $this->workingBundlesCopy["home"]
			 * Then we add another reference to the array by selecting the previous reference
			 * Thus iterating downwards
			 * So - 
			 *		candyBundleRefs[0] = &$this->workingBundlesCopy["home"]
			 * 		candyBundleRefs[1] = &$candyBundleRefs[0]["raws"] == $this->workingBundlesCopy["home"]["raws"]
			 *		candyBundleRefs[2] = &$candyBundleRefs[1]["example.home"] == $this->workingBundlesCopy["home"]["raws"]["example.home"] 
			 */
			$candyBundleRefs = array();
			$candyBundleRefs[] = &$this->workingBundlesCopy[$this->selectedBundle[0]];
			for ($i = 1; $i < count($this->selectedBundle); $i++) {
				$candyBundleRefs[] = &$candyBundleRefs[$i-1][$this->selectedBundle[$i]];
			}
			$this->workingBundle = &$candyBundleRefs[count($candyBundleRefs)-1];
		}

		// Since you can't strictly set a reference to a return value of a function
		// We're forced to set the internal variable "$this->workingBundle" to our results
		// This result will be used immediately after calling this function
		// After modifying the result, we must remember to update $this->builder->candyBundles = $this->workingBundlesCopy
	}

	private function _newBundle($name) {
		$this->updateWorkingBundle();
		$_newBundle = [
			"raws" => []
		];
		$this->workingBundle[$name] = $_newBundle;
		$this->builder->candyBundles = $this->workingBundlesCopy;
	}

	private function _newRaw($name) {
		$this->updateWorkingBundle();
		$this->workingBundle["raws"][$name] = [];
		$this->builder->candyBundles = $this->workingBundlesCopy;
	}

	public function NewBundle($name, $follow=true) {
		$this->updateWorkingBundle();
		if (!array_key_exists($name, $this->workingBundle)) {
			$this->_newBundle($name);
			if ($follow) {
				array_push($this->selectedBundle, $name);
			}
		}
		return $this;
	}

	public function NewRaw($name, $follow=true) {
		$this->updateWorkingBundle();
		if (!array_key_exists($name, $this->workingBundle)) {
			$this->_newRaw($name);
			if ($follow) {
				array_push($this->selectedBundle, $name);
			}
		}
		return $this;
	}

	public function Select($name) {
		$this->updateWorkingBundle();
		if (array_key_exists($name, $this->workingBundle)) {
			array_push($this->selectedBundle, $name);
		}
		return $this;
	}

	public function SelectRaw($name) {
		$this->updateWorkingBundle();
		if (array_key_exists($name, $this->workingBundle["raws"])) {
			array_push($this->selectedBundle, "raws");
			array_push($this->selectedBundle, $name);
		}
		return $this;
	}

	public static function ReplaceWithValue() {
		$args = func_get_args();
		$arr = [];
		foreach ($args as $arg) {
			$arr[$arg[0]] = ["var" => $arg[1]];
		}
		return $arr;
	}

	public static function ReplaceWithFunction($search, $fn, $args) {
		$args = func_get_args();
		$fn = $args[0];
		array_shift($args);
		return [$search => ["fn" => $fn, "args" => $args]];
	}

	public function SetToValue($search, $val) {
		$this->updateWorkingBundle();
		$this->workingBundle[$search] = ["var" => $val];
		$this->builder->candyBundles = $this->workingBundlesCopy;
		return $this;
	}

	public function SetToFunction() {
		$args = func_get_args();
		$this->updateWorkingBundle();
		$search = $args[0];
		array_shift($args);
		$fn = $args[0];
		array_shift($args);
		print_r($args);
		$this->updateWorkingBundle();
		$this->workingBundle[$search] = ["fn" => $fn, "args" => $args];
		$this->builder->candyBundles = $this->workingBundlesCopy;
		return $this;
	}


	public function Clear() {
		$this->updateWorkingBundle();
		$this->workingBundle = NULL;
		$this->builder->candyBundles = $this->workingBundlesCopy;
		array_pop($this->selectedBundle);
		return $this;
	}

}