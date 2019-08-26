(function () {

    const ui = {
        "contactNameInput" : document.getElementById('contact-name'),
        "contactFirstnameInput" : document.getElementById('contact-firstname'),
        "contactSearchResultList": document.getElementById('contact-result-list'),
        "searchButton" : document.getElementById('btn-search-contact')
    };

    const sendSearchContactRequest = (name, firstname) => new Promise((resolve, reject) => {
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
        ui.contactSearchResultList.innerHTML = results.features.map((feature => {
            return `<p>${feature.properties.name}</p>`;
        })).join('\n');
    };

    const handleSearchError = (err) => {

    };

    const searchContact = (ev) => {

        // @ts-ignore
        const contactName = ui.contactNameInput.value.trim();
        // @ts-ignore
        const contactFirstname = ui.contactFirstnameInput.value.trim();

        if (contactName.length == 0 || contactFirstname.length == 0) {
            alert('enter the name and firstname');
            return;
        }

        sendSearchContactRequest(contactName, contactFirstname)
            .then(renderSearchResults)
            .catch(handleSearchError);
    };

    ui.searchButton.addEventListener('click', searchContact);
})();