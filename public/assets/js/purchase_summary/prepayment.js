/* Change forward link from cash-on-delivery payment controller to prepayment */
function prepayment()
{
    let forwardToPaymentLink = document.getElementById('forward-to-payment-link');

    let productId = document.getElementById('product-id-input').value;
    
    forwardToPaymentLink.textContent = "";
    forwardToPaymentLink.appendChild(createSpinnerWidget('dark'));

    setTimeout(function() {
        forwardToPaymentLink.textContent = "Płatność";
        forwardToPaymentLink.setAttribute('href', `/purchase/${productId}/payment`);
        document.getElementById('payment-progress-bar').style.width = "0";
    }, 600);
}