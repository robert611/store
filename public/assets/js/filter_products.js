let activeOptions = {'state': null, 'auctionType': null, 'minPrice': null, 'maxPrice': null, 'deliveryTypes': []};
let currentProducts = [];

let stateOptions = [document.getElementById('state_new'), document.getElementById('state_used'),  document.getElementById('state_very_good')];
let auctionOptions = document.getElementsByClassName('product-auction-type');
let priceOptions = [document.getElementById('prize-bracket-one'), document.getElementById('prize-bracket-two'), document.getElementById('prize-bracket-three'), document.getElementById('prize-bracket-four')];

let minimumPrice = document.getElementById('minimum-price');
let maximumPrice = document.getElementById('maximum-price');

let deliveryOptions = document.getElementsByClassName('delivery-type-checkbox');

/* "Kategorie" is a default value of category parameter */
let categoryId = getUrlVars()['category'] !== 'Kategorie' && getUrlVars()['category'] !== null ? '?category=' + getUrlVars()['category'] : '';
let productName = getUrlVars()['product'] ? '?name=' + getUrlVars()['product'] : '';

fetch(`/api/products${categoryId}${productName}`)
    .then((response) => {
        return response.json();
    })
    .then(json => {
        return json['hydra:member'];
    })
    .then((products) => {
        console.log(products);
        products.length > 0 ? activateFilters(products) : null;
    });   

function activateFilters(products)
{
    stateOptions.forEach((option) => {
        option.addEventListener('click', () => {  

            /* return true if option was already checked, and now is unchecked */
            let flag = toogleOptionCheckedProperty(option, stateOptions);

            if (flag == false) {
                activeOptions.state = option.getAttribute('data-state');
            } else {
                activeOptions.state = false;
            }

            filterProducts(products);
        })
    });
    
    Array.from(auctionOptions).forEach((option) => {
        option.addEventListener('click', () => { 
            
            /* return true if option was already checked, and now is unchecked */
            let flag = toogleOptionCheckedProperty(option, auctionOptions);
            
            if (flag == false) {
                activeOptions.auctionType = option.getAttribute('data-auctionType');
            } else {
                activeOptions.auctionType = false;
            }

            filterProducts(products);
        });
    });

    priceOptions.forEach((option) => {
        option.addEventListener('click', () => {

            /* return true if option was already checked, and now is unchecked */
            let flag = toogleOptionCheckedProperty(option, priceOptions);

            if (flag == false) {
                activeOptions.minPrice = option.getAttribute('data-minPrice');
                activeOptions.maxPrice = option.getAttribute('data-maxPrice');
            } else {
                activeOptions.minPrice = false;
                activeOptions.maxPrice = false;
            }

            filterProducts(products);
        });
    });

    minimumPrice.addEventListener('keyup', () => {
        activeOptions.minPrice = minimumPrice.value;

        filterProducts(products);
    });

    maximumPrice.addEventListener('keyup', () => {
        activeOptions.maxPrice = maximumPrice.value;
        
        filterProducts(products);
    });

    Array.from(deliveryOptions).forEach((option) => {
        option.addEventListener('click', () => {
           
            if (option.checked) {
                activeOptions.deliveryTypes.push(option.value);
            }
            else {
                activeOptions.deliveryTypes.splice(activeOptions.deliveryTypes.indexOf(option.value), 1);
            }

            filterProducts(products);
        });
    });
}

function toogleOptionCheckedProperty(option, optionsGroup)
{
    flag = false;

    if(option.getAttribute('data-checked') == 'true') {
        option.checked = false;
        option.setAttribute('data-checked', 'false');

        flag = true;
    } else {
        option.setAttribute('data-checked', 'true');

        setOtherRadiosCheckedPropertyToFalse(optionsGroup, option);
    }

    return flag;
}

function setOtherRadiosCheckedPropertyToFalse(elements, currentElement)
{
    Array.from(elements).forEach((element) => {
        if (element !== currentElement) {
            element.setAttribute('data-checked', 'false');
        }
    });
}

