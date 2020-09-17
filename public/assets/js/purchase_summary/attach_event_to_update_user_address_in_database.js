function attachEventToUpdateUserAddressInDatabase()
{
    let form = document.getElementById('update-user-address-form');

    form ? form.addEventListener('submit',  (e) => {
        e.preventDefault();
    
        let formData = new FormData(form);
    
        fetch('/user/address/edit', {
            method: 'POST',
            body: formData
        }).then(response => {
            return response.json();
        }).then(userAddress => {
            hideUserAddressForm();
            hidePreviousUserAddressData();

            return userAddress;
        }).then((userAddress) => {
            showUserAddressData(JSON.parse(userAddress));
        });
    }) : null;
}