
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

