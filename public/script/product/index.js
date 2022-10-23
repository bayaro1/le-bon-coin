import { InfinitePagination } from "../components/src/Pagination/InfinitePagination.js";



const pagination = new InfinitePagination(document.getElementById('infinite-pagination'));




document.querySelector('.sort-select').addEventListener('change', e => {
    pagination.sort =  e.currentTarget.value;
    pagination.empty();
})
