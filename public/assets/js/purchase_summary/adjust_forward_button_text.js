/* Change purchase summary forward link to cash-on-delivery payment controller or to prepayment controller */
function adjustForwardButtonText(buttonText, progressBarWidth)
{
    let formButton = document.getElementById('purchase-form-button');
    
    formButton.textContent = "";
    formButton.appendChild(createSpinnerWidget('dark'));

    setTimeout(function() {
        formButton.textContent = buttonText;
        formButton.disabled = false;
        document.getElementById('payment-progress-bar').style.width = progressBarWidth + "%";
    }, 600);
}