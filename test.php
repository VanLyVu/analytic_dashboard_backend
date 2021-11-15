<?php
$origin = new DateTime('2008-01-11');
$target = new DateTime('2009-10-13');
$interval = $origin->diff($target);
var_dump($interval);

var_dump($interval->d);
var_dump($interval->format('%a'));


