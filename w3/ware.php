<?php

class Ware extends Wares
{
    private $tables = [
        'file' => [
            'id' => '',
            'obj' => '',
            'obj_id' => '',
            'name' => '',
            'size' => '',
            'type' => '',
            'x_doctype' => '',
            'comment' => '',
            'dt_c' => '',
            'c_user_id' => '',
            'dt_u' => '',
            'u_user_id' => '',
        ]
    ];

    function opt_form() {
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
        //$this->create_tables();
        return 'tables: 1';
    }

    function uninstall() {
        // 2do
    }
}
