(function () {
    const el = {
        "wizTransaction": document.getElementById('wiz-transaction'),
        // name of the action to submit (POST) to the server
        "inputAction": document.getElementById('tr-action'),
        // (optional) : index of the transaction 
        "inputIndex": document.getElementById('tr-index')
    };

    const handleTransactionAction = (ev) => {
        debugger;
        const actionEl = ev.target.closest("[data-action]");
        let needSubmitForm = false;
        const action = actionEl.dataset.action;
        switch (action) {
            case 'add-transaction':
                el.inputAction.value = action;
                needSubmitForm = true;
                break;
            case 'remove-transaction':
                    el.inputAction.value = action;
                    el.inputIndex.value = actionEl.dataset.index;
                    needSubmitForm = true;
                    break;
                    needSubmitForm = true;
                break;
        }
        if (needSubmitForm) {
            // form name is defined in the view
            document.forms['tr-form'].submit();
        }
    };

    el.wizTransaction.addEventListener('click', handleTransactionAction);

})();