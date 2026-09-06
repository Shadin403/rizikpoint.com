<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$content = file_get_contents($path);

// Replace the label-wrapped card with a div + sibling checkbox (outside label)
$oldLabel = "<label class=\"card card-file aiz-uploader-select c-default file-checkbox-card\"";
$openEnd = strpos($content, $oldLabel);
if ($openEnd === false) {
    echo "ERROR: label tag not found\n";
    exit;
}

// Find the closing </label> after it
$closeLabel = strpos($content, "</label>", $openEnd);
if ($closeLabel === false) {
    echo "ERROR: </label> not found\n";
    exit;
}

$labelBlock = substr($content, $openEnd, $closeLabel + strlen("</label>") - $openEnd);

// Build new block: same card but as <div>, with checkbox moved outside
// We strip the inline <div class="file-checkbox-overlay"> from inside the card
$newInner = preg_replace(
    "/\s*<div class=\"file-checkbox-overlay\">\s*<input[^>]*>\s*<\/div>\s*/",
    "",
    $labelBlock
);
// Change outer <label ...> to <div ...>
$newInner = preg_replace(
    "/^<label ([^>]*)>/",
    "<div $1>",
    $newInner
);
// Change closing </label> to </div>
$newInner = str_replace("</label>", "</div>", $newInner);

// Append a separate sibling for checkbox overlay (sits on top of card)
$checkboxBlock = <<<HTML
					</div>
					<div class="file-checkbox-overlay">
						<input type="checkbox" class="file-checkbox" value="{{ $file->id }}" data-name="{{ $file_name }}.{{ $file->extension }}" autocomplete="off">
					</div>
HTML;

$newBlock = $newInner . $checkboxBlock;

$content = substr($content, 0, $openEnd) . $newBlock . substr($content, $closeLabel + strlen("</label>"));

file_put_contents($path, $content);
echo "Patched. New size: ".filesize($path)."\n";
