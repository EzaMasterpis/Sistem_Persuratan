import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;
Alpine.start();

/* ---------- Gaya SweetAlert (kuning-putih, serasi dengan tema) ---------- */

const kelas = {
    popup: 'rounded-2xl font-sans',
    title: 'text-lg font-bold text-ink',
    htmlContainer: 'text-sm text-gray-600',
    actions: 'gap-2',
    confirmButton:
        'rounded-xl bg-brand-400 px-6 py-2.5 text-sm font-semibold text-ink shadow transition hover:bg-brand-500',
    cancelButton:
        'rounded-xl border border-gray-300 bg-white px-6 py-2.5 text-sm font-semibold text-ink transition hover:bg-gray-50',
};

const kelasBahaya =
    'rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-red-700';

/* ---------- Konfirmasi otomatis untuk <form data-konfirmasi="..."> ---------- */

document.addEventListener('submit', (e) => {
    const form = e.target;

    if (!(form instanceof HTMLFormElement) || !form.dataset.konfirmasi) {
        return;
    }

    // Sudah dikonfirmasi: biarkan form terkirim
    if (form.dataset.dikonfirmasi === '1') {
        return;
    }

    e.preventDefault();

    const pengirim = e.submitter;
    const bahaya = form.hasAttribute('data-bahaya');

    Swal.fire({
        title: form.dataset.judul || 'Apakah Anda yakin?',
        text: form.dataset.konfirmasi,
        icon: bahaya ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonText: form.dataset.tombol || 'Ya, lanjutkan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: bahaya,
        buttonsStyling: false,
        customClass: { ...kelas, confirmButton: bahaya ? kelasBahaya : kelas.confirmButton },
    }).then((hasil) => {
        if (!hasil.isConfirmed) {
            return;
        }

        form.dataset.dikonfirmasi = '1';
        pengirim ? form.requestSubmit(pengirim) : form.requestSubmit();
        delete form.dataset.dikonfirmasi;
    });
});

/* ---------- Toast untuk pesan sukses / gagal ---------- */

window.notifikasi = (pesan, ikon = 'success') => {
    Swal.fire({
        toast: true,
        position: 'bottom',
        icon: ikon,
        title: pesan,
        showConfirmButton: false,
        timer: 4500,
        timerProgressBar: true,
        customClass: {
            popup: 'rounded-xl border border-brand-300 font-sans',
            title: 'text-sm font-medium text-ink',
        },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        },
    });
};