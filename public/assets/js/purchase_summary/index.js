let userAddressForm = document.getElementById('user-address-form');

userAddressForm ? userAddressForm.addEventListener('submit', (e) => {
    e.preventDefault();

    let formData = new FormData(document.getElementById('user-address-form'));

    fetch('/user/address/new', {
        method: 'POST',
        body: formData
    }).then(response => {
        return response.json();
    }).then(userAddress => {
        hideUserAddressForm();
        showUserAddressData(JSON.parse(userAddress));
    });
}) : null;

attachEventToChangeUserAddressToButton();