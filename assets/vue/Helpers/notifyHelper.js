import * as Toastr from 'toastr/toastr'
Toastr.options = {
    "closeButton": true,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": true,
}

export function notify(title, content, type) {
    if (type === 'success') {
        Toastr.success(content, title)
    }
    if (type === 'error') {
        Toastr.error(content, title)
    }
    if (type === 'info') {
        Toastr.info(content, title)
    }
    if (type === 'warning') {
        Toastr.warning(content, title)
    }
}