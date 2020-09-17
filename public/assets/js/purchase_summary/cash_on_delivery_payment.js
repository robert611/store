/* If user choose to use cash-on delivery type of delivery, then there is no need to go to payment controller */
function cashOnDeliveryPayment(deliveryTypeId)
{
    let forwardToPaymentLink = document.getElementById('forward-to-payment-link');

    let productId = document.getElementById('product-id-input').value;

    forwardToPaymentLink.textContent = "";
    forwardToPaymentLink.appendChild(createSpinnerWidget('dark'));

    setTimeout(function() {
        forwardToPaymentLink.textContent = "Kupuję";
        forwardToPaymentLink.setAttribute('href', `/purchase/${productId}/${deliveryTypeId}/buy`);
        document.getElementById('payment-progress-bar').style.width = "100%";
    }, 600);
}