<?php

return [
    'view' => ['path' => __DIR__ . '/mvc'],
    'cfg' => ['path' => __DIR__],
    'app' => [
        'type' => 'prod',
        'options' => 'connection 
table file
dir var/upload
rewrite upload
scheme 0
use_crop 1
crop_sizes 200 x 200,200 x 500',
    ],
];
