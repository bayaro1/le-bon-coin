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
document.getElementById('comment-form').addEventListener('submit', e => {
    e.preventDefault();
    const form = e.currentTarget;
    const data = new FormData(form);
    fetch(e.currentTarget.dataset.endpoint, {
        method: 'POST',
        body: new FormData(form)
    })
    .then(async function(res) {
        if(res.ok) {
            const comment = await res.json();
            const card = createCorrespondingCard(document.getElementById('comment-card-template'), comment, JSON.parse(form.dataset.correspondance));
            document.getElementById('comment-list').prepend(card);
        }
    })
    .catch(function(error) {
        console.error(error);
    })
})