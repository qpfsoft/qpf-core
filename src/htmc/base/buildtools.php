<?php

$str = <<<endd
<canvas>，<embed>，<iframe>，<img>，<input>，<object>，<video>
endd;

echo str_replace(['<', '>', '，'], ['\'', '\'', ','], trim($str));