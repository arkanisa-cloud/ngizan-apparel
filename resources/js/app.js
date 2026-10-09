import Alpine from 'alpinejs';
import $ from 'jquery';
import toastr from 'toastr';
import Swal from 'sweetalert2';
import { Chart, registerables } from 'chart.js';
import L from 'leaflet';

Chart.register(...registerables);

window.$ = window.jQuery = $;
window.toastr = toastr;
window.Swal = Swal;
window.Chart = Chart;
window.L = L;
window.Alpine = Alpine;

// Configure global Toastr options
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    showDuration: 200,
    hideDuration: 200,
    timeOut: 4000,
    extendedTimeOut: 1500,
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut'
};

// Global Helper for SweetAlert2 Confirmations
window.confirmAction = function({
    title = 'Konfirmasi Tindakan',
    text = 'Apakah Anda yakin ingin melanjutkan?',
    icon = 'warning',
    confirmText = 'Ya, Lanjutkan',
    cancelText = 'Batal',
    isDanger = false
}) {
    return Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        reverseButtons: true,
        customClass: {
            popup: 'rounded-3xl border border-hairline-soft p-6 shadow-2xl',
            title: 'text-base sm:text-lg font-bold text-ink pt-2',
            htmlContainer: 'text-xs text-mute mt-2',
            confirmButton: isDanger 
                ? 'px-5 py-2.5 bg-sale hover:opacity-90 text-white rounded-full text-xs font-semibold uppercase tracking-wider transition shadow-xs cursor-pointer border-0'
                : 'px-5 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-semibold uppercase tracking-wider transition shadow-xs cursor-pointer border-0',
            cancelButton: 'px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-semibold uppercase tracking-wider transition shadow-2xs cursor-pointer border-0 mr-2'
        },
        buttonsStyling: false
    });
};

// Auto-bind SweetAlert2 to delete and confirmable forms
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;

        const isDelete = form.querySelector('input[name="_method"][value="DELETE"]') || form.classList.contains('confirm-delete');
        const customConfirm = form.getAttribute('data-confirm');

        if (isDelete || customConfirm) {
            if (form.dataset.confirmed === 'true') {
                return; // Let the form proceed
            }

            e.preventDefault();

            const title = form.getAttribute('data-confirm-title') || (isDelete ? 'Hapus Data?' : 'Konfirmasi Tindakan');
            const text = customConfirm || form.getAttribute('data-confirm-text') || (isDelete ? 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.' : 'Apakah Anda yakin ingin melanjutkan?');
            const confirmText = form.getAttribute('data-confirm-btn') || (isDelete ? 'Ya, Hapus' : 'Ya, Lanjutkan');

            window.confirmAction({
                title: title,
                text: text,
                icon: isDelete ? 'warning' : 'question',
                confirmText: confirmText,
                isDanger: Boolean(isDelete)
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            });
        }
    });
});

Alpine.start();
