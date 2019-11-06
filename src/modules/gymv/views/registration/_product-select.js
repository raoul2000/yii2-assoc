(function () {
    const el = {
        "selectedProductList"     : document.getElementById('selected-product-list'),
        "achat_licence"           : document.getElementById('achat_licence'),
        "container_assurance"     : document.getElementById('container-assurance'),
        "container_achat_licence" : document.getElementById('container-achat-licence'),
        "chk_justif_certificate"  : document.getElementById('chk_justif_certificate'),
        "certif_validity_date"    : document.getElementById('certif_validity_date'),
    };
    
    const escapeHTML = (txt) => document.createElement('div').appendChild(document.createTextNode(txt)).parentNode.innerHTML; 

    /**
     * Add a Product to the currently selected product list.
     * Creates an renders a new HTML element representing the newly added product
     * If the same product is already selected, displays an alert error message
     * @param {object} product product to add
     */
    const addToSelectedProducts = (product) => {
        // prevent multiselection of the same product
        const productAlreadySelected = el.selectedProductList.querySelector('div.selected-product-item[data-item-id="' + product.id + '"]');
        if(productAlreadySelected) {
            alert(product.name + "\nproduct already selected");
        } else {
            const html = `
            <div class="selected-product-item" data-item-id="${product.id}">
                <div class="product-remove" title="remove">
                    <span data-action="remove" class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                </div>
                <span class="product-name">
                    <span class="glyphicon glyphicon-gift" aria-hidden="true"></span> ${escapeHTML(product.name)}
                </span>`
                + ( product.short_description 
                        ? `<span class="product-short-description">
                                ${escapeHTML(product.short_description)}
                            </span>`
                        : ''
                    )
                + ` <input type="hidden" name="ProductSelectionForm[cours_ids][]" value="${product.id}"/>`
                + ` <input type="hidden" name="ProductSelectionForm[product_ids][]" value="${product.id}"/>
            </div>`;
            el.selectedProductList.insertAdjacentHTML('afterbegin',html);
        }
    }

    /**
     * Remove an item from the product selection list
     * @param {Event} ev the DOM event
     */
    const handleProductAction = (ev) => {
        const action = ev.target.dataset.action;
        switch(action) {
            case 'remove':
                const container = ev.target.closest('div[data-item-id]');
                if(container) {
                    container.parentNode.removeChild(container);
                }
                break;
        }
    };
    /**
     * show/hide the "assurance" checkbox depending on currently selected option 
     * element and the fact it owns a showOnSelect data attribute
     */
    const handlerAssuranceChoice = () => {
        el.container_assurance.style.visibility = el.achat_licence.selectedOptions[0].dataset.hasOwnProperty('showOnSelect')
            ? 'visible'
            : 'hidden';
    };
    /**
     * When the user check/uncheck the 'a fourni un certificat' the valid date range input bowes is enabled/disabled
     * 
     * @param {Event} ev checkbox change event
     */
    const onChkCertificateChange = (ev) => {
        el.certif_validity_date.querySelectorAll('input').forEach(input => input.disabled = ! el.chk_justif_certificate.checked);
    }
    // main //////////////////////////////////////////////////////////////////

    el.selectedProductList.addEventListener('click', handleProductAction);
    el.achat_licence.addEventListener('change', handlerAssuranceChoice);
    el.chk_justif_certificate.addEventListener('change', onChkCertificateChange);

    handlerAssuranceChoice(); // force render update
    onChkCertificateChange(); // force render update

    // add function "addToSelectedProducts" to the global scope so it can be used by selectizejs 
    // when user selects a product item 
    // @ts-ignore
    window.gymv = {
        addToSelectedProducts : addToSelectedProducts
    };
})();