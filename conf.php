<?php

return [
    'view' => ['path' => __DIR__ . '/mvc'],
    'app' => [
        'type' => 'prod',
        'flags' => 'tune',
        'options' => 'connection 
table file
dir var/upload
scheme 0
use_crop 1
crop_sizes 200 x 200,200 x 500',
    ],
];
