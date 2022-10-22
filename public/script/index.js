// document.getElementById('heart')
//         .addEventListener('click', function(e) {

//         })


        
//         path('cart_add', {'id': product.id})




document.querySelectorAll('.heart').forEach(function(elt) {
    elt.addEventListener('click', function(e) {
        elt = e.currentTarget;
        const id = elt.getAttribute('value');
        e.preventDefault();
        e.stopPropagation();
        fetch('/favoris/ajout/'+id, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(function(res) {
            if(res.ok) {
                let color = elt.style.color;
                if(color === 'orange') {
                    elt.classList.replace('bi-heart-fill', 'bi-heart');
                    elt.style.color = 'grey';
                }
                else {
                    elt.classList.replace('bi-heart', 'bi-heart-fill');
                    elt.style.color = 'orange';
                }
            }
        })
        .catch(function(error) {
            //on fait rien
        })
    });
});