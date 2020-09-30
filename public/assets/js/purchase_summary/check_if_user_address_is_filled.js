function checkIfUserAddressIsFilled()
{
    let forwardLink = document.getElementById('forward-to-payment-link');

    forwardLink ? forwardLink.addEventListener('click', (e) => {
        if (!forwardLink.getAttribute('data-userAddressFilled')) {
            e.preventDefault();

            let noUserAddressErrorDiv = document.getElementById('no-user-address-error-div');

            noUserAddressErrorDiv.textContent = "Nie możesz dokonać zakupu bez podania adresu dostawy."
        }
    }) : null;
}

checkIfUserAddressIsFilled();