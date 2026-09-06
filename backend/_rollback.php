<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$lines = file($path);

// 1) Remove inline position:relative from card (we use aiz-file-box instead)
foreach ($lines as $i => $l) {
    if (strpos($l, "style=\"position:relative;\"") !== false) {
        $lines[$i] = str_replace(' style="position:relative;"', '', $lines[$i]);
        echo "Removed inline style from card line $i\n";
        break;
    }
}

// 2) Find the overlay block inside the card and remove it
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
echo "Overlay lines: $start to $end\n";

// Extract overlay block
$overlay = [];
for ($i = $start; $i <= $end; $i++) $overlay[] = $lines[$i];

// Remove it from card
array_splice($lines, $start, $end - $start + 1);

// 3) Find the closing </div> of the card (the file-checkbox-card). The card now is just the original 3 children.
$cardClose = -1;
$depth = 0;
for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], 'class="card card-file aiz-uploader-select c-default file-checkbox-card"') !== false) {
        $depth = 1;
        for ($j = $i+1; $j < count($lines); $j++) {
            $opens = substr_count($lines[$j], '<div');
            $closes = substr_count($lines[$j], '</div>');
            $depth += $opens - $closes;
            if ($depth === 0) { $cardClose = $j; break; }
        }
        break;
    }
}
echo "Card closes at line: $cardClose\n";

// 4) Insert the overlay after the card's closing </div>
$insert = [];
foreach ($overlay as $ol) {
    // Re-indent to be a child of aiz-file-box (5 tabs vs 6 tabs)
    $insert[] = preg_replace('/^\t+/', "\t\t\t\t\t\t", $ol);
}
array_splice($lines, $cardClose + 1, 0, $insert);

file_put_contents($path, implode("", $lines));
echo "Size: ".filesize($path)."\n";
