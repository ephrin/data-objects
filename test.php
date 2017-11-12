<?php

error_reporting(E_ALL);

class propless {

}
$pl = new propless();


var_dump(isset($pl->a));
unset($pl->a);