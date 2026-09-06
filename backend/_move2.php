<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$c = file_get_contents($path);

// 1) Add position:relative to file-checkbox-card via inline style
$c = str_replace(
    '<div class="card card-file aiz-uploader-select c-default file-checkbox-card"',
    '<div class="card card-file aiz-uploader-select c-default file-checkbox-card" style="position:relative;"',
    $c
);

// 2) Find and remove the existing overlay (it's after the card's </div>)
$pattern = '/[\t ]*<div class="file-checkbox-overlay">[\s\S]*?<\/div>\s*(?=[\t ]*<\/div>[\t ]*<\/div>[\t ]*<\/div>)/';
$newC = preg_replace($pattern, '', $c, 1, $count);
if ($count === 0) {
    // Try simpler - find the standalone overlay block
    $pattern2 = '/[\t ]*<div class="file-checkbox-overlay">\s*<input[^>]*>\s*<\/div>\s*/';
    $newC = preg_replace($pattern2, '', $c, 1, $count2);
    if ($count2 === 0) {
        echo "ERROR: could not find overlay to remove\n";
        exit;
    }
    echo "Removed overlay (fallback). ";
}
$c = $newC;

// 3) Insert overlay INSIDE the card, right after the opening tag
$cardOpen = '<div class="card card-file aiz-uploader-select c-default file-checkbox-card" style="position:relative;" title="{{ $file_name }}.{{ $file->extension }}">';
$insertion = "\n\t\t\t\t\t\t<div class=\"file-checkbox-overlay\">\n\t\t\t\t\t\t\t<input type=\"checkbox\" class=\"file-checkbox\" value=\"{{ \$file->id }}\" data-name=\"{{ \$file_name }}.{{ \$file->extension }}\" autocomplete=\"off\">\n\t\t\t\t\t\t</div>";
$c = str_replace($cardOpen, $cardOpen . $insertion, $c);

file_put_contents($path, $c);
echo "Moved. Size: ".filesize($path)."\n";
