<?php

// We define all of our pages here. A full page with all included templates is known as a "Candy Bundle" or simply "bundle"
$CANDY_PAGE_CONFIG = [

	// At the first level we have the name of the bundle
	'home' => [

		// We define our stylesheets here
		'styles' => [
			'Main'
		],

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
					All dynamic content should be processed here via a function

					In this instance we're using the built-in FUNC_LOOP
					which will repeat the same candy template for as many argument arrays provided
		
					That function name is defined as the first element of an array
					The next element of the array are the arguments for the function
				*/
				'styles' => [
					"CandyBuilder::FUNC_LOOP",
					[	
						'example.css',
						[
							[ 'href' => './example-styles/example.style.css'],
							[ 'href' => './example-styles/example.style2.css']
						]
					]
				]
			],
			// example.home.candy has a lot to replace, but none of it is dynamic
			// Therefore, all the replacement content is automatically loaded from the raw/locale/%language%/example.home.yaml
			'example.home' => [],
			'example.foot' => []
		]
	]
];

?>