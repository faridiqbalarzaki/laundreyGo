new_code = '''// 5. GLOBAL TOAST NOTIFICATION API (createui Showcase Style)
window.showToast = function(message, type = 'success', title = null, duration = 4200) {
    let container = document.getElementById('flashToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'flashToastContainer';
        container.className = 'flash-toast-container';
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('role', 'region');
        container.setAttribute('aria-label', 'Notifikasi Sistem');
        document.body.appendChild(container);
    }

    const typeMeta = {
        success: {
            title: 'Berhasil',
            icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>'
        },
        error: {
            title: 'Terjadi Kesalahan',
            icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
        },
        warning: {
            title: 'Perhatian',
            icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
        },
        info: {
            title: 'Informasi',
            icon: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
        }
    };

    const meta = typeMeta[type] || typeMeta.info;
    const finalTitle = title || meta.title;

    const toast = document.createElement('div');
    toast.className = `flash-toast-item flash-toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.dataset.autoDismiss = duration;
    toast.style.setProperty('--toast-duration', duration + 'ms');

    toast.innerHTML = `
        <div class="flash-toast-main">
            <div class="flash-toast-icon">${meta.icon}</div>
            <div class="flash-toast-body">
                <div class="flash-toast-header">
                    <span class="flash-toast-title">${escapeHtml(finalTitle)}</span>
                </div>
                <div class="flash-toast-message">${escapeHtml(message)}</div>
            </div>
            <button type="button" class="flash-toast-close" title="Tutup Notifikasi" aria-label="Tutup Notifikasi">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="flash-toast-progress"></div>
    `;

    container.appendChild(toast);
    if (window.__attachToastBehavior) {
        window.__attachToastBehavior(toast);
    }
    return toast;
};

// 6. AQUA CONFIRMATION MODAL SYSTEM (Foto 1 Style: Samuel Adekoya)
window.AquaModal = (function() {
    let activeModal = null;

    function close() {
        if (!activeModal) return;
        const modal = activeModal;
        activeModal = null;
        const card = modal.querySelector('.aqua-modal-card');
        if (card) card.classList.add('is-leaving');
        modal.classList.add('is-leaving');
        setTimeout(() => {
            if (modal.parentNode) modal.parentNode.removeChild(modal);
        }, 180);
    }

    function confirm(options) {
        close();

        const title = options.title || 'Konfirmasi Tindakan';
        const message = options.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        const badge = options.badge || '';
        const confirmText = options.confirmText || 'Ya, Lanjutkan';
        const cancelText = options.cancelText || 'Batal';
        const type = options.type || 'primary';
        const onConfirm = typeof options.onConfirm === 'function' ? options.onConfirm : () => {};
        const onCancel = typeof options.onCancel === 'function' ? options.onCancel : () => {};

        let iconSvg = '';
        if (options.icon) {
            iconSvg = options.icon;
        } else if (type === 'danger') {
            iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>`;
        } else if (type === 'success') {
            iconSvg = `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;
        } else if (type === 'warning') {
            if (/keluar|logout/i.test(title)) {
                iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>`;
            } else {
                iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`;
            }
        } else {
            iconSvg = `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`;
        }

        const backdrop = document.createElement('div');
        backdrop.className = `aqua-modal-backdrop aqua-modal-${type}`;
        backdrop.setAttribute('role', 'dialog');
        backdrop.setAttribute('aria-modal', 'true');
        backdrop.setAttribute('aria-labelledby', 'aquaModalTitle');

        backdrop.innerHTML = `
            <div class="aqua-modal-card" onclick="event.stopPropagation()">
                <div class="aqua-modal-icon-wrap">
                    ${iconSvg}
                </div>
                <h3 id="aquaModalTitle" class="aqua-modal-title">${escapeHtml(title)}</h3>
                ${badge ? `<div class="aqua-modal-badge">${badge}</div>` : ''}
                <div class="aqua-modal-desc">${message}</div>
                <div class="aqua-modal-actions">
                    <button type="button" class="aqua-modal-btn aqua-modal-btn-cancel">${escapeHtml(cancelText)}</button>
                    <button type="button" class="aqua-modal-btn aqua-modal-btn-confirm btn-${type}">${escapeHtml(confirmText)}</button>
                </div>
            </div>
        `;

        backdrop.addEventListener('click', () => {
            close();
            onCancel();
        });

        const cancelBtn = backdrop.querySelector('.aqua-modal-btn-cancel');
        const confirmBtn = backdrop.querySelector('.aqua-modal-btn-confirm');

        cancelBtn.addEventListener('click', () => {
            close();
            onCancel();
        });

        confirmBtn.addEventListener('click', () => {
            confirmBtn.style.opacity = '0.85';
            confirmBtn.disabled = true;
            onConfirm();
            close();
        });

        document.body.appendChild(backdrop);
        activeModal = backdrop;
        confirmBtn.focus();
    }'''

file_path = r'd:\laragon\www\Laundrey\assets\js\app.js'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

start_marker = '// 5. GLOBAL TOAST NOTIFICATION API'
end_marker = '    document.addEventListener(\'keydown\', function(e) {'

start_pos = content.find(start_marker)
end_pos = content.find(end_marker)

if start_pos == -1 or end_pos == -1:
    print('ERROR: markers not found!', start_pos, end_pos)
    exit(1)

# Format replacement with the same line ending style as existing file
is_crlf = '\r\n' in content
replacement = new_code.replace('\r\n', '\n')
if is_crlf:
    replacement = replacement.replace('\n', '\r\n') + '\r\n\r\n'
else:
    replacement = replacement + '\n\n'

new_content = content[:start_pos] + replacement + content[end_pos:]

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(new_content)

print('SUCCESSFULLY UPDATED app.js!')
