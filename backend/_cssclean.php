<?php
$path = __DIR__."/public/assets/css/custom-style.css";
$css = file_get_contents($path);

// Remove the old v1 block (from .aiz-file-box{position:relative;} before /* Bulk delete UI - admin uploaded files (v3)
$old = "/your custom css goes here/\n\n.aiz-file-box{position:relative;}\n.file-checkbox-card{position:relative;cursor:pointer;display:block;}\n.file-checkbox-card .file-checkbox-overlay{position:absolute;top:6px;left:6px;z-index:5;background:rgba(255,255,255,0.85);border-radius:4px;padding:4px 6px;line-height:1;}\n.file-checkbox-card .file-checkbox-overlay input[type=checkbox]{width:18px;height:18px;cursor:pointer;margin:0;}\n.file-checkbox-card:hover .file-checkbox-overlay{background:rgba(255,255,255,0.95);}\n.file-checkbox-card input[type=checkbox]:checked ~ .card-body h6 .title{text-decoration:line-through;color:#999;}\n.bulk-actions-bar{background:#fffbe6;border-top:1px solid #ffe58f;border-bottom:1px solid #ffe58f;padding:10px 15px;}\n.bulk-actions-bar .selected-count{font-size:14px;line-height:34px;}\n/";

if (strpos($css, $old) === 0) {
    $css = "/your custom css goes here/\n" . substr($css, strlen($old));
    file_put_contents($path, $css);
    echo "Cleaned. Size: ".filesize($path)."\n";
} else {
    echo "Pattern not at start. Trying alternate...\n";
    // Find first /* Bulk delete UI - admin uploaded files (v3
    $v3pos = strpos($css, "/* Bulk delete UI - admin uploaded files (v3");
    if ($v3pos !== false) {
        // Keep the comment and everything from there
        $css = "/your custom css goes here/\n\n" . substr($css, $v3pos);
        file_put_contents($path, $css);
        echo "Trimmed to v3. Size: ".filesize($path)."\n";
    }
}
