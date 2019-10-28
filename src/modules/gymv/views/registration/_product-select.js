(function () {
    const el = {
        "selectedProductList" : document.getElementById('selected-product-list'),

        "achat_licence" : document.getElementById('achat_licence'),
        "container_assurance" : document.getElementById('container-assurance'),
        "container_achat_licence" : document.getElementById('container-achat-licence')
    };
    
    const escapeHTML = (txt) => document.createElement('div').appendChild(document.createTextNode(txt)).parentNode.innerHTML; 

    const addToSelectedProducts = (product) => {
        console.log("adding product ", product);
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
                + ` <input type="hidden" name="ProductSelectionForm[product_ids][]" value="${product.id}"/>
            </div>`;
            el.selectedProductList.insertAdjacentHTML('afterbegin',html);
        }
    }

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
    const handlerAssuranceChoice = (ev) => {
        console.log('handlerAssuranceChoice ' + ev.target.selectedOptions[0].value);

        el.container_assurance.style.visibility = ev.target.selectedOptions[0].dataset.hasOwnProperty('showOnSelect')
            ? 'visible'
            : 'hidden';
/*            

        const selectedOption = ev.target.selectedOptions[0];
        if( selectedOption.dataset.hasOwnProperty('showOnSelect')) {
            el.container_assurance.style.visibility = 'visible';
        } else {
            el.container_assurance.style.visibility = 'hidden';
        }
  */      
    };

    el.selectedProductList.addEventListener('click', handleProductAction);
    el.achat_licence.addEventListener('change', handlerAssuranceChoice);
    // @ts-ignore
    window.gymv = {
        addToSelectedProducts : addToSelectedProducts
    };
    
})();