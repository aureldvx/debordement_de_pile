import '../styles/app.scss';
import '@popperjs/core';
import * as bootstrap from 'bootstrap';
import Alpine from 'alpinejs';

[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

window.Alpine = Alpine;
Alpine.start();
