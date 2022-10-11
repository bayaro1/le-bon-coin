// document.getElementById('heart')
//         .addEventListener('click', function(e) {

//         })


        
//         path('cart_add', {'id': product.id})



const elts = document.querySelectorAll('.heart');
for(let elt of elts) {
    elt.addEventListener('click', function(e) {
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
                    console.log('change orange par grey');
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
    })
}