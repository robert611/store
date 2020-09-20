let deliveryTypes = document.getElementsByClassName('purchase-summary-product-delivery-type');

Array.from(deliveryTypes).forEach((element) => {
    element.addEventListener('click', () => {
        let currentPrice = element.getAttribute('data-deliveryPrice');

        document.getElementById('item-delivery-price').textContent = currentPrice + " zł";

        let productId = document.getElementById('product-id-input').value;

        let path = `/purchase/${productId}/${element.getAttribute('data-deliveryTypeId')}/buy`;

        /* Adjust foward button text and progress bar */
        if (element.getAttribute('data-paymentType') == "cash-on-delivery") {
            adjustForwardButtonText(path, 'Kupuję', '100');
        } else {
            adjustForwardButtonText(path, 'Płatność', '0');
        }
    });
})