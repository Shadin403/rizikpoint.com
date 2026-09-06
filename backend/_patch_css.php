<?php
$path = __DIR__."/public/assets/css/custom-style.css";
$css = file_get_contents($path);

$newBlock = <<<CSS

/* Bulk delete UI - admin uploaded files (v2 - clearer selection) */
.aiz-file-box{position:relative;}

.file-checkbox-card{position:relative;cursor:pointer;display:block;
  border:2px solid transparent;border-radius:6px;transition:all .15s ease;
}
.file-checkbox-card:hover{border-color:#d6e4ff;box-shadow:0 0 0 2px rgba(24,144,255,.08);}
.file-checkbox-card.is-selected{border-color:#1890ff;background:#e6f4ff;box-shadow:0 0 0 2px rgba(24,144,255,.25);}

.file-checkbox-overlay{position:absolute;top:8px;left:8px;z-index:5;
  background:#fff;border:2px solid #d9d9d9;border-radius:4px;
  padding:3px;line-height:0;transition:all .15s ease;
  display:flex;align-items:center;justify-content:center;
  width:22px;height:22px;box-sizing:border-box;
}
.file-checkbox-card:hover .file-checkbox-overlay{border-color:#1890ff;}
.file-checkbox-card.is-selected .file-checkbox-overlay{
  background:#1890ff;border-color:#1890ff;
}
.file-checkbox-card .file-checkbox{
  width:14px;height:14px;cursor:pointer;margin:0;accent-color:#1890ff;
  position:relative;z-index:2;
}

.file-checkbox-card.is-selected .card-body h6{color:#1890ff;font-weight:600;}

.bulk-actions-bar{background:#e6f4ff;border-top:2px solid #1890ff;border-bottom:2px solid #1890ff;padding:10px 15px;}
.bulk-actions-bar .selected-count{font-size:14px;line-height:34px;color:#0050b3;font-weight:600;}
#bulk-delete-btn{background:#ff4d4f;border-color:#ff4d4f;}
#bulk-delete-btn:hover{background:#d9363e;border-color:#d9363e;}

.file-checkbox-card.is-missing .file-checkbox-overlay{border-color:#ff4d4f;}
.file-checkbox-card.is-missing::after{
  content:"\f119";font-family:"Line Awesome Free";font-weight:900;
  position:absolute;top:8px;right:8px;z-index:5;
  background:#fff;border:2px solid #ff4d4f;color:#ff4d4f;
  width:24px;height:24px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:12px;line-height:1;
}
CSS;

$marker = "/* Bulk delete UI - admin uploaded files */";
if (strpos($css, "/* Bulk delete UI - admin uploaded files (v2") === false) {
    $css = str_replace($marker, $newBlock, $css);
    file_put_contents($path, $css);
    echo "Replaced CSS. New size: ".filesize($path)."\n";
} else {
    echo "v2 already present.\n";
}
