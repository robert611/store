function addDivWithPagination(pages, currentPage)
{
    let productsContainer = document.getElementById('product-container');

    currentPage = parseInt(currentPage);

    const pagination = document.createElement('div');
    pagination.setAttribute('class', 'text-center mt-8 mb-8');
    pagination.setAttribute('id', 'pagination-div');

    const ul = document.createElement('ul');
    ul.setAttribute('class', 'm-pagination');

    const leftArrowLi = document.createElement('li');
    leftArrowLi.setAttribute('class', 'waves-effect');

    const leftArrowA = document.createElement('a');

    const leftArrowLiIcon = document.createElement('i');
    leftArrowLiIcon.setAttribute('class', 'material-icons icon-align');
    leftArrowLiIcon.textContent = "chevron_left";

    leftArrowA.appendChild(leftArrowLiIcon);
    leftArrowLi.appendChild(leftArrowA);

    if (currentPage > 1) {
        leftArrowLi.classList.add('show-page-products');
        leftArrowLi.setAttribute('data-page', currentPage - 1);
    }

    const rightArrowLi = document.createElement('li');
    rightArrowLi.setAttribute('class', 'waves-effect');

    const rightArrowA = document.createElement('a');

    const rightArrowLiIcon = document.createElement('i');
    rightArrowLiIcon.setAttribute('class', 'material-icons icon-align');
    rightArrowLiIcon.textContent = "chevron_right";

    rightArrowA.appendChild(rightArrowLiIcon);
    rightArrowLi.appendChild(rightArrowA);

    if (currentPage < pages) { 
        rightArrowLi.classList.add('show-page-products');
        rightArrowLi.setAttribute('data-page', currentPage + 1);
    }
    
    ul.appendChild(leftArrowLi);

    for (let i = 1; i <= pages; i++) {
        let li = document.createElement('li');
        let a = document.createElement('a');
        
        li.setAttribute('class', 'waves-effect show-page-products');
        li.setAttribute('data-page', i);
        
        a.textContent = i;

        if (i == currentPage) {
            li.classList.add('active');
        }

        li.appendChild(a);
        ul.appendChild(li);
    }

    ul.appendChild(rightArrowLi);

    pagination.appendChild(ul);

    productsContainer.appendChild(pagination);
}