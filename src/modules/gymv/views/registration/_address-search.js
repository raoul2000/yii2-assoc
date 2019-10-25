(function () {
    const el = {
        "address": document.getElementById('address'),
        "city": document.getElementById('city'),
        "buttonSearchAddress" : document.getElementById('btn-search-address'),
        "addressSearchResultList" : document.getElementById('address-search-result-list'),
        "addressSearchForm" : document.getElementById('address-search-form'),
        "form_record_id" : document.getElementById("address-record_id"),
        "form_line_1" : document.getElementById("address-line_1"),
        "form_zip_code" : document.getElementById("address-zip_code"),
        "form_city" : document.getElementById("address-city"),
        "form_country" : document.getElementById("address-country"),
        "search_address" : document.getElementById("search-address"),
        "search_city" : document.getElementById("search-city"),
        "buttonAddressSearchNext" : document.getElementById('btn-address-search-next')
    };
    let addressData = [];

    const escapeHTML = (txt) => document.createElement('div').appendChild(document.createTextNode(txt)).parentNode.innerHTML; 

    const sendSearchAddressRequest = (address, city) => new Promise((resolve, reject) => {
        // @ts-ignore
        $.getJSON("?r=gymv/registration/ajax-address-search", { "address": address, "city": city }, function (data) {
            console.log("success");
            addressData = data;
            resolve(data);
        })
            // @ts-ignore
            .fail(function (jqxhr, textStatus, error) {
                console.error(error);
                reject(error);
            });
    });

    const renderSearchResults = (results) => {
        el.addressSearchResultList.innerHTML = results.map( (result, index) => {
            return `
            <div class="result-address-item" data-item-id="${result.id}" data-index="${index}">
                <div class="selected-address">
                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> 
                </div>
               
                <span class="address-name"> ${escapeHTML(result.address)}</span>
                <span class="address-zip">${escapeHTML(result.zip_code)}</span>
                <span class="address-city">${escapeHTML(result.city)}</span>
                <span class="address-country">${escapeHTML(result.country)}</span>` 
                + ( result.id 
                        ? `<span class="extra-info-line">
                                <span class="glyphicon glyphicon-star" aria-hidden="true"></span> 
                                <a href="${result.urlView}" target="_blank" class="view-address"> view </a>
                            </span>`
                        : ''
                    )
                + `
            </div>`;
        }).join('\n');
    };

    
    const handleSearchError = (err) => {
        alert('search error');
    };

    
    const searchAddress = (ev) => {
        // @ts-ignore
        const addressToSearch = el.address.value.trim();
        // @ts-ignore
        const cityToSearch = el.city.value.trim();

        if (addressToSearch.length == 0) {
            alert('enter an address to search for');
            return;
        }
        sendSearchAddressRequest(addressToSearch, cityToSearch)
            .then(renderSearchResults)
            .catch(handleSearchError);
    };

    const loadForm = (record) => {
        // @ts-ignore
        el.form_record_id.value = record.id;
        // @ts-ignore
        el.form_line_1.value = record.address;
        // @ts-ignore
        el.form_zip_code.value = record.zip_code;
        // @ts-ignore
        el.form_city.value = record.city;
        // @ts-ignore
        el.form_country.value = record.country;
    };
    // @ts-ignore
    const clearForm = () => {
        // @ts-ignore
        el.form_record_id.value = null;
        // @ts-ignore
        el.form_line_1.value = null;
        // @ts-ignore
        el.form_zip_code.value = null;
        // @ts-ignore
        el.form_city.value = null;
        // @ts-ignore
        el.form_country.value = null;
    };
    const selectResultItem = (ev) => {

        if( ev.target.closest('.view-address')) {   // user clicked the "view address" link
            return;
        }
        const resultItem = ev.target.closest('.result-address-item');
        if(resultItem) { // user clicked on an address item
            const isSelected = resultItem.classList.contains('is-selected');
            // @ts-ignore
            el.addressSearchResultList.querySelectorAll('.result-address-item').forEach( item => item.classList.remove('is-selected'));
            if( ! isSelected) {
                resultItem.classList.add('is-selected');
                const index = resultItem.dataset.index;
                const record = addressData[index];
                loadForm(record);
            } else {
                clearForm();
            }            
        }
    };

    /**
     * When submitting the form we want to copy search terms (address and city) into the form
     * so they are also submitted.
     * This is because if the user didn't select any result, the server will used search terms
     * to pre populate a newx address record
     * 
     * @param {Event} ev event
     */
    const submitForm = (ev) => {
        ev.stopPropagation();
        ev.preventDefault();

        el.search_address.value = el.address.value;
        el.search_city.value = el.city.value;
        console.log(el.search_address.value);
        el.addressSearchForm.submit();
    };
    el.buttonSearchAddress.addEventListener('click', searchAddress);
    el.addressSearchResultList.addEventListener('click', selectResultItem);
    el.buttonAddressSearchNext.addEventListener('click', submitForm);
})();