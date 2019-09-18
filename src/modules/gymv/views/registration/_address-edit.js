(function () {
    const el = {
        "buttonUpdateAddress": document.getElementById('btn-update-address'),
        "buttonResetForm": document.getElementById('btn-reset-form'),
        "readOnlyForm" : document.getElementById('read-only-form'),
        "addressEditForm" : document.getElementById('address-edit-form'),
        "action" : document.getElementById('action')
    };

    const unlockAddressForm = () => {
        el.addressEditForm.querySelectorAll('input[type=text]').forEach( input => {
            input.removeAttribute('readonly');
            el.buttonUpdateAddress.disabled = true;
        });
    };
    const lockAddressForm = () => {
        el.addressEditForm.querySelectorAll('input[type=text]').forEach( input => {
            input.setAttribute('readonly',true);
            el.buttonUpdateAddress.disabled = false;
        });
    };

    const resetForm = () => {
        el.action.value = 'reset-form';
        el.addressEditForm.submit();
    };
    
    el.buttonUpdateAddress.addEventListener('click', unlockAddressForm);
    el.buttonResetForm.addEventListener('click', resetForm);
    
    // on document ready //////////////////

    if(el.readOnlyForm.value) {
        lockAddressForm();
    } else {
        unlockAddressForm();
    }
})();