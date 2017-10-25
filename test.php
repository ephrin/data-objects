<?php


use Ephrin\Immutable\DocProperties;

require_once 'vendor/autoload.php';

/**
 * @property-read string $property
 * @property integer $int
 */
class Test {
    use DocProperties;

    public function setIt(\stdClass $p){
        var_dump($p);
    }
}

$t = Test::fromArray(['property' => 123]);

$t->int = 'm1u2s3t be int';

var_dump($t->property);

var_dump($t->int);

$t = new Test();

$t->int = 2;


$obj = $cp = (object)['a' => 1];

$var = @settype($t, 'object');

var_dump($var);

var_dump($t);

var_dump(var_export($t));