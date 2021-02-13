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