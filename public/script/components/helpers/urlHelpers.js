
/**
 * 
 * @param {string} url 
 * @param {object} params 
 * @returns {string}
 */
 export function urlAddQueryParams(url, params) {
    let addArray = [];
    for(const [key, value] of Object.entries(params)) {
        addArray.push(key+'='+value);
    }
    const addString = addArray.join('&');
    return url.includes('?') ? url+'&'+addString : url+'?'+addString;
    
}