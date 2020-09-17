function attachEventToChangeUserAddressToButton()
{
    let changeUserAddressButton = document.getElementById('change-user-address-button');

    changeUserAddressButton ? changeUserAddressButton.addEventListener('click', () => {
        changeUserAddressButton.setAttribute('disabled', 'true');
        showUserAddressForm();

        attachEventToUpdateUserAddressInDatabase();
    }) : null;
}