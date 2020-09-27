function disableForwardButton()
{
    let forwardToPaymentLink = document.getElementById('forward-to-payment-link');
    let linkButton = forwardToPaymentLink.firstChild;
    
    linkButton.textContent = "";
    linkButton.appendChild(createSpinnerWidget('dark'));

    setTimeout(function() {
        linkButton.textContent = 'Płatność';
        linkButton.disabled = true;
        forwardToPaymentLink.removeAttribute('href');
        document.getElementById('payment-progress-bar').style.width = "0%";
    }, 600);
}