# CandyBuilder

CandyBuilder is a simple templating framework implementing [LightnCandy](https://github.com/zordius/lightncandy). CandyBuilder allows you to define all of your pages as skeleton structures formed by templates. Templated data is replaced by a specified locale which is automatically located, otherwise replaced by a custom function--alowing you to remove all that messy php from your templates altogether using raw html and moustache syntax.

## Features
- Page structure management
- Functional templating
- Variable templating
- YAML locale parsing
- Automatic locale processing

## Version
v0.2.0-beta

## Tech

CandyBuilder only uses a couple packages to work correctly

* [Behat] - Behavioral testing environment
* [LightnCandy] - php port of handlebars.js and moustache.js
* [Symphony/Yaml] - For parsing locale YAML files

And of course CandyBuilder itself is open source with a [public repository](https://github.com/warent/candybuilder) on GitHub.