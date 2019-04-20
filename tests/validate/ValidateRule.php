<?php
use qpf\validator\ValidateRule;

include __DIR__ . '/../boot.php';

$id = new ValidateRule('id', 'exist|int:1,100');

echor($id->getRule());
echor($id->rule);

$id->append('user');

echor($id->rule);

$id->remove('int');

echor($id->rule);


$result = $id->check(5);

echor($result);
