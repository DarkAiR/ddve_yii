if (typeof jQuery === 'undefined') { throw new Error('jQuery required') }

+function ($) {
    'use strict';

    /**
     * Анимированное изменение содержимого элемента
     */
    $.fn.switchData = function (callbackFunc) {
        var $el = this;
        $el.animate({
            'opacity': 0.0
        }, 100, function() {
            $el.html('');
            callbackFunc.call($el);
            $el.animate({
                'opacity': 1.0
            }, 100);
        });
    }
}(jQuery);