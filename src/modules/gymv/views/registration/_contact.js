(function () {

    const model = {
        'searchResults' : [],
        'contact' : null,
        'address' : null
    };

    const el = {
        "contactName" : document.getElementById('contact-name-to-search'),
        "contactSearchResultList": document.getElementById('contact-result-list'),
        "contactResultInfo": document.getElementById('contact-result-info'),
        "searchButton" : document.getElementById('btn-search-contact'),
        "contactSearchUrl" : document.getElementById('contact-search-ws-url')
    };

    const contactSearchUrl = el.contactSearchUrl.value;

    const sendSearchContactRequest = (name) => new Promise((resolve, reject) => {
        // @ts-ignore
        $.getJSON(contactSearchUrl, { 
                "name": name,
                "expand" : "address" 
            }, function (data) {
                model.searchResults = data;
                resolve(data);
            }
        )
            .fail(function (jqxhr, textStatus, error) {
                console.error(error);
                reject(error);
            });
    });

    const renderSearchResults = (results) => {
        if(results.length == 0) {
            // noresult found
            el.contactResultInfo.innerHTML = 'No Result Found';
            el.contactSearchResultList.innerHTML = '<a href="#" data-action="new-contact">Create a new Contact ?</a>';
            
        } else {
            //render result list
            el.contactResultInfo.innerHTML = `${results.length} result(s) found`;
            el.contactSearchResultList.innerHTML = results.map( contact => {
                return `<a href="#" data-action="select-contact" data-contact-id="${contact.id}">${contact.name}</a>`;
            }).join('\n');

        }
    };

    const handleSearchError = (err) => {
        alert('oups ! something went wrong when searching for a contact .. sorry');
    };

    const searchContact = (ev) => {

        // @ts-ignore
        const contactName = el.contactName.value.trim();

        if (contactName.length == 0 ) {
            alert('enter the name or email to search');
            return;
        }

        sendSearchContactRequest(contactName)
            .then(renderSearchResults)
            .catch(handleSearchError);
    };

    const populateContactForm = (contact) => {
        
    };

    const selectContact = (ev) => {
        if(ev.target.dataset.action != 'new-contact') {
            model.contact = null; // new contact
        } else if(ev.target.dataset.action != 'select-contact') {
            const selectedContactId = ev.target.dataset.contactId;
            model.contact = model.searchResults.find( contact => contact.id == selectedContactId);
            populateContactForm(model.contact);
        } else {
            console.warn('unhandled action');
            return;
        }


    };
    el.searchButton.addEventListener('click', searchContact);
    el.contactSearchResultList.addEventListener('click', selectContact);
    
})();