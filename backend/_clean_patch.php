<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$content = file_get_contents($path);

// 1) Replace opening label tag with div
$content = str_replace(
    '<label class="card card-file aiz-uploader-select c-default file-checkbox-card"',
    '<div class="card card-file aiz-uploader-select c-default file-checkbox-card"',
    $content
);

// 2) Remove the inline file-checkbox-overlay from inside the card
$content = preg_replace(
    '/[\t ]*<div class="file-checkbox-overlay">[\s\S]*?<\/div>[\s]*?(?=<\/div>[\s]*?<\/div>[\s]*?<\/div>[\s]*?<\/div>)/u',
    "",
    $content,
    1
);

// 3) Replace the FIRST occurrence of </label> with </div>
$pos = strpos($content, "</label>");
if ($pos !== false) {
    $content = substr_replace($content, "</div>", $pos, strlen("</label>"));
}

// 4) After that </div>, append a new sibling for the checkbox (still inside the .aiz-file-box)
$needle = "					</div>\n    				</div>\n    			</div>\n    		@endforeach";
$replacement = "					</div>\n					<div class=\"file-checkbox-overlay\">\n						<input type=\"checkbox\" class=\"file-checkbox\" value=\"{{ \$file->id }}\" data-name=\"{{ \$file_name }}.{{ \$file->extension }}\" autocomplete=\"off\">\n					</div>\n				</div>\n			</div>\n		</div>\n		@endforeach";

if (strpos($content, $needle) !== false) {
    $content = str_replace($needle, $replacement, $content);
    echo "Inserted sibling checkbox block.\n";
} else {
    // Try a more relaxed match
    $needle2 = "					</div>\n    				</div>\n";
    $pos2 = strpos($content, $needle2);
    if ($pos2 !== false) {
        $endPos = strpos($content, "</div>\n    		@endforeach", $pos2);
        if ($endPos !== false) {
            $insertion = "</div>\n					<div class=\"file-checkbox-overlay\">\n						<input type=\"checkbox\" class=\"file-checkbox\" value=\"{{ \$file->id }}\" data-name=\"{{ \$file_name }}.{{ \$file->extension }}\" autocomplete=\"off\">\n					</div>\n				";
            $content = substr($content, 0, $endPos) . $insertion . substr($content, $endPos);
            echo "Inserted via relaxed match.\n";
        } else {
            echo "WARN: end marker not found for relaxed match.\n";
        }
    } else {
        echo "WARN: needle2 not found.\n";
    }
}

file_put_contents($path, $content);
echo "Final size: ".filesize($path)."\n";
