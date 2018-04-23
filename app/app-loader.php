<?php
$base = __DIR__ . '/../app/';

$folders = [
    'models',
    'controllers'
];

foreach($folders as $f)
{
    foreach (glob($base . "$f/*.php") as $filename)
    {
        require $filename;
    }
}
?>