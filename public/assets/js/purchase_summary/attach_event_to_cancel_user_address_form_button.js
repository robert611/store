function attachEventToCancelUserAddressFormButton()
{
    let cancelUserAddressFormButton = document.getElementById('cancel-user-address-update');

    cancelUserAddressFormButton.addEventListener('click', (e) => {
        e.preventDefault();

        let changeUserAddressButton = document.getElementById('change-user-address-button');
        changeUserAddressButton.removeAttribute('disabled');

        hideUserAddressForm();
    });
}