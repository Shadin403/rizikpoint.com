<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$lines = file($path);

// Find lines containing the overlay
$overlayStart = -1; $overlayEnd = -1;
for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], '<div class="file-checkbox-overlay">') !== false) {
        $overlayStart = $i;
        // closing div is on the next 2 lines
        for ($j = $i+1; $j < count($lines); $j++) {
            if (preg_match('/^\s*<\/div>\s*$/', $lines[$j])) {
                $overlayEnd = $j;
                break;
            }
        }
        break;
    }
}
echo "Overlay start: $overlayStart, end: $overlayEnd\n";
if ($overlayStart === -1) { echo "Not found\n"; exit; }

// Find the card opening
$cardOpenLine = -1;
for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], 'class="card card-file aiz-uploader-select c-default file-checkbox-card"') !== false) {
        $cardOpenLine = $i;
        break;
    }
}
echo "Card opens at: $cardOpenLine\n";
if ($cardOpenLine === -1) { echo "Card not found\n"; exit; }

// 1) Add position:relative to card
$lines[$cardOpenLine] = str_replace(
    '<div class="card card-file aiz-uploader-select c-default file-checkbox-card"',
    '<div class="card card-file aiz-uploader-select c-default file-checkbox-card" style="position:relative;"',
    $lines[$cardOpenLine]
);

// 2) Extract overlay lines
$overlayLines = [];
for ($i = $overlayStart; $i <= $overlayEnd; $i++) {
    $overlayLines[] = $lines[$i];
}

// 3) Remove overlay from its current position (higher line numbers first)
array_splice($lines, $overlayStart, $overlayEnd - $overlayStart + 1);

// 4) Insert overlay inside the card, right after the opening line
// Find the new position of cardOpenLine (unchanged because we removed later lines)
$insertion = [];
foreach ($overlayLines as $ol) {
    $insertion[] = "\t\t\t\t\t\t" . ltrim($ol);
}
array_splice($lines, $cardOpenLine + 1, 0, $insertion);

file_put_contents($path, implode("", $lines));
echo "Moved. Size: ".filesize($path)."\n";
