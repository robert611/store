const deliveryTypesTrigger = document.querySelector('#show-product-delivery-types');
const containerWithDeliveryTypes = document.querySelector('#product-delivery-types-container');

deliveryTypesTrigger.addEventListener('click', () => {
    if(containerWithDeliveryTypes.classList.contains('hidden')) {
        containerWithDeliveryTypes.classList.remove('hidden');
    } else {
        containerWithDeliveryTypes.classList.add('hidden');
    }
});