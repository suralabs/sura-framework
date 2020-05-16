<?php

require __DIR__.'\..\..\vendor\autoload.php';

echo 'f';
echo 'f';

$Cache = new MemcachedAdapter();
$Cache = new Cache($Cache);