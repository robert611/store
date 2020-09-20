let deliveryTypes = document.getElementsByClassName('purchase-summary-product-delivery-type');

Array.from(deliveryTypes).forEach((element) => {
    element.addEventListener('click', () => {
        let currentPrice = element.getAttribute('data-deliveryPrice');

        document.getElementById('item-delivery-price').textContent = currentPrice + " zł";

        let productId = document.getElementById('product-id-input').value;

        /* If user choose to use cash-on delivery type of delivery, then there is no need to go to payment controller */
        if (element.getAttribute('data-paymentType') == "cash-on-delivery") {
            let path = `/purchase/${productId}/${element.getAttribute('data-deliveryTypeId')}/buy`;
            setPurchasePaymentPath(path, 'Kupuję', '100');
        } else {
            let path = `/purchase/${productId}/payment`;
            setPurchasePaymentPath(path, 'Płatność', '0');
        }
    });
})