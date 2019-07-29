//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Form settings modal

/**
 * The global state for form settings. It is initialized 
 * with default values
 */
let formSettings = {
    "orderLockProvider" : false,
    "orderLockBeneficiary" : false,
    "orderEnableReport" : true
};

/**
 * Copy the value of the first selected item to the others.
 * If the selected result set doesn't contain more than one item, this function has no effect.
 * This function is used for example, to copy the first row contact provider to other order rows.
 * 
 * @param {string} selector Css selector for all the select input elements
 */
const copyFirstSelectedFirstRow = (selector) => {
    const arrSelect = Array.from(document.querySelectorAll(selector));
    if( arrSelect.length > 1) {
        const firstItemValue = arrSelect[0].value;   
        for (let index = 1; index < arrSelect.length; index++) {
            arrSelect[index].value = firstItemValue;        
        }
    }
};

/**
 * Enable/Disable elements selected exept for the first one.
 * 
 * @param {boolean} enable when true, enable selected elements. Disabled thm when false
 * @param {string} selector CSS selector for element to enable/disable
 */
const enableSelectedElements = (enable, selector) => {
    const arrSelect = Array.from(document.querySelectorAll(selector));
    if(arrSelect.length > 1) {
        for (let index = 1; index < arrSelect.length; index++) {
            arrSelect[index].disabled = !enable;        
        }
        if( ! enable ) {
            copyFirstSelectedFirstRow(selector);
        }
    }
};


/**
 * Load form settings from *localStorage* if available and assign them to 
 * the `formSetting` object.
 */
const readFormSettings = () => {
    const settingsStr = localStorage.getItem('settings');
    if( settingsStr ) {
        formSettings = JSON.parse(settingsStr);
    }
};

const applySettings = () => {
    enableSelectedElements(!formSettings.orderLockProvider, '.orders select[data-from-contact-id]');
    enableSelectedElements(!formSettings.orderLockBeneficiary, '.orders select[data-to-contact-id]');
};
/**
 * Load form settings into the modal used to edit settings
 */
const loadSettingsForm = () => {
    const modal = document.getElementById('form-settings-modal');
    modal.querySelector('#order-lock-provider').checked = formSettings.orderLockProvider;
    modal.querySelector('#order-lock-beneficiary').checked = formSettings.orderLockBeneficiary;
    modal.querySelector('#order-enable-report').checked = formSettings.orderEnableReport;
};
$('#form-settings-modal').on('show.bs.modal', loadSettingsForm);

/**
 * Save settings edited in the modal, into the localStorage and the global 
 * formSettings object
 */
const saveSettings = () => {
    const modal = document.getElementById('form-settings-modal');
    formSettings.orderLockProvider = modal.querySelector('#order-lock-provider').checked;
    formSettings.orderLockBeneficiary = modal.querySelector('#order-lock-beneficiary').checked;
    formSettings.orderEnableReport = modal.querySelector('#order-enable-report').checked;
    localStorage.setItem('settings', JSON.stringify(formSettings));    
};

$('#btn-save-form-settings').on('click', (ev) => {
    saveSettings();
    applySettings();
    $('#form-settings-modal').modal('hide'); // close modal
});



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Save As Template .. modal


