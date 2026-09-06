<?php
$path = __DIR__."/public/assets/css/custom-style.css";
$css = file_get_contents($path);

$append = <<<CSS

/* Bulk delete UI - admin uploaded files */
.aiz-file-box{position:relative;}
.file-checkbox-card{position:relative;cursor:pointer;display:block;}
.file-checkbox-card .file-checkbox-overlay{position:absolute;top:6px;left:6px;z-index:5;background:rgba(255,255,255,0.85);border-radius:4px;padding:4px 6px;line-height:1;}
.file-checkbox-card .file-checkbox-overlay input[type=checkbox]{width:18px;height:18px;cursor:pointer;margin:0;}
.file-checkbox-card:hover .file-checkbox-overlay{background:rgba(255,255,255,0.95);}
.file-checkbox-card input[type=checkbox]:checked ~ .card-body h6 .title{text-decoration:line-through;color:#999;}
.bulk-actions-bar{background:#fffbe6;border-top:1px solid #ffe58f;border-bottom:1px solid #ffe58f;padding:10px 15px;}
.bulk-actions-bar .selected-count{font-size:14px;line-height:34px;}
CSS;

if (strpos($css, "Bulk delete UI") === false) {
    file_put_contents($path, $css . $append);
    echo "Appended. New size: ".filesize($path)."\n";
} else {
    echo "Already appended.\n";
}
