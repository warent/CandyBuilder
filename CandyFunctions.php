<?php

function BuildStyles($candyBundle) {

	global $CANDY_PAGE_CONFIG, $CANDY_STYLES;

	$styles = "";

	foreach ($CANDY_PAGE_CONFIG[$candyBundle]['styles'] as $stylePack) {
		$styles .= CandyBuilder::ShortWrap('<link rel="stylesheet" type="text/css" href="%s" media="screen">',
			$CANDY_STYLES[$stylePack]);
	}

	return $styles;
}

?>