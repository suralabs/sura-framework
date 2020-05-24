<?php

require __DIR__.'\..\..\vendor\autoload.php';

echo 'f';
echo 'f';

$Cache = new FileAdapter();
$Cache = new Cache($Cache);
