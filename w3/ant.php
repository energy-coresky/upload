<?php

namespace upload;
use SKY, Plan, MVC;

class ant
{
    static function cfg() {
        return (object)SKY::$plans['upload']['app']['options'];
    }

    static function init($func = 'tail') {
        $tune = \Rare::tune('upload');
        Plan::$func(\js("upload.tune = '$tune'"), '~/w/upload/upload.js');
    }

    static function model() {
        return Plan::set('upload', fn() => MVC::$mc->x_able);
    }

    static function get_file($id, $is_download = false) {
        self::model()->get_file($id, $is_download);
        throw new \Stop;
    }

    static function type($fn, $real_name = null) {
        return self::model()->type($fn, $real_name);
    }
}
