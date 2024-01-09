<?php

namespace upload;
use Plan, SQL, Display, Form;

class ware extends \Wares
{
    public $bases = ['SQLite3', 'MySQLi'];
    public $engines = ['InnoDB', 'MyISAM'];

    function form() {
        return [
            'connection' => ['Database connection', 'select', \DEV::databases(['main'])],
            'table' => ['Table name', '', '', 'file'],
            'dir' => ['Upload directory', '', '', 'var/upload'],
            'use_acl' => ['Use ALC ware', 'chk'],
            'crop_sizes' => ['Crop Sizes', '', '', '200 x 200,200 x 500'],
        ];
    }

    function vars() {
        $model = ant::model();
        $dd = $model->dd();
        $dir = $model->get_dir();
        $exist = $model->get_table($table);
        $object = $this;
        $tune = Plan::_r(['main', 'wares.php'])['upload']['tune'];
        return get_defined_vars();
    }

    function install($mode) {
        $data = \view('ware.data', $vars = $this->vars());
        [$sql, $rewrite, $ajax] = explode("\n~\n", \unl($data));
        extract($vars, EXTR_REFS);
        if ($mode) {
            is_dir($dir) or mkdir($dir, 0777, true);
            if (!$exist) {
                $sql = str_replace('%engine%', $this->engines[$_POST['engine'] ?? 0], $sql);
                $dd->sqlf(SQL::NO_PARSE, trim($sql)); //2do: use migrations
            }
            echo 'OK';
            return;
        }
        $form = [
            's' => 'upload.install',
            'mode' => 1,
            ["Directory <b>$dir</b>", 'ni', is_dir($dir) ? 'exist' : 'NOT exist'],
            ["Create <b>$dir</b> dir", 'checkbox', ' disabled', !is_dir($dir)],
            ["Table <b>$table</b>", 'ni', $exist ? 'exist' : 'NOT exist'],
            ["Create <b>$table</b> table", 'checkbox', ' disabled', !$exist],
            ['', 'ni', \pre($sql)],
        ];
        if ('MySQLi' == $dd->name && !$exist)
            $form += ['engine' => ['Select %engine%', 'select', $this->engines]];
        unset($_POST['mode']);
        return [
            'md' => Display::md(Plan::_g('README.md')),
            'license' => Display::bash(Plan::_g('LICENSE')),
            'form' => Form::A([], $form + [
                9 => ['<b>Manual step:</b><br>Add/check rewrite for this ware', 'ni', $rewrite],
                ['Finalize', 'button', "onclick=\"$ajax\""]
            ]),
        ];
    }

    function uninstall($mode) {
        return $this->vars();
    }

    function update($mode) {
    }
}
