let deliveryTypes = document.getElementsByClassName('purchase-summary-product-delivery-type');
let itemsDeliveryPriceSpan = document.getElementById('item-delivery-price');
let checkedDeliveries = [];
let productsAmount = getBasketProductsAmount();

Array.from(deliveryTypes).forEach((element) => {
    element.addEventListener('click', () => {
        if(element.getAttribute('data-checked') == 'true') {
            element.checked = false;
            element.setAttribute('data-checked', 'false');

            /* Delete unchecked element from checkedDeliveries*/
            checkedDeliveries.splice(checkedDeliveries.indexOf(element), 1);
        } else if(checkIfThereIsAlreadyCheckedOptionWithTheSameNameAttribute(element) !== false) {
            checkedDeliveries = checkedDeliveries.filter((delivery) => {
                return delivery.getAttribute('name') !== element.getAttribute('name');
            });

            setGroupOptionsCheckedPropertyToFalse(document.getElementsByName(element.getAttribute('name')));

            element.setAttribute('data-checked', 'true');
            checkedDeliveries.push(element);
        } else {
            element.setAttribute('data-checked', 'true');
            checkedDeliveries.push(element);
        }

        itemsDeliveryPriceSpan.textContent = sumDeliveriesPrice();

        let path = `purchase/basket/buy`;

        /* If it is smaller than checkedDeliveries.length, then not all products have chosen delivery method */
        if (productsAmount == checkedDeliveries.length)
        {
            /* Adjust foward button text and progress bar */
            if (getPaymentMethod() == "cash-on-delivery") {
                adjustForwardButtonText(path, 'Kupuję', '100');
            } else {
                adjustForwardButtonText(path, 'Płatność', '0');
            }
        } else {
            disableForwardButton();
        }
    });
})

function getPaymentMethod()
{
    let flag = 'cash-on-delivery';

    /* If at least one of the products has checked delivery with prepayment then you can't buy it immediately */
    checkedDeliveries.forEach((delivery) => {
        if (flag != delivery.getAttribute('data-paymentType')) flag = "prepayment";
    });

    return flag;
}

function getBasketProductsAmount()
{
    let productsOptionsNames = [];

    Array.from(deliveryTypes).forEach((element) => {
        let elementNameAttribute = element.getAttribute('name');

        if (productsOptionsNames.indexOf(elementNameAttribute) == -1)
            productsOptionsNames.push(elementNameAttribute);
    });

    return productsOptionsNames.length;
}

function checkIfThereIsAlreadyCheckedOptionWithTheSameNameAttribute(element)
{
    let matchedOptions = checkedDeliveries.filter((delivery) => {
       return delivery.getAttribute('name') == element.getAttribute('name');
    });

    /* If it is 0 then it's equal to false, true otherwise */
    return matchedOptions.length;
}

function setGroupOptionsCheckedPropertyToFalse(elements)
{
    Array.from(elements).forEach((element) => {
        element.setAttribute('data-checked', 'false');
    });
}

function sumDeliveriesPrice()
{
    let sum = 0;

    checkedDeliveries.map((delivery) => {
        sum += parseFloat(delivery.getAttribute('data-deliveryPrice'));
    });

    return sum.toFixed(2);
}