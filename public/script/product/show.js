import { InfinitePagination } from "../components/src/Pagination/InfinitePagination.js";

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



new InfinitePagination(document.getElementById('infinite-pagination'));


document.getElementById('comment-form').addEventListener('submit', e => {
    e.preventDefault();
    const data = new FormData(e.currentTarget);
    fetch(e.currentTarget.dataset.endpoint, {
        method: 'POST',
        headers: {
            "Content-type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify(Object.fromEntries(data))
    })
    .then(function(res) {
        if(res.ok) {
            return res.json();
        }
    })
    .then(function(value) {
        console.log(value);
    })
    .catch(function(error) {
        console.error(error);
    })
})