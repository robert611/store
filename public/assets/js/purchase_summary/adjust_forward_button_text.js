/* Change purchase summary forward link to cash-on-delivery payment controller or to prepayment controller */
function adjustForwardButtonText(path, buttonText, progressBarWidth)
{
    let forwardToPaymentLink = document.getElementById('forward-to-payment-link');
    let linkButton = forwardToPaymentLink.firstChild;
    
    linkButton.textContent = "";
    linkButton.appendChild(createSpinnerWidget('dark'));

    setTimeout(function() {
        linkButton.textContent = buttonText;
        linkButton.disabled = false;
        forwardToPaymentLink.setAttribute('href', path);
        document.getElementById('payment-progress-bar').style.width = progressBarWidth + "%";
    }, 600);
}