(function($) {
    $(document).ready(function() {
        events();
    });

    function events() {
        $('#add-image').click(function() {
            addImage();
        });

        $('.remove-image').live('click', function() {
            removeImage(this);
        });

        $('#remove-all-images').click(function() {
            removeAllImages();
        });

        $('.image_file_btn').live('click', function() {
            browseFiles(this);
        });

        $('input:file').live('change', function() {
            fileLabel(this.id);
        });
    }

    function addImage() {
        var separator = '    <li class="separator"></li>';

        if ($('.fields').children().length == 0) {
            separator = '';
        }

        var image = '<ul id="image-' + ifc + '">' +
                    separator +
                    '    <li>' +
                    '        <ul>' +
                    '            <li class="img-label">File:&nbsp;<a href="javascript:void(0)" class="remove-image" for="image-' + ifc + '">Remove</a></li>' +
                    '            <li>' +
                    '               <input type="text" id="image' + ifc + '_file_label" value="" disabled="disabled" />' +
                    '               <input type="button" id="image' + ifc + '_file_btn" class="form-button image_file_btn" for="image' + ifc + '_file" value="Browse" />' +
                    '               <input type="file" id="image' + ifc + '_file" name="image_file[img' + ifc + ']" value="" /></li>' +
                    '            <li class="clear"></li>' +
                    '        </ul>' +
                    '        <ul>' +
                    '            <li class="img-label">Link:</li>' +
                    '            <li>' +
                    '                <input type="text" id="image' + ifc + '_link" name="image_link[img' + ifc + ']" value="" />' +
                    '                   <select name="image_link_target[img' + ifc + ']">' +
                    '                       <option value="blank" selected="selected">Blank</option>' +
                    '                       <option value="self">Self</option>' +
                    '                   </select>' +
                    '            </li>' +
                    '            <li class="clear"></li>' +
                    '        </ul>' +
                    '        <ul>' +
                    '            <li class="img-label">Title:</li>' +
                    '            <li>' +
                    '                <input type="text" id="image' + ifc + '_title" name="image_title[img' + ifc + ']" value="" />' +
                    '                <select name="image_with_text[img' + ifc + ']">' +
                    '                    <option value="0" selected="selected">Without Text</option>' +
                    '                    <option value="1">With Text</option>' +
                    '                </select>' +
                    '            </li>' +
                    '            <li class="clear"></li>' +
                    '        </ul>' +
                    '        <ul>' +
                    '            <li class="img-label">Description:</li>' +
                    '                <li><textarea id="image' + ifc + '_description" name="image_description[img' + ifc + ']"></textarea></li>' +
                    '                <li class="clear"></li>' +
                    '        </ul>' +
                    '    </li>' +
                    '</ul>';

        $('li.images li.fields').append(image);
        ifc++;
    }

    function removeImage(elm) {
        var id = $(elm).attr('for');
        $('#' + id).remove();
    }

    function removeAllImages() {
        $('.fields > ul').remove();
    }

    function browseFiles(elm) {
        var id = $(elm).attr('for');
        $('#' + id).trigger('click');
    }

    function fileLabel(id) {
        var file = $('#' + id).val()
        id = '#' + id + '_label';
        if (file.indexOf('\\') != -1) {
            file = file.split('\\');
            file = file[file.length - 1];
        } else if (file.indexOf('/') != -1) {
            file = file.split('/');
            file = file[file.length - 1];
        }
        $(id.substring(0, id.indexOf('_') + 1) + 'id').val('');
        $(id).val(file);
    }
})(jQuery);
