
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

/**
 * 
 * @param {string} url 
 * @returns 
 */
 export async function myFetch(url, options = null) {
    if(options === null) {
        options = {
            method: 'GET',
            headers: {
                "Accept": "application/json"
            }
        };
    }
    const res = await fetch(url, options);
    if(res.ok) {
        return res.json();
    }
    throw new Error('erreur de serveur');
}