
var pp_express_product_form_selector_by_id         = "form[id=main_product_form]";
var pp_express_product_form_selector_fallback      = ".product-info form[name^=product]";

var pp_express_redirect_timeout = 15000;


document.addEventListener('DOMContentLoaded', function(event)
{
    var pp_express_href = "";
    var link = $(".paypal_checkout_product");
    if (link.length) {
        link = $(link[0]); // es darf auf product.html wirklich nur eins geben
        pp_express_href = link.attr('href');
    }

    $(".paypal_checkout_cart").click(function()
    {
        console.debug(this);
        openPpExpressModal();
    });

    $(".paypal_checkout_product").click(function()
    {
        try {
            console.debug(this);

            var form = $(pp_express_product_form_selector_by_id); // find by id (prefered)
            if(!form.length)
            {
                form = $(pp_express_product_form_selector_fallback);
            }

            if (form.length)
            {
                form = $(form[0]);
                var product = form.find("input[name=product]").val();
                if(!product)
                {
                    throw "products_id empty";
                }
                var qty = form.find("[id=form-qty-"+product+"]").val();
                if(!qty)
                {
                    qty = 1;
                }

                var params = [
                    { name: 'product', value: product },
                    { name: 'qty', value: qty }
                ];

                var param_s = $.param(params);

                $(this).attr('href', pp_express_href + "&" + param_s);

                console.debug($(this).attr('href'));

                openPpExpressModal();
            }
        }
        catch(e)
        {
            console.error('Error in pp button click: ', e)
        }
        //return false;
    });

    $(".paypal_checkout_listing").click(function()
    {
        // mal sehen
    });

});

function openPpExpressModal()
{
    $("#paypal_express_modal_redirecting").modal();

    setTimeout(function(){
        $("#paypal_express_modal_redirecting").modal('hide');
        alert('Oops,\n Zeitüberschreitung bei der Weiterleitung\nredirect timeout');
    }, pp_express_redirect_timeout);
}