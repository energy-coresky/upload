<?php

class Upload
{
    static $image = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_GIF => 'gif',
    ];

    static function js_up() {
        ;
    }

    static function model() {
        $prev = Plan::set('upload');
        $model = MVC::$mc->t_uvt;
        Plan::$ware = $prev;
        return $model;
    }

    static function get_file($id, $is_download = false) {
        Upload::model()->get_file($id, $is_download);
        throw new Stop;
    }

    static function read_len($fn, $len = 1e4) {
        if (!$rc = fopen($fn, "rb"))
            throw new Error("Cannot open file `$fn` for reading");
        $bin = fread($rc, $len);
        fclose($rc);
        return mb_strcut($bin, 0, $len);///????
    }

    static function type($fn, $real_name = null) {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($fn);
        $ext = pathinfo($real_name ?? $fn)['extension'] ?? '?';
        $ap = "$mime $ext";
        $out = ['img' => 0];
        if ('image/' == substr($mime, 0, 6)) {
            $data = getimagesize($fn);
            $out += [
                'width' => $data[0],
                'height' => $data[1],
            ];
            $tmp = self::$image[$data[2]] ?? false;
            if ($tmp && $data[0] && $data[1]) {
                $out['img'] = 1;
                $ap = "$mime " . ($ext = $tmp);
            }
            $ap .= " $data[0] $data[1]";
        } elseif ('text/' == substr($mime, 0, 5)) {
            $ap .= ' ' . Rare::enc_detect(self::read_len($fn));
        }
        return [$ap, $ext, $out];
    }
}
