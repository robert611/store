function hideUserAddressForm()
{
    let addSection = document.getElementById('add-user-delivery-data-section');
    let updateSection = document.getElementById('change-user-delivery-data-section');

    addSection ? addSection.remove() : null;

    updateSection ? updateSection.remove() : null;
}