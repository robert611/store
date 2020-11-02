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
    
        let deliveryMethodsData = new FormData();

        Array.from(checkedDeliveries).forEach((delivery) => {
            deliveryMethodsData.append(`productDeliveryType[${delivery.getAttribute("data-productId")}]`, delivery.getAttribute('data-deliveryTypeId'));
        });
    
        fetch('/purchase/basket/buy', {
            method: 'POST',
            body: deliveryMethodsData
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