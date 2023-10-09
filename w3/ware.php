<?php

namespace upload;

class ware extends \Wares
{
    public $bases = ['SQLite3', 'MySQLi'];
    public $engines = ['InnoDB', 'MyISAM', 'CSV'];

    function form() {
        return [
            'connection' => ['Database connection', 'select', $this->databases()],
            'table' => ['Table name', '', '', 'file'],
            'dir' => ['Upload directory', '', '', 'var/upload'],
            'use_crop' => ['Use crop', 'chk'],
            'crop_sizes' => ['Crop Sizes', '', '', '200 x 200,200 x 500'],
        ];
    }

    function vars() {
        $model = ant::model();
        $dd = $model->dd();
        $dir = $model->get_dir();
        $exist = $model->get_table($table);
        $object = $this;
        $tune = \Plan::_rq(['main', 'wares.php'])['upload']['tune'];
        return compact(array_keys(get_defined_vars()));
    }

    function install($mode) {
        $vars = $this->vars();
        return $vars + [
            'base' => $base = $vars['dd']->name,
            'ok' => in_array($base, $this->bases),
        ];
    }

    function uninstall($mode) {
        return $this->vars();
    }

    function update($mode) {
    }
}
