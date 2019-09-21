(function () {
    const el = {
        "selectedProductList" : document.getElementById('selected-product-list'),
    };
    
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
                    <span class="glyphicon glyphicon-gift" aria-hidden="true"></span> ${product.name}
                </span>
                <input type="hidden" name="ProductSelectionForm[product_ids][]" value="${product.id}"/>
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
    el.selectedProductList.addEventListener('click', handleProductAction);
    // @ts-ignore
    window.gymv = {
        addToSelectedProducts : addToSelectedProducts
    };
    
})();