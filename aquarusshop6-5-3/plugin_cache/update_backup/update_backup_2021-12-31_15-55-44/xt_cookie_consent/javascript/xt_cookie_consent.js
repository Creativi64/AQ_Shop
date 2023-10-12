function xt_cookie_consent_accept(acceptAll, lifeTime, secure)
{
    if(typeof secure === 'undefined' || secure === null || secure === "") secure = false;

    let data = {};
    for (let cookieType in cookie_settings.topics) {
        if (Object.prototype.hasOwnProperty.call(cookie_settings.topics, cookieType))
        {
            let setVal = cookieType === 'FUNCTIONAL' ? true : acceptAll;
            data[cookieType] = { "allowed": setVal };
        }
    }

    // expires
    //let d = new Date();
    //d.setTime(d.getTime() + lifeTime); // in milliseconds

    let cookie = {'max-age': lifeTime, 'SameSite' : 'Strict' }
    if (secure) cookie.secure = true;
    xtSetCookie(COOKIE_CONSENT_COOKIE_NAME, btoa(JSON.stringify(data)), cookie);
    //window.location.reload();
    $('#cookie-consent').hide();
}

function xt_cookie_consent_init(themeId)
{
    switch(themeId)
    {
        case 1:
            break;
        case 2:
            let el = $('#cookie-consent').detach();
            // in xt_responsive template prepend to div[data-role="page"]
            // adapt to your needs/template
            if ($('div[data-role="page"]').length>0) $('div[data-role="page"]').prepend(el);
            // in xt_grid template prepend to body
            else $('body').prepend(el);
            break;
        default:
            break;
    }
}

function xt_cookie_consent_show()
{
    $('#cookie-consent').show();
}

