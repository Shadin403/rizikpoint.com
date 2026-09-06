<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$content = file_get_contents($path);

// Step 1: Convert the opening <label ...> tag to <div ...>
$old1 = '<label class="card card-file aiz-uploader-select c-default file-checkbox-card" title="{{ $file_name }}.{{ $file->extension }}">';
$new1 = '<div class="card card-file aiz-uploader-select c-default file-checkbox-card" title="{{ $file_name }}.{{ $file->extension }}">';
$content = str_replace($old1, $new1, $content);

// Step 2: Move the <div class="file-checkbox-overlay">...</div> out of the card
// Find: <div class="card-body">...</div>  followed by  <div class="file-checkbox-overlay">...</div>  followed by  </div>  (card close)
// We will remove the overlay from inside and add it as a sibling after </div> of the card

// Remove the inline overlay div
$content = preg_replace(
    '/\s*<div class="file-checkbox-overlay">\s*<input type="checkbox" class="file-checkbox" value="\{\{ \$file->id \}\}" data-name="\{\{ \$file_name \}\}\.\{\{ \$file->extension \}\}">\s*<\/div>/s',
    '',
    $content,
    1
);

// Step 3: Change first </label> to </div>
$content = preg_replace('/<\/label>/', '</div>', $content, 1);

// Step 4: Insert the checkbox overlay as a sibling AFTER the </div> that closes the .file-checkbox-card
// This </div> is right before "</div>\n    				</div>\n    		</div>"  (end of col + aiz-file-box + col-auto)
$marker = "					</div>\n    				</div>\n";
$insert = "					</div>\n					<div class=\"file-checkbox-overlay\">\n						<input type=\"checkbox\" class=\"file-checkbox\" value=\"{{ \$file->id }}\" data-name=\"{{ \$file_name }}.{{ \$file->extension }}\" autocomplete=\"off\">\n					</div>\n				</div>\n";
$content = str_replace($marker, $insert, $content);

file_put_contents($path, $content);
echo "Size: ".filesize($path)."\n";
