function sendPurchaseForm()
{
    let forwardLink = document.getElementById('forward-to-payment-link');

    forwardLink ? forwardLink.addEventListener('click',  (e) => {
        e.preventDefault();
    
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
            window.location.replace(`/purchase/${response.purchase_id}/payment/view`);
        })
    }) : null;
}

sendPurchaseForm();