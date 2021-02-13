let activeOptions = {'state': null, 'auctionType': null, 'minPrice': null, 'maxPrice': null, 'deliveryTypes': []};

/* "Kategorie" is a default value of category parameter */
let categoryId = getUrlVars()['category'] !== 'Kategorie' && getUrlVars()['category'] !== null ? '?category=' + getUrlVars()['category'] : '';
let productName = getUrlVars()['product'] ? (categoryId ? '&' : '?' ) + 'name=' + getUrlVars()['product'] : '';
let quantity = (categoryId || productName ? '&' : '?' ) + 'quantity[gt]=0'
let owner = getUrlVars()['owner'] ? '&owner=' + getUrlVars()['owner'] : '';

const productsPerPage = 12;

fetch(`/api/products${categoryId}${productName}${quantity}${owner}`)
    .then((response) => {
        return response.json();
    })
    .then(json => {
        return json['hydra:member'];
    })
    .then((products) => {
        document.getElementById('loading-filters-spinner').classList.add('hidden');
        document.getElementById('div-with-filters').classList.remove('hidden');
        products.length > 0 ? activateFilters(products) : null;
    });   

function activateFilters(products)
{
    let stateOptions = [document.getElementById('state_new'), document.getElementById('state_used'),  document.getElementById('state_very_good')];
    let auctionOptions = document.getElementsByClassName('product-auction-type');
    let priceOptions = [document.getElementById('prize-bracket-one'), document.getElementById('prize-bracket-two'), document.getElementById('prize-bracket-three'), document.getElementById('prize-bracket-four')];

    let deliveryOptions = document.getElementsByClassName('delivery-type-checkbox');

    let minimumPrice = document.getElementById('minimum-price');
    let maximumPrice = document.getElementById('maximum-price');

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

                clearPricesInputtedByUser(minimumPrice, maximumPrice);
            } else {
                activeOptions.minPrice = false;
                activeOptions.maxPrice = false;
            }

            filterProducts(products);
        });
    });

    [minimumPrice, maximumPrice].forEach(() => {
        addEventListener('keyup', () => {
            activeOptions.maxPrice = maximumPrice.value;
            activeOptions.minPrice = minimumPrice.value;

            uncheckPriceBrackets(priceOptions);
            filterProducts(products);
        });
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

function clearPricesInputtedByUser(minimumPriceInput, maximumPriceInput)
{
    minimumPriceInput.value = "";
    maximumPriceInput.value = "";
}

function uncheckPriceBrackets(priceOptions)
{
    priceOptions.forEach((option) => {
        if(option.getAttribute('data-checked') == 'true') {
            option.checked = false;
            option.setAttribute('data-checked', 'false');
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
    if (products.length == 0) {
        removeCardClassFromProductsContainer();
        changeProductsContainerContent("<h4>Żadne oferty nie pasują do podanych kryteriów</h4>");
    } else {
        addCardClassToProductsContainer();

        if(products.length > productsPerPage) {
            let pageProducts = getPageProducts(products, productsPerPage, 1);
            let pages = roundUp(products.length / productsPerPage, 0);

            changeProductsContainerContent(null);
            showPageProducts(pageProducts);

            addDivWithPagination(pages, 1);
            addEventListenerToPagination(pages, products);
        } else {
            changeProductsContainerContent(null);
            showPageProducts(products);
        }
    }
}

function changeProductsContainerContent(content)
{
    let productsContainer = document.getElementById('product-container');

    productsContainer.innerHTML = content;
}

function addCardClassToProductsContainer()
{
    let productsContainer = document.getElementById('product-container');

    if (!productsContainer.classList.contains('card')) {
        productsContainer.classList.add("card");
        productsContainer.classList.add("card-body");
    }
}
function removeCardClassFromProductsContainer()
{
    let productsContainer = document.getElementById('product-container');

    if (productsContainer.classList.contains('card')) {
        productsContainer.classList.remove("card");
        productsContainer.classList.remove("card-body");
    }
}

function showPageProducts(products)
{
    let productsContainer = document.getElementById('product-container');

    for (product in products)
    {
        productsContainer.appendChild(createProductWidget(products[product]));
        productsContainer.appendChild(document.createElement('hr'));
    }
}

function getPageProducts(products, productsOnPage, page)
{
    let pageProducts = [];

    for (let i = 1; i <= productsOnPage; i++)
    {
        let c = i + (page - 1) * productsOnPage;

        if (c <= products.length)
        {
            let b = products[c - 1];
            pageProducts.push(b);
        }
    }

    return pageProducts;  
}

function addEventListenerToPagination(pages, products)
{
    let paginationAnchors = document.getElementsByClassName('show-page-products');

    Array.from(paginationAnchors).forEach((anchor) => {
        anchor.addEventListener('click', () => {
            let currentPage = anchor.getAttribute('data-page');

            let pageProducts = getPageProducts(products, productsPerPage, currentPage);

            changeProductsContainerContent(null);
            showPageProducts(pageProducts);

            window.scrollTo(0, 0);
            
            addDivWithPagination(pages, currentPage);

            addEventListenerToPagination(pages, products);
        });
    });
}

/**
 * @param num The number to round
 * @param precision The number of decimal places to preserve
 */
function roundUp(num, precision) {
    precision = Math.pow(10, precision)
    return Math.ceil(num * precision) / precision
}

function getUrlVars() {
    let vars = {};
    
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });

    return vars;
}
