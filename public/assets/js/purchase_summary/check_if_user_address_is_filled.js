function checkIfUserAddressIsFilled()
{
    let purchaseFormButton = document.getElementById('purchase-form-button');

    purchaseFormButton.addEventListener('click', (e) => {
        if (!purchaseFormButton.getAttribute('data-userAddressFilled')) {
            e.preventDefault();

            let noUserAddressErrorDiv = document.getElementById('no-user-address-error-div');

            noUserAddressErrorDiv.textContent = "Nie możesz dokonać zakupu bez podania adresu dostawy."
        } else {
            if (e.target.getAttribute('data-alreadyClicked')) {
                e.preventDefault();
            }
    
            /* Make sure user will not click many times, and by accident purchase the same order multiple times */
            e.target.setAttribute('data-alreadyClicked', true);
        }
    });
}

checkIfUserAddressIsFilled();