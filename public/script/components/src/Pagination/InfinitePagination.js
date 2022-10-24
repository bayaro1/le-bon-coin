import { myGetFetch } from "../../helpers/api.js";
import { createAlert } from "../../helpers/dom.js";
import { cardCreators } from "../../templating/cardCreators.js";

export class InfinitePagination {

    /** @type {HTMLElement} */
    #element
    /** @type {string} */
    #endpoint
    /** @type {HTMLElement} */
    #container
    /** @type {HTMLTemplateElement} */
    #template
    /** @type {string} */
    #itemname
    /** @type {object} */
    #correspondance
    /** @type {number} */
    #limit
    /** @type {number} */
    #offset
    /** @type {string} */
    sort
    /** @type {IntersectionObserver} */
    #observer
    /** @type {boolean} */
    #loading = false;
    /** @type {boolean} */
    #disconnected = false;
    

    /**
     * 
     * @param {HTMLElement} element 
     */
    constructor(element) {

        this.#element = element;
        this.#endpoint = element.dataset.endpoint;
        this.#container = document.querySelector(element.dataset.container);
        this.#template = document.querySelector(element.dataset.template);
        this.#itemname = element.dataset.itemname;
        this.#limit = element.dataset.limit;
        this.sort = element.dataset.sort;
        this.#offset = 0;
        if(element.dataset.correspondance) {
            this.#correspondance = JSON.parse(element.dataset.correspondance);
        }
        this.#observer = new IntersectionObserver((entries) => {
            for(const entry of entries) {
                if(entry.isIntersecting) {
                    this.#loadMore();
                }
            }
        });

        this.#observer.observe(this.#element);
    }

    empty() {
        this.#container.innerText = '';
        this.#offset = 0;
        if(this.#disconnected === true) {
            this.#observer.observe(this.#element);
            this.#element.style.display = '';
        }
    }

    async #loadMore() {
        if(this.#loading) {
            return;
        }
        this.#loading = true;
        const url = new URL(this.#endpoint);
        url.searchParams.set('offset', this.#offset);
        url.searchParams.set('limit', this.#limit);
        url.searchParams.set('sort', this.sort);
        try {
            const items = await myGetFetch(url);
            if(items.length === 0) {
                this.#element.style.display = 'none';
                this.#observer.disconnect();
                this.#disconnected = true;
            }
            for(const item of items) {
                if(this.#itemname) {
                    const card = cardCreators[this.#itemname](this.#template, item);
                    this.#container.append(card);
                } else if(this.#correspondance) {
                    this.#container.append(this.#createCorrespondingCard(item, this.#correspondance));
                } else {
                    throw new Error('InfinitePagination ne sait pas comment placer les données reçues dans le template : Veuillez renseigner soit une balise data-itemname soit data-corresponding dans l\'élément infinite-pagination en html')
                }
            }
            this.#offset += items.length;

        } catch(e) {
            this.#observer.disconnect();
            this.#disconnected = true;
            this.#element.style.display = 'none';
            this.#container.append(createAlert('Le contenu n\'a pas pu être chargé'));
            console.error(e);
        }
        this.#loading = false;
    }

    /**
     * 
     * @param {Object} item 
     * @param {Object} correspondance 
     * @returns {HTMLElement}
     */
    #createCorrespondingCard(item, correspondance)
    {
        const card = this.#template.content.cloneNode(true).firstElementChild;
        for(const [itemKey, eltClass] of Object.entries(correspondance)) {
            card.querySelector(eltClass).innerText = item[itemKey];
        }
        return card;
    }
}



