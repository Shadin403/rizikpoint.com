<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$c = file_get_contents($path);

// 1) Add position:relative to file-checkbox-card via inline class
$c = str_replace(
    '<div class="card card-file aiz-uploader-select c-default file-checkbox-card"',
    '<div class="card card-file aiz-uploader-select c-default file-checkbox-card" style="position:relative;"',
    $c
);

// 2) Move the file-checkbox-overlay INSIDE the file-checkbox-card, right after the opening tag
// First remove the existing overlay (which is currently a sibling AFTER the card)
$overlay = "					<div class=\"file-checkbox-overlay\">\n						<input type=\"checkbox\" class=\"file-checkbox\" value=\"{{ \$file->id }}\" data-name=\"{{ \$file_name }}.{{ \$file->extension }}\" autocomplete=\"off\">\n					</div>\n";
$pos = strpos($c, $overlay);
if ($pos === false) {
    // try slight variant
    $overlay2 = "					<div class=\"file-checkbox-overlay\">\n						<input type=\"checkbox\" class=\"file-checkbox\" value=\"{{ \$file->id }}\" data-name=\"{{ \$file_name }}.{{ \$file->extension }}\" autocomplete=\"off\">\n					</div>";
    $pos = strpos($c, $overlay2);
    if ($pos !== false) $overlay = $overlay2;
}
if ($pos === false) {
    echo "ERROR: overlay block not found\n";
    exit;
}
$c = substr($c, 0, $pos) . substr($c, $pos + strlen($overlay));

// 3) Insert it inside the card, right after the opening div
$cardOpen = '<div class="card card-file aiz-uploader-select c-default file-checkbox-card" style="position:relative;" title="{{ $file_name }}.{{ $file->extension }}">';
$insertion = "\n						<div class=\"file-checkbox-overlay\">\n							<input type=\"checkbox\" class=\"file-checkbox\" value=\"{{ \$file->id }}\" data-name=\"{{ \$file_name }}.{{ \$file->extension }}\" autocomplete=\"off\">\n						</div>";
$c = str_replace($cardOpen, $cardOpen . $insertion, $c);

file_put_contents($path, $c);
echo "Moved. Size: ".filesize($path)."\n";
