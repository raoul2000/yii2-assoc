(function () {
    const el = {
        "wizTransaction": document.getElementById('wiz-transaction'),
        // name of the action to submit (POST) to the server
        "inputAction": document.getElementById('tr-action'),
        // (optional) : index of the transaction 
        "inputIndex": document.getElementById('tr-index'),
        "expectedTotalValue" : document.getElementById('expected-total-value'),
        "diffMarker" : document.getElementById('diff-marker')
    };

    // Handle Add/Remove Transaction /////////////////////////////////////////////////////////

    const handleTransactionAction = (ev) => {
        const actionEl = ev.target.closest("[data-action]");
        if(!actionEl) {
            return;
        }
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
        }
        if (needSubmitForm) {
            // form name is defined in the view
            document.forms['tr-form'].submit();
        }
    };

    el.wizTransaction.addEventListener('click', handleTransactionAction);

    // handle transaction value change and total value update //////////////////////////////////////

    const computeInputValueSum = (selector) => Array.from(document.querySelectorAll(selector))
        .reduce((acc, cur) => {
            const num = Number(cur.value);
            if (cur.value.trim().length === 0 || isNaN(num) || acc == -1) {
                return -1;
            } else {
                return acc + num;
            }
        }, 0);

    const renderValueSum = (selector, sumValue) => {
        const orderValueEl = document.querySelector(selector);
        if (orderValueEl) {
            orderValueEl.textContent = sumValue == -1 ? '????' : sumValue.toFixed(2);
            orderValueEl.dataset.sumValue = sumValue;
            // round 2 digit after coma
            const expectedTotal = Math.round(parseFloat(el.expectedTotalValue.dataset.value) * 100) / 100; 
            // round 2 digit after coma
            const sumValueRound = Math.round(sumValue * 100) / 100;
            // compare floats
            if(expectedTotal != sumValueRound) {
                el.diffMarker.classList.add('no-match');
            } else {
                el.diffMarker.classList.remove('no-match');
                
            }
        }
    };

    const inputTransactionValueChanged = (ev) => {
        renderValueSum('#transaction-value-sum', computeInputValueSum('.transaction-value'));

    };
    document.querySelectorAll('input.transaction-value').forEach(input => {
        input.onchange = inputTransactionValueChanged;
        input.oninput = inputTransactionValueChanged;
    });

    inputTransactionValueChanged(); // force update when loaded
})();