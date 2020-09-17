function showUserAddressForm()
{
    let wrapper = document.createElement('div');
    wrapper.setAttribute('id', 'change-user-delivery-data-section');

    let h4 = document.createElement('h4');
    h4.setAttribute('class', 'mt-4 mb-3');
    h4.textContent = "Edycja adresu";

    let col6 = document.createElement('div');
    col6.setAttribute('class', 'col-12 col-sm-8 col-md-6');

    let form = document.createElement('form');
    form.setAttribute('name', 'user_address');
    form.setAttribute('id', 'update-user-address-form');

    let nameField = document.createElement('div');
    nameField.setAttribute('class', 'mb-3');

    let nameLabel = document.createElement('label');
    nameLabel.setAttribute('class', 'mb-2 required');
    nameLabel.textContent = "Imię";

    let nameInput = document.createElement('input');
    nameInput.setAttribute('type', 'text');
    nameInput.setAttribute('name', 'user_address[name]');
    nameInput.setAttribute('class', 'form-control');
    nameInput.setAttribute('required', 'required');

    nameField.appendChild(nameLabel);
    nameField.appendChild(nameInput);

    let surnameField = document.createElement('div');
    surnameField.setAttribute('class', 'mb-3');

    let surnameLabel = document.createElement('label');
    surnameLabel.setAttribute('class', 'mb-2 required');
    surnameLabel.textContent = "Nazwisko";

    let surnameInput = document.createElement('input');
    surnameInput.setAttribute('type', 'text');
    surnameInput.setAttribute('name', 'user_address[surname]');
    surnameInput.setAttribute('class', 'form-control');
    surnameInput.setAttribute('required', 'required');

    surnameField.appendChild(surnameLabel);
    surnameField.appendChild(surnameInput);

    let addressField = document.createElement('div');
    addressField.setAttribute('class', 'mb-3');

    let addressLabel = document.createElement('label');
    addressLabel.setAttribute('class', 'mb-2 required');
    addressLabel.textContent = "Adres";

    let addressInput = document.createElement('input');
    addressInput.setAttribute('type', 'text');
    addressInput.setAttribute('name', 'user_address[address]');
    addressInput.setAttribute('class', 'form-control');
    addressInput.setAttribute('required', 'required');

    let specification = document.createElement('small');
    specification.textContent = "Podaj nazwę ulicy wraz z numerem domu.";

    addressField.appendChild(addressLabel);
    addressField.appendChild(addressInput);
    addressField.appendChild(specification);

    let zipCodeField = document.createElement('div');
    zipCodeField.setAttribute('class', 'mb-3');

    let zipCodeLabel = document.createElement('label');
    zipCodeLabel.setAttribute('class', 'mb-2 required');
    zipCodeLabel.textContent = "Kod pocztowy";

    let zipCodeInput = document.createElement('input');
    zipCodeInput.setAttribute('type', 'text');
    zipCodeInput.setAttribute('name', 'user_address[zip_code]');
    zipCodeInput.setAttribute('class', 'form-control');
    zipCodeInput.setAttribute('required', 'required');
    zipCodeInput.setAttribute('pattern', '[0-9]{2}-[0-9]{3}');

    zipCodeField.appendChild(zipCodeLabel);
    zipCodeField.appendChild(zipCodeInput);

    let cityField = document.createElement('div');
    cityField.setAttribute('class', 'mb-3');

    let cityLabel = document.createElement('label');
    cityLabel.setAttribute('class', 'mb-2 required');
    cityLabel.textContent = "Miasto";

    let cityInput = document.createElement('input');
    cityInput.setAttribute('type', 'text');
    cityInput.setAttribute('name', 'user_address[city]');
    cityInput.setAttribute('class', 'form-control');
    cityInput.setAttribute('required', 'required');

    cityField.appendChild(cityLabel);
    cityField.appendChild(cityInput);

    let countryField = document.createElement('div');
    countryField.setAttribute('class', 'mb-3');

    let countryLabel = document.createElement('label');
    countryLabel.setAttribute('class', 'mb-2 required');
    countryLabel.textContent = "Państwo";

    let countryInput = document.createElement('input');
    countryInput.setAttribute('type', 'text');
    countryInput.setAttribute('name', 'user_address[country]');
    countryInput.setAttribute('class', 'form-control');
    countryInput.setAttribute('required', 'required');

    countryField.appendChild(countryLabel);
    countryField.appendChild(countryInput);

    let phoneNumberField = document.createElement('div');
    phoneNumberField.setAttribute('class', 'mb-3');

    let phoneNumberLabel = document.createElement('label');
    phoneNumberLabel.setAttribute('class', 'mb-2 required');
    phoneNumberLabel.textContent = "Numer telefonu";

    let phoneNumberInput = document.createElement('input');
    phoneNumberInput.setAttribute('type', 'text');
    phoneNumberInput.setAttribute('name', 'user_address[phone_number]');
    phoneNumberInput.setAttribute('class', 'form-control');
    phoneNumberInput.setAttribute('required', 'required');

    phoneNumberField.appendChild(phoneNumberLabel);
    phoneNumberField.appendChild(phoneNumberInput);

    let saveButton = document.createElement('button');
    saveButton.setAttribute('class', 'btn btn-outline-secondary no-border-radius');
    saveButton.textContent = "Zapisz";

    let cancelButton = document.createElement('button');
    cancelButton.setAttribute('class', 'ml-2 btn bg-inherit link-button');
    cancelButton.setAttribute('id', 'cancel-user-address-update');
    cancelButton.textContent = "Anuluj";

    form.appendChild(nameField);
    form.appendChild(surnameField);
    form.appendChild(addressField);
    form.appendChild(zipCodeField);
    form.appendChild(cityField);
    form.appendChild(countryField);
    form.appendChild(phoneNumberField);
    form.appendChild(saveButton);
    form.appendChild(cancelButton);

    col6.appendChild(form);

    wrapper.appendChild(h4);
    wrapper.appendChild(col6);

    deliveryAddressDiv.appendChild(wrapper);

    attachEventToCancelUserAddressFormButton();
}