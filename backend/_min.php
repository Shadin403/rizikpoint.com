<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$content = file_get_contents($path);

// Only 2 changes:
// 1) <label ...> -> <div ...>
$content = str_replace(
    '<label class="card card-file aiz-uploader-select c-default file-checkbox-card" title="{{ $file_name }}.{{ $file->extension }}">',
    '<div class="card card-file aiz-uploader-select c-default file-checkbox-card" title="{{ $file_name }}.{{ $file->extension }}">',
    $content
);

// 2) </label> -> </div>  (only the first one inside the loop)
$content = preg_replace('/<\/label>/', '</div>', $content, 1);

file_put_contents($path, $content);
echo "Size: ".filesize($path)."\n";
