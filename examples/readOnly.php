<?php
require_once '../vendor/autoload.php';

use Ephrin\DataObject\DocBlockProperties;

/**
 * @property-read string $data
 */
class DataObject {
    use DocBlockProperties;
}

//the trait declares its own constructor with defaults as an assoc array
$payload = new DataObject(['data' => 'Unchangeable property value :P']);

//but you can use trait's factory method as well (to define own constructor)
$payload = DataObject::fromArray(['data' => 'Unchangeable property value :P']);


//static declaration is available trough `defaults` method.



//easy autocompletion of property with IDE
print $payload->data . PHP_EOL; //prints content

//IDE shows me that property isn't writable
//$payload->data = 'try'; //gets exception: Can not store value.... is not writable

//undeclared properties are not allowed
$payload->dynamic = 'hack you!!'; //gets exception: No such property ...