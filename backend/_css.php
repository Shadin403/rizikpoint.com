<?php
$path = __DIR__."/public/assets/css/custom-style.css";
$css = file_get_contents($path);

$new = <<<CSS

/* Bulk delete UI - admin uploaded files (v4 - final) */
.aiz-file-box{position:relative;display:block;}

.file-checkbox-card{cursor:pointer;display:block;
  border:2px solid transparent;border-radius:6px;transition:all .15s ease;
  position:relative;overflow:visible;
}
.file-checkbox-card:hover{border-color:#d6e4ff;box-shadow:0 0 0 2px rgba(24,144,255,.08);}
.aiz-file-box.is-selected .file-checkbox-card{border-color:#1890ff;background:#e6f4ff;box-shadow:0 0 0 2px rgba(24,144,255,.25);}
.aiz-file-box.is-selected .file-checkbox-card .card-body h6{color:#1890ff;font-weight:600;}

.file-checkbox-overlay{position:absolute;top:6px;left:6px;z-index:10;
  background:#fff;border:2px solid #d9d9d9;border-radius:4px;
  padding:3px;line-height:0;transition:all .15s ease;
  display:flex;align-items:center;justify-content:center;
  width:24px;height:24px;box-sizing:border-box;
}
.aiz-file-box:hover .file-checkbox-overlay{border-color:#1890ff;}
.aiz-file-box.is-selected .file-checkbox-overlay{background:#1890ff;border-color:#1890ff;}
.file-checkbox-overlay input[type=checkbox]{
  width:14px;height:14px;cursor:pointer;margin:0;accent-color:#1890ff;
  position:relative;z-index:11;
}

.bulk-actions-bar{background:#e6f4ff;border-top:2px solid #1890ff;border-bottom:2px solid #1890ff;padding:10px 15px;}
.bulk-actions-bar .selected-count{font-size:14px;line-height:34px;color:#0050b3;font-weight:600;}
#bulk-delete-btn{background:#ff4d4f;border-color:#ff4d4f;}
#bulk-delete-btn:hover{background:#d9363e;border-color:#d9363e;}

.aiz-file-box.is-missing .file-checkbox-overlay{border-color:#ff4d4f;}
.aiz-file-box.is-missing::after{
  content:"\\f119";font-family:"Line Awesome Free";font-weight:900;
  position:absolute;top:6px;right:6px;z-index:10;
  background:#fff;border:2px solid #ff4d4f;color:#ff4d4f;
  width:22px;height:22px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:11px;line-height:1;
}
CSS;

// Replace v3 block
$v3 = strpos($css, "/* Bulk delete UI - admin uploaded files (v3");
if ($v3 !== false) {
    // Find end of v3 block - the last } before any next section
    $end = strpos($css, "}", strrpos($css, "}"));
    $css = substr($css, 0, $v3) . trim($new) . "\n";
    file_put_contents($path, $css);
    echo "Replaced. Size: ".filesize($path)."\n";
} else {
    file_put_contents($path, $css . $new);
    echo "Appended. Size: ".filesize($path)."\n";
}
