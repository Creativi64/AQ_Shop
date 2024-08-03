console.log('ppcp logWorker');

onmessage = (e) => {

    return fetch(
        e.data.url,
        {
            method: 'POST',
            redirect: "error",
            body: JSON.stringify({ppcp_session_id: e.data.ppcp_session_id, fnc: e.data.fnc, data: e.data.data})
        }
    ).then((response) => {
        // console.error('pcpp logWorker: msg sent error.', response);
     }).catch(error => {
        console.error('PAYPAL_CHECKOUT_LOG error.', error);
    });
};
