import { myFetch } from "../components/helpers/api.js";
import { InfinitePagination } from "../components/src/Pagination/InfinitePagination.js";
import { createCorrespondingCard } from "../components/templating/createCorrespondingCard.js";


// sélection des photos
const minies = document.querySelectorAll('.img-mini');
minies.forEach(img => {
    img.addEventListener('click', (e) => {
        for(const mini of minies ) {
            mini.classList.remove('is-selected');
        }   
        document.querySelector('#img-first').setAttribute('src', e.currentTarget.dataset.bigimg);
        e.currentTarget.classList.add('is-selected');
    });
});


// pagination des commentaires
new InfinitePagination(document.getElementById('infinite-pagination'));


// poster un commentaire
document.getElementById('comment-form').addEventListener('submit', e => onCommentSubmission(e))


/**
 * 
 * @param {Event} e 
 */
async function onCommentSubmission(e) {

    e.preventDefault();
    const form = e.currentTarget;
    const data = new FormData(form);

    try {
        const [comment, errors] = await myFetch(form.dataset.endpoint, {
            method: 'POST',
            body: data
        });
        if(errors.length !== 0) {
            viewErrors(form, errors);
            return;
        }
        const card = createCorrespondingCard(
            document.querySelector(form.dataset.template), 
            comment, 
            JSON.parse(form.dataset.correspondance)
        );
        document.querySelector(form.dataset.container)
                .prepend(card);

    } catch(e) {
        console.error(e);
    }
}

/**
 * 
 * @param {HTMLElement} form 
 * @param {Object} errors 
 */
function viewErrors(form, errors) {
    for(const [field, message] of Object.entries(errors)) {
        form.querySelector('#comment-form-'+field).classList.add('form-field-invalid');
        form.querySelector('#comment-form-'+field+'-error').innerText = message + '';
    }
    form.addEventListener('input', e => {
        e.target.classList.remove('form-field-invalid');
        e.target.nextElementSibling.innerText = '';
    }, {once: true});
}