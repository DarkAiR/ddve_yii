;(function($) {
    'use strict';

    $.fn.translate = function(options) {
        var opts = $.extend({}, $.fn.translate.defaults, options);
        var $element = $(this);

        var isSended = false;

        $element.click( function() {
            if (isSended === true)
                return false;
            isSended = true;

            var data = {
                id: opts.modelId,
                model: opts.modelName,
                field: opts.fieldName
            };

            $.ajax({
                type: "POST",
                dataType: "JSON",
                cache: false,
                url: opts.url,
                data: data,
                success: function(data) {
                    if (data.errors) {
                        var errText = '';
                        $.each(data.errors, function(key, value) {
                            if (value.lang) {
                                errText += '['+value.lang+'] ';
                            }
                            switch (value.errMsg) {
                                case 'big-text':
                                    errText += 'Слишком большой текст\n\r';
                                    break;
                                case 'api-error':
                                    errText += 'Ошибка: '+value.code+'\n\r';
                                    break;
                                case 'api-not-response':
                                    errText += 'Перевод не получен\n\r';
                                    break;
                                case 'curl-error':
                                    errText += 'Ошибка запроса к Yandex Translate: '+value.code+'\n\r';
                                    break;
                                case 'not-save':
                                    errText += 'Не удалось сохранить данные в базе\n\r';
                                    break;
                                case 'already-translate':
                                    errText += 'Уже имеется перевод. Необходимо удалить его, чтобы перевести заново\n\r';
                                    break;
                                default:
                                    errText += 'Неизвестная ошибка\n\r';
                                    break;
                            }
                        });
                        window.alert(errText);
                    }
                    if (data.success) {
                        window.location.reload();
                    }
                },
                complete: function() {
                    isSended = false;
                },
                error: function(err) {
                    console.error(err);
                    isSended = false;
                }
            });
            return false;
        });
    };

    $.fn.translate.defaults = {
        modelName: '',
        modelId: 0,
        fieldName: '',
        url: ''
    };
})(jQuery);