function filterProducts(products)
{
    let filteredProducts = [];
    
    Array.from(products).forEach((product) => {
        let flag = true;

        if (activeOptions.state) {
            if (product.state !== activeOptions.state) flag = false;
        }

        if (activeOptions.auctionType) {
            if (product.auction_type !== activeOptions.auctionType) flag = false;
        }

        if (activeOptions.minPrice) {
            if (product.price < activeOptions.minPrice) flag = false;
        }

        if (activeOptions.maxPrice) {
            if (product.price > activeOptions.maxPrice) flag = false;
        }

        activeOptions.deliveryTypes.forEach((type) => {
            let productTypes = new Array();
            
            product.deliveryTypes.forEach((deliveryType) => {
                productTypes.push(deliveryType.name);
            });

            if (productTypes.indexOf(type) == -1) flag = false;
        });
    
        if (flag == true) filteredProducts.push(product);
    });
    
    renderProducts(filteredProducts);
}

function renderProducts(products)
{
    let productContainer = document.getElementById('product-container');

    if (products.length == 0) {
        productContainer.innerHTML = "<h4>Żadne oferty nie pasują do podanych kryteriów</h4>";
        productContainer.classList.remove("card");
        productContainer.classList.remove("card-body");
    } else {
        productContainer.textContent = "";
        productContainer.classList.add("card");
        productContainer.classList.add("card-body");
    } 

    for (product in products)
    {
        productContainer.appendChild(createProductWidget(products[product]));
        productContainer.appendChild(document.createElement('hr'));
    }
}

function createProductWidget(product)
{
    let container = document.createElement('div');
    container.setAttribute('class', 'row mb-2');

    let leftSide = document.createElement('div');
    leftSide.setAttribute('class', 'col-4 col-sm-4 col-md-3 col-xl-2 text-center mb-2');

    let img = document.createElement('img');
    img.setAttribute('class', 'img-fluid');
    
    img.setAttribute('src', `/uploads/pictures/${product.productPictures[0] ? product.productPictures[0].name : ''}`);

    leftSide.appendChild(img);

    let rightSide = document.createElement('div');
    rightSide.setAttribute('class', 'col-8 col-sm-6 col-md-9 col-xl-10 mb-2');

    let paragraph = document.createElement('p');

    let anchor = document.createElement('a');
    anchor.setAttribute('href', `/product/${product.id}`);
    anchor.setAttribute('class', 'no-anchor-styles');
    anchor.textContent = product.name;

    let breakLine = document.createElement('br');

    let firstProperty = document.createElement('small');
    let secondProperty = document.createElement('small');
    let thirdProperty = document.createElement('small');

    firstProperty.textContent = 'Stan: ' + product.state;

    paragraph.appendChild(anchor);
    paragraph.appendChild(breakLine);
    paragraph.appendChild(firstProperty);

    if (typeof product.productSpecificProperties[0] !== 'undefined') {
        secondProperty.textContent = product.productSpecificProperties[0].property + ': ' + product.productSpecificProperties[0].value;
        secondProperty.setAttribute('class', 'ml-3');
        paragraph.appendChild(secondProperty);
    }

    if (typeof product.productBasicProperties[0] !== 'undefined') {
        thirdProperty.textContent = product.productBasicProperties[0].property + ': ' + product.productBasicProperties[0].value;
        thirdProperty.setAttribute('class', 'ml-3');
        paragraph.appendChild(thirdProperty);
    }

    let priceHeadline = document.createElement('h4');
    priceHeadline.textContent = product.price + " zł";

    rightSide.appendChild(paragraph);
    rightSide.appendChild(priceHeadline);

    container.appendChild(leftSide);
    container.appendChild(rightSide);

    return container;
}

function getUrlVars() {
    let vars = {};
    
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });

    return vars;
}
