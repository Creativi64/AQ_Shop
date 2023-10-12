/**
 *
 */
document.addEventListener("DOMContentLoaded", function(event) {
    $.fn.delayedKeyup = function( delay, callback, options ) {

        return this.each(function() {
            $(this).bindWithDelay('keyup', callback, delay);
        });

    };

});

