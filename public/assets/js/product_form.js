let basicParametersDiv = document.getElementById('basic-parameters-div');
let specificParametersDiv = document.getElementById('specific-parameters-div');
let physicalParametersDiv = document.getElementById('physical-parameters-div');

let addBasicParameterRowButton = document.getElementById('add-basic-parameter-row-button');
let addSpecificParameterRowButton = document.getElementById('add-specific-parameter-row-button');
let addPhysicalParameterRowButton = document.getElementById('add-physical-parameter-row-button');

/* Button, div for parameters inputs rows, type of parameter */
let addParameterButtonsArray = [
    {'button': addBasicParameterRowButton, 'parametersDiv': basicParametersDiv, 'type_of_parameter': 'basic'}, 
    {'button': addSpecificParameterRowButton, 'parametersDiv': specificParametersDiv, 'type_of_parameter': 'specific'},
    {'button': addPhysicalParameterRowButton, 'parametersDiv': physicalParametersDiv, 'type_of_parameter': 'physical'}
];

addParameterButtonsArray.forEach((array) => { 
    array['button'].addEventListener('click', (e) => {
        e.preventDefault();

        array['parametersDiv'].appendChild(createParameterRowWidget(array['type_of_parameter']));

        updateParameterRowDeleteButtons();
    });
});

function updateParameterRowDeleteButtons()
{
    let deleteInputRowButtons = document.getElementsByClassName('delete-property-input-row');

    Array.from(deleteInputRowButtons).forEach((button) => {
        button.addEventListener('click', (e) => {
            e.preventDefault();

            if (e.srcElement.classList.contains("material-icons")) {
                rowToDelete = e.target.parentElement.parentElement.parentElement;
            } else {
                rowToDelete = e.target.parentElement.parentElement;
            }

            /* Create an effect of deleting element, after that change display to none, so element will not leave any space */
            rowToDelete.style.transitionTimingFunction = 'ease-in';
            rowToDelete.style.transition = '0.6s';
            rowToDelete.style.transform = "scale(0.0)";

            setTimeout(function () {
                rowToDelete.style.display = "none";
                rowToDelete.remove();
            }, 610);
        });
    });
}

function createParameterRowWidget(name) 
{
    let row = document.createElement('div');
    row.setAttribute('class', 'row mt-2');

    let propertyInputDiv = document.createElement('div');
    propertyInputDiv.setAttribute('class', 'col-5');

    let valueInputDiv = document.createElement('div');
    valueInputDiv.setAttribute('class', 'col-5');

    let removeButtonDiv = document.createElement('div');
    removeButtonDiv.setAttribute('class', 'col-2');

    let propertyInput = document.createElement('input');
    propertyInput.setAttribute('type', 'text');
    propertyInput.setAttribute('type', 'basic');
    propertyInput.setAttribute('class', 'form-control');
    propertyInput.setAttribute('placeholder', 'nazwa');
    propertyInput.setAttribute('name', `product[${name}_properties][name][]`);

    let valueInput = document.createElement('input');
    valueInput.setAttribute('type', 'text');
    valueInput.setAttribute('class', 'form-control');
    valueInput.setAttribute('placeholder', 'wartość');
    valueInput.setAttribute('name', `product[${name}_properties][value][]`);

    let removeButton = document.createElement('button');
    removeButton.setAttribute('class', 'btn btn-danger delete-property-input-row');
    
    let removeIcon = document.createElement('icon');
    removeIcon.setAttribute('class', 'material-icons icon-align');
    removeIcon.textContent = 'remove';

    removeButton.appendChild(removeIcon);

    propertyInputDiv.appendChild(propertyInput);
    valueInputDiv.appendChild(valueInput);
    removeButtonDiv.appendChild(removeButton);

    row.appendChild(propertyInputDiv);
    row.appendChild(valueInputDiv);
    row.appendChild(removeButtonDiv);

    return row;
} 