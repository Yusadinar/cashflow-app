// assets/js/script.js — global utilities

/* ────────────────────────────────────────
   Sidebar toggle (mobile)
──────────────────────────────────────── */
function toggleSidebar() {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebar-overlay');
    const isOpen   = !sidebar.classList.contains('-translate-x-full');

    if (isOpen) {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    } else {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }
}

/* ────────────────────────────────────────
   Modal helpers
──────────────────────────────────────── */
function openModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('hidden');
    // Focus first input
    const first = el.querySelector('input:not([type=hidden]), select, textarea');
    if (first) setTimeout(() => first.focus(), 80);
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
}

// Close modal on backdrop click
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.add('hidden');
    }
});

// Close modal on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay:not(.hidden)').forEach(m => {
            m.classList.add('hidden');
        });
    }
});

/* ────────────────────────────────────────
   Flash message auto-dismiss
──────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    const flashes = document.querySelectorAll('[data-flash]');
    flashes.forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s ease';
            el.style.opacity    = '0';
            setTimeout(() => el.remove(), 400);
        }, 3500);
    });

    // Amount input formatter: auto-format display in a companion span (optional)
    const amountInputs = document.querySelectorAll('input[name="amount"]');
    amountInputs.forEach(input => {
        input.addEventListener('blur', () => {
            const val = parseFloat(input.value);
            if (!isNaN(val) && val > 0) {
                input.title = 'Rp ' + val.toLocaleString('id-ID');
            }
        });
    });
});

/* ────────────────────────────────────────
   Utility: format number as Rupiah
──────────────────────────────────────── */
function formatRupiah(amount) {
    return 'Rp ' + parseFloat(amount).toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    });
}
