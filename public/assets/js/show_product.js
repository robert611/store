/* Assign quantity of buying product to forms in buy now and add to basket anchors */
function assignItemsQuantityToForms()
{
    let itemsQuantityInput = document.getElementById('buying-items-quantity-input');

    let addToBasketFormInput = document.getElementById('add-to-basket-form');

    let buyNowFormInput = document.getElementById('buy-now-form');

    /* If given product was posted as announcment then itemsQuantityInput will be null */
    itemsQuantityInput ? itemsQuantityInput.addEventListener('change', (e) => {
        let itemsQuantity = itemsQuantityInput.value;

        addToBasketFormInput.value = itemsQuantity;
        buyNowFormInput.value = itemsQuantity;
    }): null;
}

assignItemsQuantityToForms();
