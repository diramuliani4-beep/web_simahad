@php
    $pesanNotifikasi = [];
    $pesanSudahDitampilkan = [];

    foreach ([
        'success' => session('success'),
        'error' => session('error'),
        'status' => session('status'),
        'info' => session('info'),
        'warning' => session('warning'),
    ] as $tipe => $pesan) {
        if (! filled($pesan) || ! is_scalar($pesan)) {
            continue;
        }

        $pesan = (string) $pesan;

        if (in_array($pesan, $pesanSudahDitampilkan, true)) {
            continue;
        }

        $pesanSudahDitampilkan[] = $pesan;
        $pesanNotifikasi[] = ['tipe' => $tipe, 'pesan' => $pesan];
    }
@endphp

<style>
    .sui-toast-region {
        position: fixed;
        top: calc(var(--header-height, 68px) + 16px);
        right: 20px;
        z-index: 5000;
        display: grid;
        gap: 10px;
        width: min(390px, calc(100vw - 32px));
        pointer-events: none;
    }

    .sui-toast {
        display: grid;
        grid-template-columns: 34px minmax(0, 1fr) 28px;
        align-items: start;
        gap: 11px;
        padding: 14px 14px 14px 15px;
        border: 1px solid var(--line, #ecd9dd);
        border-left: 4px solid var(--gold, #C9A96A);
        border-radius: 14px;
        background: #fff;
        color: var(--text, #292929);
        box-shadow: 0 16px 42px rgba(59, 7, 15, .16);
        pointer-events: auto;
        animation: sui-toast-in .2s ease-out;
    }

    .sui-toast--success { border-left-color: #2f7a4f; }
    .sui-toast--error { border-left-color: #a3162c; }
    .sui-toast--warning { border-left-color: #b7791f; }
    .sui-toast--status,
    .sui-toast--info { border-left-color: var(--maroon, #6B0D1B); }

    .sui-toast__mark {
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--maroon-tint, #F8EDEF);
        color: var(--maroon, #6B0D1B);
        font-size: 16px;
        font-weight: 800;
    }

    .sui-toast--success .sui-toast__mark { background: #eef7f0; color: #22653a; }
    .sui-toast--error .sui-toast__mark { background: #fdeef0; color: #8a1224; }
    .sui-toast--warning .sui-toast__mark { background: #fff7e8; color: #8a5a12; }

    .sui-toast__mark::before { content: "i"; }
    .sui-toast--success .sui-toast__mark::before { content: "✓"; }
    .sui-toast--error .sui-toast__mark::before { content: "!"; }
    .sui-toast--warning .sui-toast__mark::before { content: "!"; }

    .sui-toast__title {
        display: block;
        margin: 0 0 3px;
        color: var(--maroon, #6B0D1B);
        font-size: 13px;
        font-weight: 800;
        line-height: 1.35;
    }

    .sui-toast__message {
        margin: 0;
        color: var(--text-secondary, #4b5563);
        font-size: 12.5px;
        line-height: 1.55;
        overflow-wrap: anywhere;
        white-space: pre-line;
    }

    .sui-toast__close {
        display: grid;
        place-items: center;
        width: 28px;
        height: 28px;
        padding: 0;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #7a7074;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
    }

    .sui-toast__close:hover,
    .sui-toast__close:focus-visible { background: var(--maroon-tint, #F8EDEF); color: var(--maroon, #6B0D1B); }

    .sui-dialog {
        width: min(480px, calc(100vw - 32px));
        max-width: none;
        max-height: calc(100dvh - 32px);
        margin: auto;
        padding: 0;
        overflow: visible;
        border: 0;
        border-radius: 18px;
        background: transparent;
        color: var(--text, #292929);
        box-shadow: 0 24px 80px rgba(30, 3, 8, .28);
    }

    .sui-dialog::backdrop { background: rgba(35, 9, 14, .5); backdrop-filter: blur(3px); }

    .sui-dialog__card {
        max-height: calc(100dvh - 32px);
        overflow-y: auto;
        padding: 24px;
        border: 1px solid var(--line, #ecd9dd);
        border-radius: 18px;
        background: #fff;
    }

    .sui-dialog__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .sui-dialog__icon {
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        width: 46px;
        height: 46px;
        margin-bottom: 14px;
        border-radius: 50%;
        background: var(--maroon-tint, #F8EDEF);
        color: var(--maroon, #6B0D1B);
        font-size: 22px;
        font-weight: 800;
    }

    .sui-dialog__icon::before { content: "?"; }
    .sui-dialog[data-tone="danger"] .sui-dialog__icon { background: #fdeef0; color: #8a1224; }
    .sui-dialog[data-tone="danger"] .sui-dialog__icon::before { content: "!"; }
    .sui-dialog[data-tone="success"] .sui-dialog__icon { background: #eef7f0; color: #22653a; }
    .sui-dialog[data-tone="success"] .sui-dialog__icon::before { content: "✓"; }

    .sui-dialog__title {
        margin: 0;
        color: var(--maroon, #6B0D1B);
        font-size: 18px;
        font-weight: 800;
        line-height: 1.35;
    }

    .sui-dialog__message {
        margin: 0;
        color: var(--text-secondary, #4b5563);
        font-size: 13.5px;
        line-height: 1.65;
        overflow-wrap: anywhere;
        white-space: pre-line;
    }

    .sui-dialog__close {
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        width: 32px;
        height: 32px;
        margin: -6px -6px 0 0;
        padding: 0;
        border: 0;
        border-radius: 9px;
        background: transparent;
        color: #74696d;
        font-size: 23px;
        line-height: 1;
        cursor: pointer;
    }

    .sui-dialog__close:hover,
    .sui-dialog__close:focus-visible { background: var(--maroon-tint, #F8EDEF); color: var(--maroon, #6B0D1B); }

    .sui-dialog__actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 24px;
    }

    .sui-dialog__button {
        min-height: 42px;
        padding: 10px 18px;
        border: 1px solid transparent;
        border-radius: 10px;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: background .16s ease, border-color .16s ease, color .16s ease;
    }

    .sui-dialog__button--secondary { border-color: var(--line, #ecd9dd); background: #fff; color: #5c5155; }
    .sui-dialog__button--secondary:hover { border-color: var(--gold, #C9A96A); background: var(--maroon-tint, #F8EDEF); color: var(--maroon, #6B0D1B); }
    .sui-dialog__button--primary { background: var(--maroon, #6B0D1B); color: #fff; }
    .sui-dialog__button--primary:hover { background: var(--maroon-dark, #52070f); }
    .sui-dialog__button--danger { background: #8f1023; color: #fff; }
    .sui-dialog__button--danger:hover { background: #730b1c; }

    .sui-dialog__field { display: grid; gap: 8px; margin-top: 18px; }
    .sui-dialog__label { color: var(--text, #292929); font-size: 12.5px; font-weight: 700; }
    .sui-dialog__textarea {
        width: 100%;
        min-height: 112px;
        resize: vertical;
        padding: 12px 13px;
        border: 1px solid var(--line, #ecd9dd);
        border-radius: 10px;
        background: #fff;
        color: var(--text, #292929);
        font: inherit;
        font-size: 13px;
        line-height: 1.55;
    }
    .sui-dialog__textarea:focus { border-color: var(--gold, #C9A96A); outline: 3px solid rgba(201, 169, 106, .2); }
    .sui-dialog__error { margin: 0; color: #9f1239; font-size: 12px; font-weight: 600; }
    .sui-dialog__error[hidden] { display: none; }

    body.sui-dialog-open { overflow: hidden; }

    @keyframes sui-toast-in {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 600px) {
        .sui-toast-region { top: calc(var(--header-height, 62px) + 12px); right: 16px; }
        .sui-dialog__card { padding: 20px; }
        .sui-dialog__actions { flex-direction: column-reverse; }
        .sui-dialog__button { width: 100%; }
    }

    @media (prefers-reduced-motion: reduce) {
        .sui-toast { animation: none; }
        .sui-dialog__button { transition: none; }
    }
</style>

<div class="sui-toast-region" id="suiToastRegion" aria-live="polite" aria-relevant="additions removals">
    @foreach ($pesanNotifikasi as $notifikasi)
        <div class="sui-toast sui-toast--{{ $notifikasi['tipe'] }}" data-sui-toast role="{{ $notifikasi['tipe'] === 'error' ? 'alert' : 'status' }}">
            <span class="sui-toast__mark" aria-hidden="true"></span>
            <div class="sui-toast__body">
                <strong class="sui-toast__title">
                    {{ match ($notifikasi['tipe']) {
                        'success' => 'Berhasil',
                        'error' => 'Terjadi kesalahan',
                        'warning' => 'Perhatian',
                        default => 'Informasi',
                    } }}
                </strong>
                <p class="sui-toast__message">{{ $notifikasi['pesan'] }}</p>
            </div>
            <button type="button" class="sui-toast__close" data-sui-toast-close aria-label="Tutup notifikasi">&times;</button>
        </div>
    @endforeach
</div>

<dialog class="sui-dialog" id="suiConfirmDialog" aria-labelledby="suiConfirmTitle" aria-describedby="suiConfirmMessage" data-tone="primary">
    <div class="sui-dialog__card">
        <div class="sui-dialog__head">
            <div>
                <span class="sui-dialog__icon" aria-hidden="true"></span>
                <h2 class="sui-dialog__title" id="suiConfirmTitle">Konfirmasi tindakan</h2>
            </div>
            <button type="button" class="sui-dialog__close" data-sui-dialog-close aria-label="Tutup dialog">&times;</button>
        </div>
        <p class="sui-dialog__message" id="suiConfirmMessage"></p>
        <div class="sui-dialog__actions">
            <button type="button" class="sui-dialog__button sui-dialog__button--secondary" id="suiConfirmCancel" data-sui-dialog-cancel>Batal</button>
            <button type="button" class="sui-dialog__button sui-dialog__button--primary" id="suiConfirmAccept" data-sui-dialog-accept>Ya, Lanjutkan</button>
        </div>
    </div>
</dialog>

<dialog class="sui-dialog" id="suiPromptDialog" aria-labelledby="suiPromptTitle" aria-describedby="suiPromptMessage" data-tone="primary">
    <div class="sui-dialog__card">
        <div class="sui-dialog__head">
            <div>
                <span class="sui-dialog__icon" aria-hidden="true"></span>
                <h2 class="sui-dialog__title" id="suiPromptTitle">Masukkan data</h2>
            </div>
            <button type="button" class="sui-dialog__close" data-sui-dialog-close aria-label="Tutup dialog">&times;</button>
        </div>
        <p class="sui-dialog__message" id="suiPromptMessage"></p>
        <div class="sui-dialog__field">
            <label class="sui-dialog__label" id="suiPromptLabel" for="suiPromptInput">Keterangan</label>
            <textarea class="sui-dialog__textarea" id="suiPromptInput" rows="4"></textarea>
            <p class="sui-dialog__error" id="suiPromptError" data-sui-prompt-error hidden></p>
        </div>
        <div class="sui-dialog__actions">
            <button type="button" class="sui-dialog__button sui-dialog__button--secondary" id="suiPromptCancel" data-sui-dialog-cancel>Batal</button>
            <button type="button" class="sui-dialog__button sui-dialog__button--primary" id="suiPromptAccept" data-sui-dialog-accept>Simpan</button>
        </div>
    </div>
</dialog>

<script>
    (() => {
        'use strict';

        const confirmDialog = document.getElementById('suiConfirmDialog');
        const promptDialog = document.getElementById('suiPromptDialog');
        const toastRegion = document.getElementById('suiToastRegion');
        const dialogStates = new WeakMap();
        const pendingForms = new WeakSet();
        const confirmedForms = new WeakSet();
        const toastTitles = {
            success: 'Berhasil',
            error: 'Terjadi kesalahan',
            warning: 'Perhatian',
            info: 'Informasi',
            status: 'Informasi',
        };

        function focusElement(element) {
            if (!element) return;
            window.setTimeout(() => element.focus(), 0);
        }

        function finishDialog(dialog, result) {
            const state = dialogStates.get(dialog);
            if (!state) return;

            dialogStates.delete(dialog);
            dialog.close();

            if (!document.querySelector('dialog[open]')) {
                document.body.classList.remove('sui-dialog-open');
            }

            if (state.returnFocus && state.returnFocus.isConnected) {
                state.returnFocus.focus();
            }

            state.resolve(result);
        }

        function openDialog(dialog, resolve, focusTarget, fallbackResult) {
            const previousState = dialogStates.get(dialog);
            if (previousState) {
                finishDialog(dialog, fallbackResult);
            }

            dialogStates.set(dialog, {
                resolve,
                returnFocus: document.activeElement,
            });

            document.body.classList.add('sui-dialog-open');
            dialog.showModal();
            focusElement(focusTarget);
        }

        function confirmAction(options = {}) {
            const config = typeof options === 'string' ? { message: options } : options;
            const title = document.getElementById('suiConfirmTitle');
            const message = document.getElementById('suiConfirmMessage');
            const cancel = document.getElementById('suiConfirmCancel');
            const accept = document.getElementById('suiConfirmAccept');

            title.textContent = config.title || 'Konfirmasi tindakan';
            message.textContent = config.message || 'Lanjutkan tindakan ini?';
            cancel.textContent = config.cancelText || 'Batal';
            cancel.hidden = config.hideCancel === true;
            accept.textContent = config.confirmText || 'Ya, Lanjutkan';
            accept.className = 'sui-dialog__button ' + (config.tone === 'danger'
                ? 'sui-dialog__button--danger'
                : 'sui-dialog__button--primary');
            confirmDialog.dataset.tone = config.tone || 'primary';

            return new Promise((resolve) => {
                openDialog(confirmDialog, resolve, cancel.hidden ? accept : cancel, false);
            });
        }

        function alertAction(options = {}) {
            const config = typeof options === 'string' ? { message: options } : options;

            return confirmAction({
                title: config.title || 'Informasi',
                message: config.message || '',
                confirmText: config.confirmText || 'Mengerti',
                hideCancel: true,
                tone: config.tone || 'info',
            }).then(() => undefined);
        }

        function promptAction(options = {}) {
            const config = typeof options === 'string' ? { message: options } : options;
            const title = document.getElementById('suiPromptTitle');
            const message = document.getElementById('suiPromptMessage');
            const label = document.getElementById('suiPromptLabel');
            const input = document.getElementById('suiPromptInput');
            const error = document.getElementById('suiPromptError');
            const cancel = document.getElementById('suiPromptCancel');
            const accept = document.getElementById('suiPromptAccept');

            title.textContent = config.title || 'Masukkan data';
            message.textContent = config.message || '';
            message.hidden = !config.message;
            label.textContent = config.label || 'Keterangan';
            input.value = config.value || '';
            input.placeholder = config.placeholder || '';
            input.rows = config.rows || 4;
            input.required = config.required !== false;
            input.minLength = config.minLength || 0;
            input.maxLength = config.maxLength || 500;
            error.textContent = '';
            error.hidden = true;
            cancel.textContent = config.cancelText || 'Batal';
            accept.textContent = config.confirmText || 'Simpan';
            promptDialog.dataset.tone = config.tone || 'primary';

            return new Promise((resolve) => {
                openDialog(promptDialog, resolve, input, null);

                function acceptValue() {
                    const value = input.value.trim();
                    let validationMessage = '';

                    if (input.required && value.length === 0) {
                        validationMessage = config.requiredMessage || 'Kolom ini wajib diisi.';
                    } else if (value.length < input.minLength) {
                        validationMessage = config.minLengthMessage
                            || `Masukkan minimal ${input.minLength} karakter.`;
                    }

                    if (validationMessage) {
                        error.textContent = validationMessage;
                        error.hidden = false;
                        input.focus();
                        return;
                    }

                    finishDialog(promptDialog, value);
                }

                accept.onclick = acceptValue;
                input.onkeydown = (event) => {
                    if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
                        event.preventDefault();
                        acceptValue();
                    }
                };
            });
        }

        function removeToast(toast) {
            if (!toast || !toast.isConnected) return;
            toast.remove();
        }

        function initializeToast(toast) {
            if (!toast || toast.dataset.suiInitialized === '1') return;
            toast.dataset.suiInitialized = '1';

            const close = toast.querySelector('[data-sui-toast-close]');
            const duration = Number(toast.dataset.suiDuration || (toast.classList.contains('sui-toast--error') ? 9000 : 6500));
            let timeout = null;
            let remaining = duration;
            let startedAt = Date.now();

            function stopTimer() {
                if (timeout === null) return;
                window.clearTimeout(timeout);
                timeout = null;
                remaining = Math.max(0, remaining - (Date.now() - startedAt));
            }

            function startTimer() {
                if (timeout !== null || remaining <= 0) return;
                startedAt = Date.now();
                timeout = window.setTimeout(() => removeToast(toast), remaining);
            }

            if (close) close.addEventListener('click', () => removeToast(toast));
            toast.addEventListener('mouseenter', stopTimer);
            toast.addEventListener('mouseleave', startTimer);
            toast.addEventListener('focusin', stopTimer);
            toast.addEventListener('focusout', startTimer);
            startTimer();
        }

        function toast(message, type = 'info', options = {}) {
            if (!message || !toastRegion) return null;
            const normalizedType = Object.hasOwn(toastTitles, type) ? type : 'info';
            const element = document.createElement('div');
            const mark = document.createElement('span');
            const body = document.createElement('div');
            const title = document.createElement('strong');
            const text = document.createElement('p');
            const close = document.createElement('button');

            element.className = `sui-toast sui-toast--${normalizedType}`;
            element.dataset.suiToast = '';
            element.dataset.suiDuration = String(options.duration ?? (normalizedType === 'error' ? 9000 : 6500));
            element.setAttribute('role', normalizedType === 'error' ? 'alert' : 'status');
            mark.className = 'sui-toast__mark';
            mark.setAttribute('aria-hidden', 'true');
            body.className = 'sui-toast__body';
            title.className = 'sui-toast__title';
            title.textContent = options.title || toastTitles[normalizedType];
            text.className = 'sui-toast__message';
            text.textContent = String(message);
            close.className = 'sui-toast__close';
            close.type = 'button';
            close.dataset.suiToastClose = '';
            close.setAttribute('aria-label', 'Tutup notifikasi');
            close.textContent = '×';

            body.append(title, text);
            element.append(mark, body, close);
            toastRegion.append(element);
            initializeToast(element);

            return element;
        }

        function formConfirmOptions(form) {
            return {
                title: form.dataset.suiConfirmTitle || 'Konfirmasi tindakan',
                message: form.dataset.suiConfirm || 'Lanjutkan tindakan ini?',
                confirmText: form.dataset.suiConfirmLabel || 'Ya, Lanjutkan',
                cancelText: form.dataset.suiConfirmCancel || 'Batal',
                tone: form.dataset.suiConfirmTone === 'danger' ? 'danger' : 'primary',
            };
        }

        document.addEventListener('click', (event) => {
            const alertTrigger = event.target.closest('[data-sui-alert]');
            if (!alertTrigger) return;

            event.preventDefault();
            alertAction({
                title: alertTrigger.dataset.suiAlertTitle || 'Informasi',
                message: alertTrigger.dataset.suiAlert,
                tone: alertTrigger.dataset.suiAlertTone === 'danger' ? 'danger' : 'info',
            });
        });

        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-sui-confirm')) return;

            if (confirmedForms.has(form)) {
                confirmedForms.delete(form);
                return;
            }

            event.preventDefault();
            if (pendingForms.has(form)) return;
            pendingForms.add(form);

            const submitter = event.submitter;
            confirmAction(formConfirmOptions(form)).then((accepted) => {
                pendingForms.delete(form);
                if (!accepted) return;

                confirmedForms.add(form);
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit(submitter || undefined);
                    window.setTimeout(() => confirmedForms.delete(form), 0);
                } else {
                    form.submit();
                }
            });
        });

        [confirmDialog, promptDialog].forEach((dialog) => {
            dialog.addEventListener('cancel', (event) => {
                event.preventDefault();
                finishDialog(dialog, dialog === promptDialog ? null : false);
            });

            dialog.addEventListener('click', (event) => {
                if (event.target === dialog) {
                    finishDialog(dialog, dialog === promptDialog ? null : false);
                }
            });

            dialog.querySelectorAll('[data-sui-dialog-close], [data-sui-dialog-cancel]').forEach((button) => {
                button.addEventListener('click', () => {
                    finishDialog(dialog, dialog === promptDialog ? null : false);
                });
            });
        });

        document.getElementById('suiConfirmAccept').addEventListener('click', () => {
            finishDialog(confirmDialog, true);
        });

        function initialize() {
            toastRegion?.querySelectorAll('[data-sui-toast]').forEach(initializeToast);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initialize, { once: true });
        } else {
            initialize();
        }

        window.SimahadUI = Object.freeze({
            toast,
            confirm: confirmAction,
            prompt: promptAction,
            alert: alertAction,
        });
    })();
</script>
