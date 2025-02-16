console.log('ppcp logWorker');

onmessage = (e) => {

    console.log('ppcp logWorker', e, e.data);

    const formData = new FormData();
    formData.append("ppcp_session_id", e.data.ppcp_session_id);
    formData.append("fnc", e.data.fnc);
    formData.append("data", JSON.stringify(e.data.data));
    formData.append("error", JSON.stringify(e.data.error, Object.getOwnPropertyNames(e.data.error)));

    // JavaScript file-like object
    const blob = new Blob([e.data.error], { type: "text/xml" });
    ///formData.append("webmasterFile", blob);

    return fetch(
        e.data.url,
        {
            method: 'POST',
            redirect: "error",
            body: formData
            //body: JSON.stringify(e.data, Object.getOwnPropertyNames(e.data))
        }
    ).then((response) => {
        console.log('pcpp logWorker: msg sent.', response);
     }).catch(error => {
        console.error('PAYPAL_CHECKOUT_LOG error.', error);
    });
};
