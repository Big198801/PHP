const customAlert = document.getElementById('customAlert');

function showAlert() {
    customAlert.style.display = 'block';
}

function hideAlert() {
    customAlert.style.display = 'none';
}

function showEdit(formNumber) {
    let formId = 'form' + formNumber;
    document.getElementById(formId).style.display = 'block';
}

function hideEdit(formNumber) {
    let formId = 'form' + formNumber;
    document.getElementById(formId).style.display = 'none';
}