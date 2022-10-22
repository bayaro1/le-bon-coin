import { urlAddQueryParams } from "../../helpers/urlHelpers.js";
import { cardCreators } from "../../templating/cardCreators.js";

export class InfinitePagination {

    /**
     * 
     * @param {HTMLElement} infinitePaginationElement 
     */
    paginateWhenElementIsVisible(infinitePaginationElement) {
        const observer = this.#createIntersectionObserver();
        observer.observe(infinitePaginationElement);
    }

    /**
     * 
     * @returns {IntersectionObserver}
     */
    #createIntersectionObserver() {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if(entry.isIntersecting) {
                    
                    fetch(urlAddQueryParams(entry.target.dataset.endpoint, {
                        offset: document.getElementById(entry.target.dataset.container).childElementCount,
                        limit: entry.target.dataset.limit
                    }), {
                        method: 'GET',
                        headers: {
                            "Accept": "application/json",
                            "Content-type": "application/json"
                        }
                    })
                    .then(function(res) {
                        if(res.ok) {
                            return res.json();
                        }
                    })
                    .then(function(items) {
                        items.forEach(function(item) {
                            const card = cardCreators[entry.target.dataset.itemname](entry.target.dataset.template, item);
                            document.getElementById(entry.target.dataset.container).append(card);  
                        });
                        if(items.length < entry.target.dataset.limit) {
                            entry.target.remove();
                            observer.disconnect();
                        }
                    })
                    .catch(function(error) {
                        console.error(error);
                    })
                }
            })
        });

        return observer;
    }
}



