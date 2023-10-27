
# Upload

Production ware. Use this product if your app need upload files. Custom croping images included.

Essence | Count
:----- | :-----
Installer class | present (**upload\\ware**)
_w3_ class | 1 (**upload\\ant**)
Table in the database | 1, custom name
Controller | 1, tune used
Model | 1
Jet templates | 2
_Asset_ files | 1 (**upload.js**)


Status: _under development_

## Rewrite for a_.. actions:
```php
if ($cnt && 'upload' == $surl[0]) {
    common_c::$tune = array_shift($surl);
    $cnt--;
}
```

## For j_.. actions add to HTML:
```html
<script src="w/upload/upload.js"></script>
<script>upload.jact = 'upload'</script>
```

Where 'upload' - tuning value (any of `/^[\w+\-]+$/`)
