function sendPurchaseForm()
{
    let forwardLink = document.getElementById('forward-to-payment-link');

    forwardLink ? forwardLink.addEventListener('click',  (e) => {
        e.preventDefault();
        if (!forwardLink.getAttribute('data-userAddressFilled')) {
            let noUserAddressErrorDiv = document.getElementById('no-user-address-error-div');

            noUserAddressErrorDiv.textContent = "Nie możesz dokonać zakupu bez podania adresu dostawy."

            return false;
        }

        if (forwardLink.getAttribute('data-alreadyClicked')) {
            return false;
        }

        /* Make sure user will not click many times, and by accident purchase the same order multiple times */
        forwardLink.setAttribute('data-alreadyClicked', true);
    
        let formData = new FormData();

        Array.from(checkedDeliveries).forEach((delivery) => {
            formData.append(`productDeliveryType[${delivery.getAttribute("data-productId")}]`, delivery.getAttribute('data-deliveryTypeId'));
        });

        formData.append('code', document.getElementById('code-input').value);

        e.preventDefault();
    
        fetch('/purchase/basket/buy', {
            method: 'POST',
            body: formData
        }).then(response => {
            return response.json();
        }).then(response => {
            if (response.prepayment) {
                window.location.replace(`/purchase/${response.purchase_id}/payment/view`);
            } else {
                window.location.replace(`/purchase/after/buy/message`);
            }
        })
    }) : null;
}

sendPurchaseForm();