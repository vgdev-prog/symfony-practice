import {Controller} from '@hotwired/stimulus';
import Toastify from 'toastify-js'

export default class extends Controller {
    connect() {
        const rawValue = this.element.getAttribute('data-flash-messages-value');
        try {
            const parsed = JSON.parse(rawValue);
            console.log('Parsed value:', parsed);
            console.log('Type:', typeof parsed, 'Array?', Array.isArray(parsed));

            // Если это объект с сообщениями
            if (typeof parsed === 'object' && !Array.isArray(parsed) && Object.keys(parsed).length > 0) {
                Object.entries(parsed).forEach(([type, messages]) => {
                    messages.forEach(message => {
                        this.showToast(message, type);
                    });
                });
            }

        } catch (e) {
            console.log('No flash messages to display');
        }
    }

    getToastConfig(type) {
        const configs = {
            success: {
                backgroundColor: '#28a745',
                icon: '<i class="fas fa-check-circle me-2"></i>',
                className: 'toast-success'
            },
            error: {
                backgroundColor: '#dc3545', 
                icon: '<i class="fas fa-exclamation-circle me-2"></i>',
                className: 'toast-error'
            },
            warning: {
                backgroundColor: '#ffc107',
                icon: '<i class="fas fa-exclamation-triangle me-2"></i>',
                className: 'toast-warning'
            },
            info: {
                backgroundColor: '#17a2b8',
                icon: '<i class="fas fa-info-circle me-2"></i>', 
                className: 'toast-info'
            }
        };

        return configs[type] || configs.info;
    }

    showToast(message, type) {
        const config = this.getToastConfig(type);
        
        Toastify({
            text: `${config.icon}${message}`,
            duration: 4000,
            close: true,
            gravity: "top",
            position: "right",
            stopOnFocus: true,
            backgroundColor: config.backgroundColor,
            className: config.className,
            escapeMarkup: false,
            style: {
                background: config.backgroundColor,
                borderRadius: '8px',
                fontFamily: 'inherit',
                fontSize: '14px',
                fontWeight: '500',
                boxShadow: '0 4px 12px rgba(0, 0, 0, 0.15)',
                border: 'none'
            }
        }).showToast();
    }
}
