let deliveryTypes = document.getElementsByClassName('purchase-summary-product-delivery-type');

Array.from(deliveryTypes).forEach((element) => {
    element.addEventListener('click', () => {
        let currentPrice = element.getAttribute('data-deliveryPrice');

        document.getElementById('item-delivery-price').textContent = currentPrice;

        document.getElementById('delivery-type-id-input').value = element.getAttribute('data-deliveryTypeId');

        /* Adjust foward button text and progress bar */
        if (element.getAttribute('data-paymentType') == "cash-on-delivery") {
            adjustForwardButtonText('Kupuję', '100');
        } else {
            adjustForwardButtonText('Płatność', '0');
        }
    });
})