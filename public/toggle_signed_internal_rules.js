$(document).ready(function() {
    if(document.querySelectorAll('#thereAreUnsigned').length !== 0){
        document.querySelectorAll('#signed')[0].style.display='none';
        document.querySelectorAll('#toggleSignedButton')[0].textContent = ' Εμφάνιση Υπογεγραμμένων';// Εσωτερικών Κανονισμών';
    }else{
        document.querySelectorAll('#signed')[0].style.display='block';
        document.querySelectorAll('#toggleSignedButton')[0].textContent = ' Απόκρυψη Υπογεγραμμένων';
    }
});
function showSigned() {
    var x = document.querySelectorAll('#signed')[0].style.display;
    console.log(x);
    if(x=='none'){
        document.querySelectorAll('#toggleSignedButton')[0].textContent = ' Απόκρυψη Υπογεγραμμένων';// Εσωτερικών Κανονισμών';
        document.querySelectorAll('#signed')[0].style.display='block';
    } else {
        document.querySelectorAll('#toggleSignedButton')[0].textContent = ' Εμφάνιση Υπογεγραμμένων';// Εσωτερικών Κανονισμών';
        document.querySelectorAll('#signed')[0].style.display='none';
    }
}