(function () {

    const el = {
        "orderValueSum": document.getElementById('order-value-sum')
    };
    /**
     * Compute and return the sum of all order values or -1 if at last one order value
     * is not a number.
     */
    const computeInputValueSum = (selector) => Array.from(document.querySelectorAll(selector))
        .reduce((acc, cur) => {
            const num = Number(cur.value);
            if (cur.value.trim().length === 0 || isNaN(num) || acc == -1) {
                return -1;
            } else {
                return acc + num;
            }
        }, 0);

    const computeOrderValueSum = () => computeInputValueSum('#wiz-order .order-value');
    const renderValueSum = (selector, sumValue) => {
        const orderValueEl = document.querySelector(selector);
        if (orderValueEl) {
            orderValueEl.textContent = sumValue == -1 ? '????' : sumValue.toFixed(2);
            orderValueEl.dataset.sumValue = sumValue;
        }
    };
    const renderOrderValueSum = () => renderValueSum('#order-value-sum', computeOrderValueSum());

    const inputOrderValueChanged = (ev) => {
        console.log(ev);
        renderOrderValueSum();
    };

    document.querySelectorAll('input.order-value').forEach(input => {
        input.onchange = inputOrderValueChanged;
        input.oninput = inputOrderValueChanged;
    });


})();