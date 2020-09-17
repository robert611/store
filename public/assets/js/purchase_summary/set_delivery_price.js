let deliveryTypes = document.getElementsByClassName('purchase-summary-product-delivery-type');
var paymentFlag = false;

Array.from(deliveryTypes).forEach((element) => {

    element.addEventListener('click', () => {
        let currentPrice = element.getAttribute('data-deliveryPrice');

        document.getElementById('item-delivery-price').textContent = currentPrice + " zł";

        if (element.getAttribute('data-paymentType') == "cash-on-delivery") {
            cashOnDeliveryPayment(element.getAttribute('data-deliveryTypeId'));

            paymentFlag = true;
        } else if(paymentFlag == true) {
            prepayment();
        }
    });
})