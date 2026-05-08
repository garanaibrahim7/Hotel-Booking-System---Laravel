
window.openConfirmModal = function (url, message, buttonText = 'Confirm', method = 'PATCH') {
    const modalBody = document.getElementById('confirmModalBody');
    if (modalBody) {
        modalBody.innerHTML = message; // innerHTML use kiya taaki <strong> tags kaam karein
    }

    let form = document.getElementById('confirmActionForm');
    if (form) {
        form.setAttribute('action', url);
        let methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.value = method;
    }

    let modalElement = document.getElementById('confirmActionModal');
    let myModal = new bootstrap.Modal(modalElement);
    myModal.show();
}

window.onload = function () {
    if (window.$) {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "Search for a city...",
            allowClear: true,
            width: '100%'
        });
    } else {
        console.error("Bhai, $ abhi bhi nahi mila!");
    }
};



