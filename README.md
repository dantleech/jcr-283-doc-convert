JCR-283 to Restructured Text
============================

These scripts will convert the HTML version of the JCR-293 Content Repository
specification to restructured text format.

Installation
------------

Install dependencies:

````php
$ composer install
````

Install pandoc

````php
$ apt-get install pandoc
````

(or whatever)

Usage
-----

Download the sources:

````
$ php download.php
````

Convert the files:

````
$ php convert.php
````

Copy the output somewhere:

````
cp -R output /somewhere
````
