<?php

/*
	This file specifically is arbitrary and not necessary
	For the purposes of the example, all config data is stored here
	Config data may be kept elsewhere to eliminate this file
*/

// CandyBuilder version
$CB_VER = "v0.2.0-beta";

// Features for demonstration of recursion
$CB_FEAT2 = "Functional templating";
$CB_FEAT3 = "Variable templating";

// We define all of our pages here. A full page with all included templates is known as a "Candy Bundle" or simply "bundle"
$candy_config = [

	// At the first level we have the name of the bundle
	'home' => [
		/*
			We define our raw candies here
			It's important to note that these directly reference the files contained in the "raws" directory
			CandyBuilder will load the template from raws/candy
			and check to see if it has a corresponding locale to parse against
		*/
		'raws'	=>	[
			'example.head' => [
				/*
					Inside of the example.head.candy file we have the handlebar {{styles}}
					Since {{styles}} isn't part of any locale, that must mean that the replacement is dynamic
					Equally important to remember is that all static content should go in a locale
					All dynamic content should be processed here via a function or variable

					In this instance we're using the built-in FUNC_LOOP
					which will repeat the same candy template for as many argument arrays provided

					Note: when using built-in functions, we use the shorthand ::%function%, see the initial double-colon ::
		
					The key of the first element in the array tells us if its a function (fn) or variable (var)
				*/
				'styles' => [
					"fn" 	=> "::FN_REPEAT",
					"args" 	=> [	
						'example.css', // Remember, this refers to the file raw/candy/example.css.candy
						[
							[ 'href' => ['var' => './example-styles/example.style.css']],
							[ 'href' => ['var' => './example-styles/example.style2.css']]
						]
					]
				],
			],
			/*
				example.home.candy has a lot to replace, but only one dynamic field
				Therefore, all the replacement content is automatically loaded from the raw/locale/%language%/example.home.yaml
				With the exception of 'version-num' which we replace with a variable (constants will work as well)
				example.php shows use of the verbose bundler to insert the version number
			*/
			'example.home' => [
				'features' => [
					/*
						Here we can see the new recursive candy nesting in action.
						You can replace one field with a whole other candy to be processed
					*/
					'bundle' => [
						"example.features" => [
							'feature-2' => [
								'var' => $CB_FEAT2
							],

							'feature-3' => [
								'var' => $CB_FEAT3
							]
						]
					]
				]
			],
			'example.foot' => []
		]
	]
];

?>