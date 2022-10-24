/**
 * 
 * @param {HTMLTemplateElement} template 
 * @param {Object} item 
 * @param {Object} correspondance 
 * @returns {HTMLElement}
 */
export function createCorrespondingCard(template, item, correspondance)
{
    const card = template.content.cloneNode(true).firstElementChild;
    for(const [itemKey, eltClass] of Object.entries(correspondance)) {
        card.querySelector(eltClass).innerText = item[itemKey];
    }
    return card;
}