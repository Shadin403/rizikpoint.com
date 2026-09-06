<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$c = file_get_contents($path);

// Update the refreshUI function: toggle on col-auto instead of aiz-file-box
$old = "function refreshUI(){
                var n = 0;
                $('.file-checkbox').each(function(){
                    var $box = $(this).closest('.aiz-file-box');
                    if (this.checked) { $box.addClass('is-selected'); n++; }
                    else { $box.removeClass('is-selected'); }
                });
                $count.text(n);
                if (n > 0) { $bar.slideDown(150); } else { $bar.slideUp(150); }
            }";

$new = "function refreshUI(){
                var n = 0;
                $('.file-checkbox').each(function(){
                    var $col = $(this).closest('.col-auto');
                    if (this.checked) { $col.addClass('is-selected'); n++; }
                    else { $col.removeClass('is-selected'); }
                });
                $count.text(n);
                if (n > 0) { $bar.slideDown(150); } else { $bar.slideUp(150); }
            }";

if (strpos($c, $old) !== false) {
    $c = str_replace($old, $new, $c);
    echo "Patched refreshUI\n";
}

// Update card click handler
$old2 = "$(document).on('click', '.file-checkbox-card', function(e){
                if ($(e.target).closest('.dropdown-file, .dropdown-menu').length) return;
                var $box = $(this).closest('.aiz-file-box');
                var $cb = $box.find('.file-checkbox');
                $cb.prop('checked', !$cb.prop('checked'));
                refreshUI();
            });";

$new2 = "$(document).on('click', '.file-checkbox-card', function(e){
                if ($(e.target).closest('.dropdown-file, .dropdown-menu').length) return;
                var $col = $(this).closest('.col-auto');
                var $cb = $col.find('.file-checkbox');
                $cb.prop('checked', !$cb.prop('checked'));
                refreshUI();
            });";

if (strpos($c, $old2) !== false) {
    $c = str_replace($old2, $new2, $c);
    echo "Patched click handler\n";
}

file_put_contents($path, $c);
echo "Size: ".filesize($path)."\n";
