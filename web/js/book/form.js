jQuery(document).ready(function () {
    $('.add-new').click(function (e) {
        var $target = $(e.target);

        $('.add-new').not(this).popover('hide');

        $target.popover({
            content: $('#add-new-popover').html(),
            html: true,
            trigger: 'manual'
        }).popover('show');

        $('.save').click(function () {
            var name = $target.parent().find('.popover .name').val();

            $target.popover('hide');

            if (name.trim()) {
                $.ajax({
                    url: $target.data('url'),
                    type: 'POST',
                    dataType: 'json',
                    data: {name: name.trim()},
                    success: function (data) {
                        if (data.status == 'OK') {
                            $.notify(
                                $target
                                    .data('success-message')
                                    .replace(/%name%/gi, name),
                                {
                                    className: 'success'
                                }
                            );

                            if ($target.data('id')) {
                                $('#' + $target.data('id')).append(
                                    $('<option>')
                                        .attr('value', data.data.id)
                                        .text(name.trim())
                                ).selectpicker('refresh');
                            } else {
                                $('.' + $target.data('class')).append(
                                    $('<option>')
                                        .attr('value', data.data.id)
                                        .text(name.trim())
                                ).selectpicker('refresh');
                            }
                        } else {
                            $.notify(data.data.name[0], {
                                className: 'error'
                            });
                        }
                    },
                    error: function (request, status, error) {
                        $.notify(request.responseText, {
                            className: 'error'
                        });
                    }
                });
            }
        });

        $('.hide-popover').click(function () {
            $target.popover('hide');
        });

    });

    $("#upload-image").change(function () {
        var input = this;

        if (input.files && input.files[0]) {
            var fileData = new FormData();

            fileData.append('imageFile', input.files[0]);

            $.ajax({
                url: $('#image-wrapper img').data('url'),
                type: "POST",
                data: fileData,
                processData: false,
                contentType: false,
                xhr: function () {
                    var xhr = $.ajaxSettings.xhr();

                    xhr.upload.onload = function () {
                        var reader = new FileReader();

                        reader.onload = function (e) {
                            $('#image-wrapper img').attr('src', e.target.result);
                        };

                        reader.readAsDataURL(input.files[0]);
                    };

                    return xhr;
                },
                success: function (data) {
                    if (data.errors) {
                        for (var i in data.errors) {
                            $.notify(data.errors[i], {
                                className: 'error'
                            });
                        }
                    }
                }
            });

        }
    });

    $("#image").click(function () {
        $("#upload-image").click();
    });

    $("#upload-book").click(function () {
        $("#upload-book-modal").modal('show');
    });

    $(".select-file").click(function () {
        $("#upload-file").click();
    });

    $(".format").change(function () {
        var that = $(this),
            selected = that.find("option:selected"),
            inputs = that.closest('.format-select').find('.upload-book'),
            input = inputs.last();

        if (inputs.length == 1) {
            var clone = input.parent().clone();
            input.parent().after(clone);
            input = clone.find('.upload-book');
        }

        input.off('change').change(function () {
            var input = this,
                $input = $(this);

            if (input.files && input.files[0]) {
                var inputParent = $input.parent();

                inputParent.after(inputParent.clone());
                inputParent
                    .find('.file-extension')
                    .text(selected.text().toUpperCase() + ': ');
                inputParent
                    .removeClass('hide')
                    .find('.book-name')
                    .text(input.files[0].name);
                inputParent
                    .find('.book-format')
                    .val(selected.text());

                $('.format-pattern .delete-file').click(deleteFile);

                inputParent.find('*:not(.progress)').hide();

                var fileData = new FormData();

                fileData.append('bookFile', input.files[0]);

                $.ajax({
                    url: $('.format-pattern').data('url'),
                    type: "POST",
                    data: fileData,
                    processData: false,
                    contentType: false,
                    xhr: function () {
                        var xhr = $.ajaxSettings.xhr();

                        xhr.upload.onprogress = function (evt) {
                            var progress = evt.loaded / evt.total * 100;
                            inputParent
                                .find('.progress .progress-bar')
                                .css('width', progress + '%')
                                .text(progress);
                        };

                        xhr.upload.onload = function () {
                            inputParent.find('*:not(.progress)').show();
                            inputParent.find('.progress').hide();
                        };

                        return xhr;
                    },
                    success: function (data) {
                        if (data.errors) {
                            for (var i in data.errors) {
                                $.notify(data.errors[i], {
                                    className: 'error'
                                });
                            }
                        }
                    }
                });
            }
        }).attr('accept', '.' + selected.text()).click();

        that.find(':selected').prop('selected', false);
        that.find('.empty').prop('selected', true);
        that.selectpicker('refresh');
    });


    $("#book-form").on('keypress', 'input[type="text"], input[type="number"], textarea', function (e) {
        var $target = $(e.target);

        delay(function () {
            var formFields = {};

            formFields[$target.attr('name')] = $target.val();


            $.ajax({
                type: 'POST',
                url: $('#formId').data('url'),
                dataType: 'json',
                data: {
                    formId: $('#formId').val(),
                    formFields: formFields
                }
            });
        }, 800);
    }).on('change', 'select', function (e) {
        var $target = $(e.target),
            formFields = {},
            selected = [];

        $target.find(":selected").each(function (index, elem) {
            selected.push($(elem).val());
        });

        formFields[$target.attr('id')] = selected;

        $.ajax({
            type: 'POST',
            url: $('#formId').data('url'),
            dataType: 'json',
            data: {
                formId: $('#formId').val(),
                formFields: formFields
            }
        });
    });

    $('.format-pattern .delete-file').click(deleteFile);

    function deleteFile()
    {
        var that = $(this);

        $.ajax({
            url: $('.format-pattern').data('url') + '?remove=1&file=' + that.parent().find('.book-name').text(),
            success: function (data) {
                if (data.errors) {
                    for (var i in data.errors) {
                        $.notify(data.errors[i], {
                            className: 'error'
                        });
                    }
                } else {
                    that.closest(".format-pattern").remove();
                }
            }
        });
    }

    var delay = (function () {
        var timer = 0;
        return function (callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();
});
