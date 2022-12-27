<?php

class Ware extends Ware_make
{
    private $tables = [
        'file' => [
            'id' => ,
            'obj' => ,
            'obj_id' => ,
            'name' => ,
            'size' => ,
            'type' => ,
            'x_doctype' => ,
            'comment' => ,
            'dt_c' => ,
            'c_user_id' => ,
            'dt_u' => ,
            'u_user_id' => ,
        ]
    ];

    function options() {
        return [
            'connection' => ['Database connection'],
            'table' => ['Table name'],
            'dir' => ['Upload directory'],
            'rewrite' => ['Rewrite'],
            'scheme' => ['Scheme'],
            'use_crop' => ['Use crop'],
        ];
    }

    function install() {
        $this->create_tables();
    }

    function uninstall() {
        // 2do
    }
}
