$('#save-template-modal').on('shown.bs.modal', function (e) {
   
});

$('#save-template-modal').on('show.bs.modal', function (e) {
    
 });
 
 /**
  * Page action manager
  * Handle all actions emitted by click on element having "data-action" as attribute
  * or ancestors
  * 
  * @param {Event} ev 
  */
 const cartManagerActionHandler = (ev) => {
    const actionEl = ev.target.closest("[data-action]");
    if (actionEl) {
        ev.stopPropagation();
        ev.preventDefault();
        const actionName = actionEl.dataset.action;
        console.log(`calling action : ${actionName}`);

        switch (actionName) {
            default:
                document.getElementById('cart-action').value = actionEl.dataset.action;
                if (actionEl.dataset.index) {
                    document.getElementById('cart-index').value = actionEl.dataset.index;
                    console.log(`index : ${actionEl.dataset.index}`);
                }
                document.forms['cart-manager-form'].submit();
        }
    }
 };

$('#cart-manager-container').on('click', cartManagerActionHandler);

// order handlers /////////////////////////////////////////////////////

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

const computeOrderValueSum = () => computeInputValueSum('.order-value');
const computeTransactionValueSum = () => computeInputValueSum('.transaction-value');

const renderValueSum = (selector, sumValue) => {
    const orderValueEl = document.getElementById(selector);
    if (orderValueEl) {
        orderValueEl.textContent = sumValue == -1 ? '????' : sumValue.toFixed(2);
        orderValueEl.dataset.sumValue = sumValue;
    }
};
const renderOrderValueSum = () => renderValueSum('order-value-sum', computeOrderValueSum());
const renderTransactionValueSum = () => renderValueSum('transaction-value-sum', computeTransactionValueSum());

const computeOrderDiscountPercent = (productValue, orderValue) => {
    const discount = orderValue - productValue;
    return ((100 * discount) / productValue).toFixed(0);
};

const renderOrderDiscount = (inputValue) => {
    const orderValue = inputValue.value;
    const index = inputValue.id.split('-')[1];

    const orderDiscountEl = document.getElementById(`order-discount-${index}`);
    if (orderValue.trim().length === 0 || isNaN(orderValue)) {
        // hide discount item
        orderDiscountEl.value = "";
    } else {
        const productValue = document.getElementById(`order-${index}-product_id`).selectedOptions[0].dataset.value;
        const pcDiscount = computeOrderDiscountPercent(productValue, orderValue);
        if (pcDiscount == 0) {
            orderDiscountEl.value = "";
        } else {
            orderDiscountEl.value = pcDiscount;
        }
    }
};
/**
 * Handle the event triggered when the order value change.
 * If the order value is a number, update the discount value and the order value sum fields
 * 
 * @param {Event} ev the event to process
 */
const orderValueChange = (ev) => {
    renderOrderDiscount(ev.target);
    renderOrderValueSum();
};
$('.order-value').on('change input', orderValueChange);

/**
 * Copy the selected option data-value to the text content of another element
 * 
 * Each option of the select element must own a "data-value" attribute whose value
 * is copied to the target element
 * 
 * @param sel the select element. It must own a "data-target-id" attribute with the value
 * of the element to update
 */
const copySelectedProductValue = (sel) => {
    const targetElement = document.getElementById(sel.dataset.targetId);
    const productValue = sel.selectedOptions[0].dataset.value;
    targetElement.value = isNaN(productValue) ? '' : productValue;
};

const copyProductValueToOrderValue = (index) => {
    const productValue = document.getElementById(`order-${index}-product_id`).selectedOptions[0].dataset.value;
    const orderValueInput = document.getElementById(`order-${index}-value`);
    orderValueInput.value = isNaN(productValue) ? '' : productValue;
};
/**
 * Each time user selects a product :
 * - update product value display
 * - clear order value
 * 
 * @param {*} ev Event
 */
 const onSelectedProductChange = (ev) => {
    // get line index
    const index = ev.target.id.split('-')[1];

    // copy product value to order value
    copyProductValueToOrderValue(index);
    copySelectedProductValue(ev.target);

    // update order value sum
    renderOrderValueSum();

    // clear order value discount
    renderOrderDiscount(document.getElementById(`order-${index}-value`));
};
$('.orders select[data-product]').change(onSelectedProductChange);

/**
 * Handle change of the discount value.
 * When user change discount value, and if it is a valid number, compute and update the order value field
 * 
 * @param {*} ev Event
 */
const applyDiscount = (ev) => {
    const discount = ev.target.value;
    const index = ev.target.id.split('-')[2]; // element id example : order-discount-2

    if(!discount || discount.trim().length == 0) {
         document.getElementById(`order-${index}-value`).value =  document.getElementById(`product-value-${index}`).value;
    } else if(isNaN(discount)) {
        return;
    } else {
        const productValue = document.getElementById(`product-value-${index}`).value;
        if(isNaN(productValue)) {
            return;
        }
        const discountValue = Number(productValue) * Number(discount) / 100;
        console.log(discountValue);
        document.getElementById(`order-${index}-value`).value = Number(Number(productValue) + discountValue).toFixed(2);
    }
};
$('.order-discount').on('change input', applyDiscount);

// transaction handlers /////////////////////////////////////////////////////
const ventileOrderSumToTransactions = (ev) => {
    const transactionInputs = document.querySelectorAll('#transactions input.transaction-value');
    if (transactionInputs.length == 0) {
        return; // no transaction to report to
    }

    /*
    const totalTransactionValue = Array.from(document.querySelectorAll('#transactions input.transaction-value'))
        .reduce((acc, curr) => acc+ Number(curr.value), 0 );
    */
    const sumValue = document.getElementById('order-value-sum').dataset.sumValue;
    if (sumValue != -1) {
        const valueToReport = (Number(sumValue) / transactionInputs.length).toFixed(2);
        transactionInputs.forEach((el) => {
            el.value = valueToReport;
        });
        renderTransactionValueSum();
    }
};
$('#btn-report-sum-order').on('click', ventileOrderSumToTransactions);
$('.transaction-value').on('change input', renderTransactionValueSum);

/////////////////////////////////////////////////////////////////////////////
$(document).ready(() => {
    document.querySelectorAll('.orders select[data-product').forEach(copySelectedProductValue);
    document.querySelectorAll('input.order-value').forEach(renderOrderDiscount);

    renderOrderValueSum();
    renderTransactionValueSum();
});
