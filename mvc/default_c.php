<?php

class default_c extends Controller
{
    function j_crop_code() {
        $sizes = explode(',', Upload::cfg()->crop_sizes);
        return ['opt' => option(0, array_combine($sizes, $sizes))];
    }

    function j_crop($post) {
        $this->t_uvt->crop($post);
        return true;
    }

    function j_file_tmp() {
        $this->t_uvt->tmp($_FILES['file'] ?? false);
    }

    function j_delete($id) {
        $n = $this->t_uvt->remove(qp(' id=$.', $id));
        echo 1 == $n ? 'ok' : '-';
    }

    function a_test() {
        if (!DEV)
            return 404;
        echo implode('<br>', glob($this->t_uvt->get_dir() . '/*'));
    }
}
