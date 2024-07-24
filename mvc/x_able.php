<?php

namespace upload;
use function qp;

class t_able extends \Model_t
{
    private $image = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_GIF => 'gif',
    ];

    private $handle = [
        'jpg' => 'imagecreatefromjpeg',
        'png' => 'imagecreatefrompng',
        'gif' => 'imagecreatefromgif',
    ];

    private $dir;
    private $ext_in;
    //private $ext_out;

    function head_y() {
        $cfg = ant::cfg();
        $this->dir = $cfg->dir;
        $this->table = $cfg->table;
        return \SQL::open($cfg->connection, 'main');
    }

    function get_dir() {
        return $this->dir;
    }

    function get_table(&$table) {
        $table = $this->dd->pref . $this->table;
        return $this->dd->_tables($this->table);
    }

    function get_file($id, $is_download = false) {
        if (!$row = $this->one((int)$id, '>'))
            return;
        $ary = explode(' ', $row->type);
        $size = @filesize($fn = "$this->dir/$id.$ary[1]");
        if ($is_download && false === $size)
            return;
        while (ob_get_level())
            ob_end_clean();
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        $is_php = 'text/x-php' == $ary[0];
        $is_img = 'image/' == substr($ary[0], 0, 6);
        if ('text/' == substr($ary[0], 0, 5))
            $ary[0] = $is_php ? "text/html; charset=$ary[2]" : "$ary[0]; charset=$ary[2]";
        \MVC::mime($is_download ? 'application/octet-stream' : $ary[0]); # Content-Type

        if ($is_download) {
            header(sprintf('Content-Disposition: attachment; filename="%s"', $row->name));
            header("Content-Length: $size");
        }
        if ($is_php) {
            echo \css(['~/m/sky.css']);
            echo \Display::php(file_get_contents($fn));
        } else {
            readfile($fn);
        }
    }

    function remove($rule) {
        $cnt = 0;
        if ($tmp = $this->all($rule)) {
            $ids = [];
            foreach ($tmp as $id => $one) {
                $ary = explode(' ', $one->type);
                if (@unlink("$this->dir/$id.$ary[1]")) {
                    $ids[] = $id;
                    $cnt++;
                }
            }
            if ($ids) {
                $d = $this->delete(qp(' id in ($@)', $ids));
                $d == $cnt or $cnt--;
            }
        }
        return $cnt == count($tmp) ? $cnt : false;
    }

    function read_len($fn, $len = 1e4) {
        if (!$rc = fopen($fn, "rb"))
            throw new \Error("Cannot open file `$fn` for reading");
        $bin = fread($rc, $len);
        fclose($rc);
        return mb_strcut($bin, 0, $len);///????
    }

    function type($fn, $real_name = null) {
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($fn);
        $ext = pathinfo($real_name ?? $fn)['extension'] ?? '?';
        $type = "$mime $ext";
        $ary = ['img' => 0];
        if ('image/' == substr($mime, 0, 6)) {
            $data = getimagesize($fn);
            $ary += [
                'width' => $data[0],
                'height' => $data[1],
            ];
            $known = $this->image[$data[2]] ?? false;
            if ($known && $data[0] && $data[1]) {
                $ary['img'] = 1;
                $type = "$mime " . ($ext = $known);
            }
            $type .= " $data[0] $data[1]";
        } elseif ('text/' == substr($mime, 0, 5)) {
            $type .= ' ' . \Rare::enc_detect($this->read_len($fn));
        }
        return [$type, $ext, $ary];
    }

    function tmp($file) {
        global $user;
        $this->remove(qp('obj is null and ' . $this->dd->f_dt('dt_c', '+', 1, 'day') . ' < $now')); # delete unfinished files

        if (!$file) {
            echo 'File don\'t transfered';
            return;
        }
        if ($file['error']) {
            echo $file['error'];
            return;
        }
        [$type, $ext, $ary] = $this->type($file['tmp_name'], $file['name']);
        $id = $this->insert([
            '!dt_c' => '$now',
            '.c_user_id' => $user->id,
            'name' => $file['name'],
            'size' => $file['size'],
            'type' => $file['type'] = $type,
        ]);
        is_dir($this->dir) or mkdir($this->dir, 0777, true);
        if (move_uploaded_file($file['tmp_name'], "$this->dir/$id.$ext")) {
            \json($ary + ['id' => $id]);
            return;
        }
        $this->delete($id);
        echo 'File don\'t moved';
    }

    function crop($post) {
        extract($post);
        if (!$row = $this->one(qp(' id=$.', $id), '>'))
            return false;
        $ary = explode(' ', $row->type);
        $func = $this->handle[$ary[1]];
        $src = $func($fn = "$this->dir/$id.$ary[1]");
        $dst = imagecreatetruecolor($szx, $szy);
        imagecopyresampled($dst, $src, 0, 0, $x0, $y0, $szx, $szy, $x1 - $x0, $y1 - $y0);
        imagejpeg($dst, $fn, 80);
        //$this->update();
    }

    function img_load($fn) {
        [$x, $y, $type] = getimagesize($fn);
        if (!$this->ext_in = $this->image[$type] ?? false)
            return false;
        $func = $this->handle[$this->ext_in];
        return [$x, $y, $func($fn)];
    }

    function resize($to_width, $to_height) {
        $width = $x;
        $height = $y;
        $ratio = $x / $y;
        if ($ratio > $to_width / $to_height) {
            $x = $to_width;
            $y = floor($x / $ratio);
        } else {
            $y = $to_height;
            $x = floor($y * $ratio);
        }
        $out = imagecreatetruecolor($x, $y);
        imagecopyresampled($out, $im, 0, 0, 0, 0, $x, $y, $width, $height);
        imagejpeg($out, \WWW . "img/task/$fn_out", 80);
    }
}
