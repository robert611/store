const trigger = document.querySelector('#show-product-opinions-icon');
const containerWithOpinions = document.querySelector('#product-opinions-container');

trigger.addEventListener('click', () => {
    if(containerWithOpinions.classList.contains('hidden')) {
        containerWithOpinions.classList.remove('hidden');
    } else {
        containerWithOpinions.classList.add('hidden');
    }
});