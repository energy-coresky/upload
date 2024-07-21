<?php

class default_c extends Controller
{
    function j_crop_code() {
        if (!ACM::Rupload())
            throw new Hacker('ACL-upload popup');
        $sizes = explode(',', upload\ant::cfg()->crop_sizes);
        return ['opt' => option(0, array_combine($sizes, $sizes))];
    }

    function j_crop($post) {
        if (!ACM::Cupload())
            throw new Hacker('ACL-upload crop');
        $this->x_able->crop($post);
        return true;
    }

    function j_file_tmp() {
        if (!ACM::Xupload())
            throw new Hacker('ACL-upload tmp');
        $this->x_able->tmp($_FILES['file'] ?? false);
    }

    function j_delete($id) {
        if (!ACM::Dupload())
            throw new Hacker('ACL-upload delete');
        $n = $this->x_able->remove(qp(' id=$.', $id));
        echo 1 == $n ? 'ok' : '-';
    }

    function a_test() {
        if (!DEV)
            return 404;
        echo implode('<br>', glob($this->x_able->get_dir() . '/*'));
    }
}
