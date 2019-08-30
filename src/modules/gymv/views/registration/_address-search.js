(function () {
    const el = {
        "address": document.getElementById('address'),
        "city": document.getElementById('city'),
        "buttonSearchAddress" : document.getElementById('btn-search-address'),
        "addressSearchUrl" : document.getElementById('address-search-ws-url'),
        "addressSearchResultList" : document.getElementById('address-search-result-list'),
        "form_record_id" : document.getElementById("address-record_id"),
        "form_line_1" : document.getElementById("address-line_1"),
        "form_zip_code" : document.getElementById("address-zip_code"),
        "form_city" : document.getElementById("address-city"),
        "form_country" : document.getElementById("address-country")
    };
    let addressData = [];

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
                <span class="address-name">${result.address}</span>
                <span class="address-zip">${result.zip_code}</span>
                <span class="address-city">${result.city}</span>
                <span class="address-country">${result.country}</span>
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
    const clearForm = (record) => {
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
        const resultItem = ev.target.closest('.result-address-item');
        if(!resultItem) {
            return;
        }
        const isSelected = resultItem.classList.contains('is-selected');
        // @ts-ignore
        el.addressSearchResultList.querySelectorAll('.result-address-item').forEach( item => item.classList.remove('is-selected'));
        if( ! isSelected) {
            resultItem.classList.add('is-selected');
            debugger;
            const index = resultItem.dataset.index;
            const record = addressData[index];
            loadForm(record);
        } else {
            clearForm();
        }
    };
    el.buttonSearchAddress.addEventListener('click', searchAddress);
    el.addressSearchResultList.addEventListener('click', selectResultItem);
})();