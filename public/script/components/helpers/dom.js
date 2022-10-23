/**
 * 
 * @param {string} content
 * @returns {HTMLElement} 
 */
export function createAlert(content) {
    const alert = document.createElement('div');
    alert.classList.add('alert', 'alert-danger');
    alert.style.width = '700px';
    alert.innerText = content;
    const button = document.createElement('button');
    button.innerText = 'ok';
    alert.append(button);
    button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.currentTarget.parentElement.remove();
    })
    return alert;
}