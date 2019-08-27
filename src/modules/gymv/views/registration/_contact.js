(function () {

    const ui = {
        "contactNameOrEmailInput" : document.getElementById('contact-name-or-email'),
        "contactSearchResultList": document.getElementById('contact-result-list'),
        "searchButton" : document.getElementById('btn-search-contact')
    };

    const sendSearchContactRequest = (nameOrEmail) => new Promise((resolve, reject) => {
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
        const contactNameOrEmail = ui.contactNameOrEmailInput.value.trim();

        if (contactNameOrEmail.length == 0 ) {
            alert('enter the name or email to search');
            return;
        }

        sendSearchContactRequest(contactNameOrEmail)
            .then(renderSearchResults)
            .catch(handleSearchError);
    };

    ui.searchButton.addEventListener('click', searchContact);
})();