let deliveryAddressDiv = document.getElementById('summary-delivery-address-div');

function showUserAddressData(userAddress)
{
    let div = document.createElement('div');
    div.setAttribute('class', 'user-address-data mt-1 col-12 col-md-6');

    div.innerHTML = `${userAddress.name} ${userAddress.surname} <br> 
        ${userAddress.address} <br>
        ${userAddress.zipCode} ${userAddress.city} <br>
        ${userAddress.phoneNumber}
    `;

    let changeAddressButton = document.createElement('button');
    changeAddressButton.setAttribute('class', 'mt-2 btn bg-inherit link-button');
    changeAddressButton.setAttribute('id', 'change-user-address-button');
    changeAddressButton.textContent = "ZMIEŃ ADRES";

    deliveryAddressDiv.appendChild(div); 
    deliveryAddressDiv.appendChild(changeAddressButton);    

    attachEventToChangeUserAddressToButton();
}