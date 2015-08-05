Feature: ShortWrap
	In order to quickly create duplicate strings with slight variations
	As a template builder
	I need to define a base string in sprintf format with an array of array arguments for formatting

	Scenario:
	  Given I have a CandyBuilder "home"
	  And I have a string "<a href='#%d'>%s</a>"
	  And I have an array argument:
	  	| href	| linkstr	|
	  	| 0		| link one	|
	  	| 1		| link two	|
	  When I run ShortWrap
	  Then I should get "<a href='#1'>link one</a><a href='#2'>link two</a>"