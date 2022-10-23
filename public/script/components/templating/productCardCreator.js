/**
 * @typedef Product
 * @property {string} title
 * @property {string} price
 * @property {string} categoryName
 * @property {string} city
 * @property {string} postalCode
 * @property {string} showPath
 * @property {string} firstPicturePath
 * @property {string} cartAddPath
 * @property {boolean} inCart
 */


/**
 * 
 * @param {HTMLTemplateElement} template 
 * @param {Product} product 
 * @returns 
 */
export const productCardCreator = function(template, product) {

    const card = template.content.cloneNode(true).firstElementChild;

    card.querySelector('.product-title').innerText = product.title;
    card.querySelector('.product-price').innerText = product.price;
    card.querySelector('.product-categoryName').innerText = product.categoryName;
    card.querySelector('.product-city').innerText = product.city;
    card.querySelector('.product-postalCode').innerText = product.postalCode;

    card.querySelector('.product-showPath').setAttribute('href', product.showPath);
    card.querySelector('.product-firstPicture').setAttribute('src', product.firstPicturePath);

    const cartIndicator = card.querySelector('.product-cartIndicator');
    cartIndicator.dataset.cartaddpath = product.cartAddPath;
    if(product.inCart) {
        cartIndicator.classList.replace('bi-heart', 'bi-heart-fill');
    }
    configureCartListener(cartIndicator);
    
    return card;
}


/**
 * 
 * @param {HTMLElement} cartIndicator 
 */
function configureCartListener(cartIndicator) {
    cartIndicator.addEventListener('click', function(e) {
        
        e.preventDefault();
        e.stopPropagation();
        toggle(cartIndicator);
        
        fetch(cartIndicator.dataset.cartaddpath, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .catch(function(error) {
            toggle(cartIndicator);
            console.log(error)
        })
    });
}

/**
 * 
 * @param {HTMLElement} cartIndicator 
 */
function toggle(cartIndicator) {
    if(cartIndicator.classList.contains('bi-heart-fill')) {
        cartIndicator.classList.replace('bi-heart-fill', 'bi-heart');
    }
    else {
        cartIndicator.classList.replace('bi-heart', 'bi-heart-fill');
    }
}

