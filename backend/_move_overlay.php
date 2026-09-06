<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$lines = file($path);

// Find the .col-auto opening tag with the file
for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], 'class="col-auto w-140px w-lg-220px"') !== false) {
        $colLine = $i;
        break;
    }
}
echo "col-auto opens at line: $colLine\n";

// Add position:relative inline to col-auto
$lines[$colLine] = str_replace(
    'class="col-auto w-140px w-lg-220px"',
    'class="col-auto w-140px w-lg-220px" style="position:relative;"',
    $lines[$colLine]
);

// Find and remove the existing overlay block (currently inside .aiz-file-box)
$start = -1; $end = -1;
for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], '<div class="file-checkbox-overlay">') !== false) {
        $start = $i;
        for ($j = $i; $j < count($lines); $j++) {
            if (preg_match('/^\s*<\/div>\s*$/', $lines[$j]) && $j > $i) {
                $end = $j;
                break;
            }
        }
        break;
    }
}
echo "Overlay: $start to $end\n";

$overlay = [];
for ($i = $start; $i <= $end; $i++) $overlay[] = $lines[$i];

// Remove from aiz-file-box (later lines, so splice first)
array_splice($lines, $start, $end - $start + 1);

// Recompute colLine - it should still be the same since we removed later lines
// Insert overlay right after the col-auto opening div (colLine)
$insert = [];
foreach ($overlay as $ol) {
    // Indent: col-auto is at 4 tabs, its children should be at 5 tabs
    $insert[] = preg_replace('/^\t+/', "\t\t\t\t\t", $ol);
}
array_splice($lines, $colLine + 1, 0, $insert);

file_put_contents($path, implode("", $lines));
echo "Size: ".filesize($path)."\n";
