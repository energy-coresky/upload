<?php

namespace upload;

class ware extends \Wares
{
    public $bases = ['SQLite3', 'MySQLi'];

    function form() {
        return [
            'connection' => ['Database connection', 'select', $this->databases()],
            'table' => ['Table name', '', '', 'file'],
            'dir' => ['Upload directory', '', '', 'var/upload'],
            'use_crop' => ['Use crop', 'chk'],
            'crop_sizes' => ['Crop Sizes', '', '', '200 x 200,200 x 500'],
        ];
    }

    function install($mode) {
        $model = ant::model();
        $dd = $model->dd();
        $dir = $model->get_dir();
        return [
            'exist' => $model->get_table($table),
            'table' => $table,
            'dir' => $dir,
            'base' => $dd->name,
            'ok' => in_array($dd->name, $this->bases),
        ];
    }

    function uninstall($mode) {
    }
}