const showContent = (selector) => {
    document.querySelectorAll('.modal-dialog .alert, .modal-dialog .form-group').forEach((el) => el.style.display = 'none');
    document.querySelector(`.modal-dialog ${selector}`).style.display = 'block';
}
const showTemplateNameInput = () => showContent('.form-group');
const showSavingTemplate = () => showContent('.alert-info');
const showSaveSuccess = () => showContent('.alert-success');
const showSaveError = () => showContent('.alert-danger');
const showStartButtons = () => {
    document.getElementById('btnbar-end').style.display = 'none';
    document.getElementById('btnbar-start').style.display = 'block';
};
const showEndButtons = () => {
    document.getElementById('btnbar-start').style.display = 'none';
    document.getElementById('btnbar-end').style.display = 'block';
};
const disableStartButtons = () => {
    document.querySelectorAll('#btnbar-start > button').forEach((el) => el.disabled = true);
}
const enableStartButtons = () => {
    document.querySelectorAll('#btnbar-start > button').forEach((el) => el.disabled = false);
}
const clearTemplateName = () => {
    document.getElementById('template-name').value = "";
};
const saveAsTemplate = (ev) => {
    const templateName = document.getElementById('template-name').value.trim();
    if (templateName == '') {
        alert('please enter a template name');
        return;
    }
    disableStartButtons();
    showSavingTemplate();
    document.getElementById('cart-template-name').value = templateName;
    document.getElementById('cart-action').value = 'save-as-template';

    const postURL = document.forms['cart-manager-form'].getAttribute('action');
    $.post(postURL, $('#cart-manager-form').serialize())
        .done((data) => {
            console.log(data);
            showSaveSuccess();
        })
        .fail((err) => {
            console.error(err);
            showSaveError();
        })
        .always(() => {
            showEndButtons();
        });
};
$('#btn-save-as-template').on('click', saveAsTemplate);

$('#save-template-modal').on('show.bs.modal', function (e) {
    enableStartButtons();
    showStartButtons();
    clearTemplateName();
    showTemplateNameInput();
});

/**
 * check that there is at least two identicals array items at the same index in arrays
 * 
 * @param {array} arr1 first array to compare
 * @param {array} arr2 second array to compare
 */
const sameArrayItemFound = (arr1, arr2) => {
    if(arr1.length !== arr1.length) {
        throw new Exception('both array must have the same number of items');
    }
    for(let idx=0; idx < arr1.length; idx++) {
        if (arr1[idx] != "" && arr1[idx] == arr2[idx]) {
            return true;
        }
    }    
    return false;
};

/**
 * Basic form validation
 * - ask confirmation if same account is defined as both source and target of a transaction
 * - ask confirmation if same contact is defined as both beneficiary and provide of an order
 */
const validateForm = () => {

    const fromContactIds = Array.from(document.querySelectorAll('table#orders > tbody > tr select[data-from-contact-id]')).map( (sel) => sel.selectedOptions[0].value);
    const toContactIds   = Array.from(document.querySelectorAll('table#orders > tbody > tr select[data-to-contact-id]')).map( (sel) => sel.selectedOptions[0].value);

    if(sameArrayItemFound(fromContactIds, toContactIds)) {
        if(!confirm('One or more orders have the same provider and consumer. Do you confirm it\'s ok ?' )) {
            return false;
        }
    } 

    const fromAccountIds = Array.from(document.querySelectorAll('table#transactions > tbody > tr select[data-from-account-id]')).map( (sel) => sel.selectedOptions[0].value);
    const toAccountIds   = Array.from(document.querySelectorAll('table#transactions > tbody > tr select[data-to-account-id]')).map( (sel) => sel.selectedOptions[0].value);
    if(sameArrayItemFound(fromAccountIds, toAccountIds)) {
        if(!confirm('One or more transactions have the same source and target. Do you confirm it\'s ok ?' )) {
            return false;
        }
    } 
    return true;
};


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



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
                if( actionName == 'submit' && validateForm() || actionName != 'submit') {
                    document.getElementById('cart-action').value = actionEl.dataset.action;
                    if (actionEl.dataset.index) {
                        document.getElementById('cart-index').value = actionEl.dataset.index;
                        console.log(`index : ${actionEl.dataset.index}`);
                    }
                    //we must enable select element in order to be included in the form submit operation
                    enableSelectedElements(true,'form select');
                    document.forms['cart-manager-form'].submit();
                }
        }
    }
};

$('#cart-manager-container').on('click', cartManagerActionHandler);


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// order handlers 


const contactChange = (ev) => {    
    const dataAttrSelector = ev.target.dataset.hasOwnProperty('fromContactId') ? 'data-from-contact-id' : 'data-to-contact-id';
    if( (dataAttrSelector == 'data-from-contact-id' && formSettings.orderLockProvider)
     || (dataAttrSelector == 'data-to-contact-id' && formSettings.orderLockBeneficiary)) {
         document.querySelectorAll(`.orders select[${dataAttrSelector}]`).forEach( (sel) => {
             sel.value = ev.target.value;
         });
     }
};

