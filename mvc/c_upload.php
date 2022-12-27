<?php

class c_upload extends Controller
{
    function empty_a() {
        return [
        ];
    }

    function a_list() {
    }

    function j_crop_code() {
        $cfg = array_explode(unl(Plan::cfg_g('options.cfg')));
        $sizes = explode(',', $cfg['crop_sizes']);
        return ['opt' => option(0, array_combine($sizes, $sizes))];
    }

    function j_crop() {
        $this->t_uvt->crop($_POST['id'], $_POST['x0'], $_POST['y0'], $_POST['x1'], $_POST['y1'], $_POST['szx'], $_POST['szy']);
        return true;
    }

    function j_file_tmp() {
        $this->t_uvt->tmp($_FILES['file'] ?? false);
    }

    function j_delete($id) {
        $n = $this->t_uvt->remove(qp(' id=$.', $id));
        echo 1 == $n ? 'ok' : '-';
    }
}
