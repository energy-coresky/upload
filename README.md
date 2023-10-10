
# Upload

Production ware. Use this product if your app need file uploads.
Ware contain 1 table in the database, 1 controller,
class Upload, 1 model to work with table, 1 js-file & 1 Jet template for visualization
crop images page.

Status: _under development_

Use this ware with rewrite for a_.. actions:
```php
if ($cnt && 'u' == $surl[0])
    common_c::$tune = array_shift($surl);
```

for j_.. actions:
```html
<script src="w/upload/upload.js"></script>
<script>upload.jact = 'u'</script>
```

Where 'u' - tuning value (any of `/^[\w+\-]+$/`)
