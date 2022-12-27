
$.fn.serializeFiles = function() {
    var obj = $(this);
    upload.post_files = true;
    var formData = new FormData();
    $.each($(obj).find("input[type='file']"), function(i, tag) {
        $.each($(tag)[0].files, function(i, file) {
            formData.append(tag.name, file);
        });
    });

    var params = $(obj).serializeArray();
    $.each(params, function (i, val) {
        formData.append(val.name, val.value);
    });
    return formData;
};

var upload = {
    controller: 'upload',
    max_filesize: 5000000,
    post_files: false,

    init: function() {
        $('form div.imgs, form div.files').each(function() {
            var el = $(this),
                input = el.next(),
                img = el.hasClass('imgs');
            el.on('dragover', function(e) {
                $(this).addClass('hover');
                return false;
            }).on('dragleave', function(e) {
                $(this).removeClass('hover');
                return false;
            }).on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('hover');
                input[0].files = e.originalEvent.dataTransfer.files;
                input.trigger('change');
                return false;
            }).on('click', function(e) {
                e.preventDefault();
                if (el.has('span')[0])
                    input.trigger('click');
                return false;
            });
            input.on('change', function() {
                var file = this.files[0];
                if (undefined === file)
                    return;
                if (file.size > upload.max_filesize) {// Also see .name, .type
                    sky.err('max upload size is ' + upload.max_filesize + ' bytes');
                } else {
                    upload.post_files = el.find('progress').show();
                    upload.post_files.prev().remove();
                    var formData = new FormData();
                    formData.append('file', file);
                    ajax('file_tmp', formData, function(r) {
                        el.html('<div class="doc-file-name">' + file.name + '</div>');
                        if (r.id !== undefined) {
                            el.siblings('input[type=hidden]').val(r.id);
                            var s0 = '<a href="javascript:;" class="delete-' + (img ? 'img' : 'doc')
                                + '" onclick="upload.file_delete(this, ' + r.id + ')"></a>';
                            el.append(s0);
                            if (r.img == 1 && img) { // crop image if required
                                ajax('crop_code', {}, function(h) {
                                    box(h);
                                    r.place = el;
                                    upload.crop(r);
                                }, upload.controller);
                            }
                        } else {
                            sky.err(r);
                        }
                    }, upload.controller);
                }
            });
        });
    },

    crop: function(r) { // crop images
        var td = $('table#crop td:eq(0)'), reg = td.find('div'), img = $('table#crop img:eq(0)'),
            td_w = td.width(), td_h = $('#box-in').height() - 15;
        $('table#crop span:eq(0)').text(r.width + ' x ' + r.height);
        td.height(td_h);
        img.attr('src', 'file?id' + r.id + '&_0').on('load', function() {
            var ratio = r.width / r.height > td_w / td_h, left, top, rw, rh, reg_css = function() {
                reg.css({
                    left: left = (left < 0 ? 0 : (left + rw > x ? x - rw - 2 : left)), width: rw,
                    top:   top = (top < 0 ? 0 : (top + rh > y ? y - rh - 2 : top)),    height: rh
                });
            };
            img.css({
                width: ratio ? td_w : 'auto',
                height: ratio ? 'auto' : 'inherit'
            });
            var x = img.width(), y = img.height(), minimal, size, flag = false;
            reg.click(function() {
                flag = !flag;
                $('table#crop span:eq(1)').text(flag ? 'crop' : 'move').css('background', flag ? '#fbb' : '#bfb');
            });
            var range = $('table#crop input[type=range]').on('input', function() {
                var val = minimal * this.value / 100 - 2;
                rw = val * (x == minimal ? 1 : ratio);
                rh = val / (x == minimal ? ratio : 1);
                reg_css();
            });
            $('table#crop select:eq(0)').change(function() {
                size = $(this).val().match(/(\d+)\D+(\d+)/);
                ratio = size[1] / size[2];
                minimal = ratio > x / y ? x : y;
                range.trigger('input');
            }).change();
            $('table#crop button:eq(0)').click(function() { // crop
                var q = {
                    x0: Math.round(left * r.width / x),
                    y0: Math.round(top * r.height / y),
                    x1: Math.round((left + rw) * r.width / x) + 2,
                    y1: Math.round((top + rh) * r.height / y) + 2,
                    id: r.id, szx: size[1], szy: size[2]
                };
                ajax('crop', q, function() {
                    var html = '<a class="delete-img" href="javascript:;" onclick="upload.file_delete(this, ' + r.id + ')"></a>';
                    r.place.html('<img style="position:absolute" src="file?id' + r.id + '&_"/>' + html);
                    sky.hide();
                }, upload.controller);
            });
            $(document).mousemove(function(e) {
                if (flag)
                    return;
                var os = img.offset();
                left = e.clientX - rw / 2 - os.left + $(this).scrollLeft();
                top = e.clientY - rh / 2 - os.top + $(this).scrollTop();
                reg_css();
            });
        });
    },

    file_delete_c: false,
    file_delete: function(el, id) {
        ajax('delete', {id:id}, function(r) {
            if ('ok' == r) {
                var imgs = $(el).parent(), tpl = imgs.siblings('div:eq(0)').html();
                imgs.html(tpl);
                imgs.siblings('input[type=hidden]').val('');
                if (upload.file_delete_c)
                    upload.file_delete_c();
            }
        }, upload.controller);//_file_delete
    },

    post: function(url, data, func) {
        if (!upload.post_files)
            return $.post(url, data, func);
        var el = upload.post_files === true ? false : upload.post_files;
        upload.post_files = false;
        $.ajax({
            url: url,
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST', // For jQuery < 1.9
            success: func,
            xhr: function() {
                var a = $.ajaxSettings.xhr();
                if (a.upload) {
                    a.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable && el) {
                            el.attr({
                                value: e.loaded,
                                max: e.total,
                            });
                        }
                    }, false);
                }
                return a;
            }
        });
    }
};

sky.post = upload.post;

$(function() {
    upload.init();


});
