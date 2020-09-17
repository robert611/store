function hidePreviousUserAddressData()
{
    let userAddressDataDiv = document.getElementsByClassName('user-address-data')[0];
    let changeUserDataButton = document.getElementById('change-user-address-button');

    userAddressDataDiv ? userAddressDataDiv.remove() : null;

    changeUserDataButton ? changeUserDataButton.remove() : null;
}