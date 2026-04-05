/**
 * CNMX Popup System - Airbnb Style
 */

var CNMX = window.CNMX || {};

CNMX.Toast = {
    container: null,
    
    init: function() {
        this.container = document.createElement('div');
        this.container.className = 'cnmx-toast-container';
        document.body.appendChild(this.container);
    },
    
    show: function(options) {
        if (!this.container) this.init();
        
        const { type = 'info', title = '', message = '', duration = 4000 } = options;
        
        const icons = {
            success: '✓',
            error: '✕',
            info: 'ℹ'
        };
        
        const toast = document.createElement('div');
        toast.className = `cnmx-toast ${type}`;
        toast.innerHTML = `
            <span class="cnmx-toast-icon">${icons[type]}</span>
            <div class="cnmx-toast-content">
                ${title ? `<div class="cnmx-toast-title">${title}</div>` : ''}
                ${message ? `<div class="cnmx-toast-message">${message}</div>` : ''}
            </div>
            <button class="cnmx-toast-close">✕</button>
        `;
        
        toast.querySelector('.cnmx-toast-close').addEventListener('click', () => {
            this.hide(toast);
        });
        
        this.container.appendChild(toast);
        
        if (duration > 0) {
            setTimeout(() => this.hide(toast), duration);
        }
        
        return toast;
    },
    
    hide: function(toast) {
        if (!toast) return;
        toast.classList.add('hiding');
        setTimeout(() => toast.remove(), 300);
    },
    
    success: function(message, title = 'Éxito') {
        return this.show({ type: 'success', title, message });
    },
    
    error: function(message, title = 'Error') {
        return this.show({ type: 'error', title, message });
    },
    
    info: function(message, title = 'Info') {
        return this.show({ type: 'info', title, message });
    }
};

CNMX.Modal = {
    activeModal: null,
    
    show: function(options) {
        const { title = '', content = '', buttons = [], onClose = null } = options;
        
        const overlay = document.createElement('div');
        overlay.className = 'cnmx-modal-overlay';
        
        let buttonsHtml = '';
        if (buttons.length > 0) {
            buttonsHtml = '<div class="cnmx-modal-footer">';
            buttons.forEach(btn => {
                buttonsHtml += `<button class="cnmx-btn ${btn.class || 'cnmx-btn-secondary'}" data-action="${btn.action || ''}">${btn.text}</button>`;
            });
            buttonsHtml += '</div>';
        }
        
        overlay.innerHTML = `
            <div class="cnmx-modal">
                <div class="cnmx-modal-header">
                    <h3 class="cnmx-modal-title">${title}</h3>
                    <button class="cnmx-modal-close">✕</button>
                </div>
                <div class="cnmx-modal-body">${content}</div>
                ${buttonsHtml}
            </div>
        `;
        
        const close = () => {
            overlay.classList.remove('active');
            setTimeout(() => overlay.remove(), 300);
            if (onClose) onClose();
            this.activeModal = null;
        };
        
        overlay.querySelector('.cnmx-modal-close').addEventListener('click', close);
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) close();
        });
        
        buttons.forEach(btn => {
            overlay.querySelector(`[data-action="${btn.action}"]`)?.addEventListener('click', () => {
                if (btn.onClick) btn.onClick();
                if (btn.closeOnClick !== false) close();
            });
        });
        
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        
        setTimeout(() => overlay.classList.add('active'), 10);
        this.activeModal = overlay;
        
        return { close };
    },
    
    hide: function() {
        if (this.activeModal) {
            this.activeModal.querySelector('.cnmx-modal-close').click();
        }
    },
    
    confirm: function(message, onConfirm, onCancel) {
        return this.show({
            title: 'Confirmar',
            content: `<p style="margin:0; color:#374151;">${message}</p>`,
            buttons: [
                { text: 'Cancelar', action: 'cancel', class: 'cnmx-btn-secondary', onClick: onCancel },
                { text: 'Confirmar', action: 'confirm', class: 'cnmx-btn-primary', onClick: onConfirm }
            ]
        });
    },
    
    alert: function(message, onOk) {
        return this.show({
            title: 'Aviso',
            content: `<p style="margin:0; color:#374151;">${message}</p>`,
            buttons: [
                { text: 'OK', action: 'ok', class: 'cnmx-btn-primary', onClick: onOk }
            ]
        });
    }
};

// Helper functions
window.cnmxToast = CNMX.Toast.show.bind(CNMX.Toast);
window.cnmxToastSuccess = CNMX.Toast.success.bind(CNMX.Toast);
window.cnmxToastError = CNMX.Toast.error.bind(CNMX.Toast);
window.cnmxModal = CNMX.Modal.show.bind(CNMX.Modal);
window.cnmxModalConfirm = CNMX.Modal.confirm.bind(CNMX.Modal);
window.cnmxModalAlert = CNMX.Modal.alert.bind(CNMX.Modal);

// Initialize toast container on load
document.addEventListener('DOMContentLoaded', () => {
    CNMX.Toast.init();
});
