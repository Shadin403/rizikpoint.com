<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$c = file_get_contents($path);

$old1 = "var \$box = \$(this).closest('.aiz-file-box');\n                    if (this.checked) { \$box.addClass('is-selected'); n++; }\n                    else { \$box.removeClass('is-selected'); }";
$new1 = "var \$col = \$(this).closest('.col-auto');\n                    if (this.checked) { \$col.addClass('is-selected'); n++; }\n                    else { \$col.removeClass('is-selected'); }";

if (strpos($c, $old1) !== false) {
    $c = str_replace($old1, $new1, $c);
    echo "Patched refreshUI\n";
}

$old2 = "var \$box = \$(this).closest('.aiz-file-box');\n                var \$cb = \$box.find('.file-checkbox');";
$new2 = "var \$col = \$(this).closest('.col-auto');\n                var \$cb = \$col.find('.file-checkbox');";

if (strpos($c, $old2) !== false) {
    $c = str_replace($old2, $new2, $c);
    echo "Patched click handler\n";
}

file_put_contents($path, $c);
echo "Size: ".filesize($path)."\n";
