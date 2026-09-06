<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$content = file_get_contents($path);

// Replace the old bulk action JS with v2 that toggles .is-selected class
$old = "        // Bulk action logic\n        (function(){\n            var \$form = \$('#bulk-action-form');\n            var \$bar  = \$('#bulk-actions-bar');\n            var \$count = \$('#selected-count');\n\n            function updateCount(){\n                var n = \$('.file-checkbox:checked').length;\n                \$count.text(n);\n                if (n > 0) { \$bar.show(); } else { \$bar.hide(); }\n            }\n\n            \$(document).on('change', '.file-checkbox', updateCount);\n            \$(document).on('click', function(e){\n                // ignore clicks inside the dropdown-menu or label checkbox itself\n                if (\$(e.target).closest('.dropdown-menu').length) return;\n                if (\$(e.target).closest('.file-checkbox').length) return;\n            });\n\n            \$('#bulk-clear-btn').on('click', function(){\n                \$('.file-checkbox').prop('checked', false);\n                updateCount();\n            });";

$new = "        // Bulk action logic v2
        (function(){
            var \$form  = \$('#bulk-action-form');
            var \$bar   = \$('#bulk-actions-bar');
            var \$count = \$('#selected-count');

            function refreshUI(){
                var n = 0;
                \$('.file-checkbox').each(function(){
                    var \$card = \$(this).closest('.file-checkbox-card');
                    if (this.checked) {
                        \$card.addClass('is-selected');
                        n++;
                    } else {
                        \$card.removeClass('is-selected');
                    }
                });
                \$count.text(n);
                if (n > 0) { \$bar.slideDown(150); } else { \$bar.slideUp(150); }
            }

            // Toggle selection when clicking anywhere on the card (except dropdown links)
            \$(document).on('click', '.file-checkbox-card', function(e){
                if (\$(e.target).closest('.dropdown-file, .dropdown-menu, .file-checkbox-overlay').length) return;
                var \$cb = \$(this).find('.file-checkbox');
                \$cb.prop('checked', !\$cb.prop('checked')).trigger('change');
            });

            // Prevent double-toggle when clicking the actual checkbox / overlay
            \$(document).on('click', '.file-checkbox-overlay', function(e){
                e.stopPropagation();
            });
            \$(document).on('click', '.file-checkbox', function(e){
                e.stopPropagation();
            });

            \$(document).on('change', '.file-checkbox', refreshUI);

            \$('#bulk-clear-btn').on('click', function(){
                \$('.file-checkbox').prop('checked', false);
                refreshUI();
            });";

if (strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    file_put_contents($path, $content);
    echo "Patched JS. New size: ".filesize($path)."\n";
} else {
    echo "Old JS block not found exactly. Trying alternate match...\n";
    // fallback - try to find the IIFE start
    $alt = "        // Bulk action logic";
    $altNew = "        // Bulk action logic v2
        (function(){
            var \$form  = \$('#bulk-action-form');
            var \$bar   = \$('#bulk-actions-bar');
            var \$count = \$('#selected-count');

            function refreshUI(){
                var n = 0;
                \$('.file-checkbox').each(function(){
                    var \$card = \$(this).closest('.file-checkbox-card');
                    if (this.checked) {
                        \$card.addClass('is-selected');
                        n++;
                    } else {
                        \$card.removeClass('is-selected');
                    }
                });
                \$count.text(n);
                if (n > 0) { \$bar.slideDown(150); } else { \$bar.slideUp(150); }
            }

            \$(document).on('click', '.file-checkbox-card', function(e){
                if (\$(e.target).closest('.dropdown-file, .dropdown-menu, .file-checkbox-overlay').length) return;
                var \$cb = \$(this).find('.file-checkbox');
                \$cb.prop('checked', !\$cb.prop('checked')).trigger('change');
            });
            \$(document).on('click', '.file-checkbox-overlay, .file-checkbox', function(e){ e.stopPropagation(); });
            \$(document).on('change', '.file-checkbox', refreshUI);
            \$('#bulk-clear-btn').on('click', function(){ \$('.file-checkbox').prop('checked', false); refreshUI(); });";
    if (strpos($content, $alt) !== false) {
        // find IIFE start
        $iifeStart = strpos($content, $alt);
        $iifeEnd = strpos($content, "        })();", $iifeStart);
        if ($iifeEnd !== false) {
            $iifeEnd += strlen("        })();");
            $content = substr($content, 0, $iifeStart) . $altNew . "\n\n            \$('#bulk-delete-btn').on('click', function(){\n                var n = \$('.file-checkbox:checked').length;\n                if (n === 0) {\n                    AIZ.plugins.notify('warning', '{{ translate('Please select at least one file.') }}');\n                    return;\n                }\n                \$('#bulk-delete-msg').text('{{ translate('Are you sure to delete') }} ' + n + ' {{ translate('file(s)? This cannot be undone.') }}');\n                \$('#bulk-delete-modal').modal('show');\n            });\n\n            \$('#bulk-delete-confirm').on('click', function(){\n                var ids = \$('.file-checkbox:checked').map(function(){ return this.value; }).get();\n                if (ids.length === 0) return;\n                \$form.find('input[name=\"id[]\"]').remove();\n                ids.forEach(function(id){\n                    \$('<input>').attr({type:'hidden', name:'id[]', value:id}).appendTo(\$form);\n                });\n                \$('#bulk-delete-confirm').prop('disabled', true).text('{{ translate('Deleting...') }}');\n                \$.ajax({\n                    url: \$form.attr('action'),\n                    method: 'POST',\n                    data: \$form.serialize(),\n                    headers: { 'X-CSRF-TOKEN': AIZ.data.csrf },\n                    success: function(){\n                        AIZ.plugins.notify('success', '{{ translate('Selected files deleted.') }}');\n                        setTimeout(function(){ location.reload(); }, 800);\n                    },\n                    error: function(){\n                        AIZ.plugins.notify('danger', '{{ translate('Delete failed. Please try again.') }}');\n                        \$('#bulk-delete-confirm').prop('disabled', false).text('{{ translate('Delete All') }}');\n                    }\n                });\n            });\n        })();\n" . substr($content, $iifeEnd);
            file_put_contents($path, $content);
            echo "Patched via fallback. New size: ".filesize($path)."\n";
        }
    }
}
