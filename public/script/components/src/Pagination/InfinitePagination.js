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
    /** @type {number} */
    #limit
    /** @type {number} */
    #offset
    /** @type {IntersectionObserver} */
    #observer
    /** @type {boolean} */
    #loading = false;

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
        this.#offset = 0;

        this.#observer = new IntersectionObserver((entries) => {
            for(const entry of entries) {
                if(entry.isIntersecting) {
                    this.#loadMore();
                }
            }
        });

        this.#observer.observe(this.#element);
    }

    async #loadMore() {
        if(this.#loading) {
            return;
        }
        this.#loading = true;
        const url = new URL(this.#endpoint);
        url.searchParams.set('offset', this.#offset);
        url.searchParams.set('limit', this.#limit);
        try {
            const items = await myGetFetch(url);
            for(const item of items) {
                const card = cardCreators[this.#itemname](this.#template, item);
                this.#container.append(card);
            }
            if(items.length < this.#element.dataset.limit) {
                entry.target.remove();
                observer.disconnect();
            }
            this.#offset += items.length;

        } catch(e) {
            this.#observer.disconnect();
            this.#element.remove();
            this.#container.append(createAlert('Le contenu n\'a pas pu être chargé'));
            console.error(e);
        }
        this.#loading = false;
    }
}



