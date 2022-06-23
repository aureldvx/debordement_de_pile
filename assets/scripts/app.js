import '../styles/app.scss';
import '@popperjs/core';
import * as bootstrap from 'bootstrap';

[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

const datetimeFormat = new Intl.DateTimeFormat(undefined, {
    day: '2-digit',
    weekday: undefined,
    year: 'numeric',
    month: 'long',
    hour: 'numeric',
    minute: 'numeric',
});

[].slice.call(document.querySelectorAll('[data-intl-date]')).map(intlEl => {
    intlEl.textContent = datetimeFormat.format(new Date(intlEl.dataset.intlDate));
});
