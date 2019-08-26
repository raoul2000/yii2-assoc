const ui = {
    "addressSearchInput": document.getElementById('address-search'),
    "addressSearchResultList" : document.getElementById('address-result-list')
};

const sendSearchAddressRequest = (term) => new Promise((resolve, reject) => {
    // @ts-ignore
    $.getJSON("https://api-adresse.data.gouv.fr/search", { q: term }, function (data) {
        console.log("success");
        resolve(data);
    })
        .fail(function (jqxhr, textStatus, error) {
            console.error(error);
            reject(error);
        });
});

const renderSearchResults = (results) => {
    ui.addressSearchResultList.innerHTML = results.features.map( (feature => {
        return `<p>${feature.properties.name}</p>`;
    })).join('\n');
};

const handleSearchError = (err) => {

};

const searchAddress = (ev) => {

    // @ts-ignore
    const addressToSearch = ui.addressSearchInput.value.trim();
    if (addressToSearch.length == 0) {
        alert('enter an address to search for');
        return;
    }
    sendSearchAddressRequest(addressToSearch)
        .then(renderSearchResults)
        .catch(handleSearchError);
};

document.getElementById('btn-search-address').addEventListener('click', searchAddress);