$('.orders select[data-from-contact-id], .orders select[data-to-contact-id]').change(contactChange);
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
    autoVentileOrderSumToTransactions();
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
 * Update the enable/disable state of the order value and discount, depending on the currently selected product
 * If no product is selected, both order value and discount are disabled.
 * 
 * @param {HTMLElement} productSelectElement product selection element
 */
const updateFromSelectedProduct = (productSelectElement) => {
    const index = productSelectElement.id.split('-')[1];
    if(!productSelectElement.value) {
        document.getElementById(`order-${index}-value`).disabled = true;
        document.getElementById(`order-discount-${index}`).disabled = true;
    } else {
        document.getElementById(`order-${index}-value`).disabled = false;
        document.getElementById(`order-discount-${index}`).disabled = false;
    }
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
/*
    if(!ev.target.value) {
        document.getElementById(`order-${index}-value`).disabled = true;
        document.getElementById(`order-discount-${index}`).disabled = true;
    } else {
        document.getElementById(`order-${index}-value`).disabled = false;
        document.getElementById(`order-discount-${index}`).disabled = false;
    }
*/
    updateFromSelectedProduct(ev.target);
    // copy product value to order value
    copyProductValueToOrderValue(index);
    copySelectedProductValue(ev.target);

    // update order value sum
    renderOrderValueSum();

    autoVentileOrderSumToTransactions();

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

    if (!discount || discount.trim().length == 0) {
        document.getElementById(`order-${index}-value`).value = document.getElementById(`product-value-${index}`).value;
    } else if (isNaN(discount)) {
        return;
    } else {
        const productValue = document.getElementById(`product-value-${index}`).value;
        if (isNaN(productValue)) {
            return;
        }
        const discountValue = Number(productValue) * Number(discount) / 100;
        console.log(discountValue);
        document.getElementById(`order-${index}-value`).value = Number(Number(productValue) + discountValue).toFixed(2);
        renderOrderValueSum();
        autoVentileOrderSumToTransactions();
    }
};
$('.order-discount').on('change input', applyDiscount);


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// transaction handlers 

/**
 * Compoute orders value sum and assign to transactions value.
 * If more than one transaction are available, the order's value sum is equaly dispatched among them.
 * 
 * @param {Event} ev current event
 */
const ventileOrderSumToTransactions = (ev) => {
    const transactionInputs = document.querySelectorAll('#transactions input.transaction-value');
    if (transactionInputs.length == 0) {
        return; // no transaction to report to : nothing to do here
    }

    const orderValueSum = Number(document.getElementById('order-value-sum').dataset.sumValue);
    if (orderValueSum != -1) {
        // compute and dispatch equal parts
        const valueToReport = ( orderValueSum / transactionInputs.length).toFixed(2);

        transactionInputs.forEach((el) => {
            el.value = valueToReport;
        });

        // if there is a remains, add it to the first trnsaction row
        const diff = orderValueSum - (valueToReport * transactionInputs.length);
        if(diff != 0) {
            transactionInputs.item(0).value =  Number(valueToReport) + Number(diff.toFixed(2));
        }
     } else {
        transactionInputs.forEach((el) => {
            el.value = "";
        });         
     }
    renderTransactionValueSum();
};
$('#btn-report-sum-order').on('click', ventileOrderSumToTransactions);
$('.transaction-value').on('change input', renderTransactionValueSum);

const autoVentileOrderSumToTransactions = () => {
    if(formSettings.orderEnableReport) {
        ventileOrderSumToTransactions();
    }
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// On Document Ready


$(document).ready(() => {
    readFormSettings();
    document.querySelectorAll('.orders select[data-product').forEach( (productSelectElement) => {
        copySelectedProductValue(productSelectElement);
        updateFromSelectedProduct(productSelectElement);
    });
    document.querySelectorAll('input.order-value').forEach(renderOrderDiscount);


    renderOrderValueSum();
    autoVentileOrderSumToTransactions();
    renderTransactionValueSum();
    applySettings();
});
