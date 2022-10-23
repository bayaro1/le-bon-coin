
/**
 * 
 * @param {string} url 
 * @returns 
 */
export async function myGetFetch(url) {
    const res = await fetch(url, {
        method: 'GET',
        headers: {
            "Accept": "application/json"
        }
    });
    if(res.ok) {
        return res.json();
    }
    throw new Error('erreur de serveur');
}