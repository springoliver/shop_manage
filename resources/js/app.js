import './bootstrap';

import Alpine from 'alpinejs';
import DataTable from 'datatables.net-dt';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import flatpickr from 'flatpickr';
import $ from 'jquery';

window.Alpine = Alpine;
window.DataTable = DataTable;
window.FullCalendar = { Calendar };
window.fullCalendarPlugins = {
    dayGrid: dayGridPlugin,
    interaction: interactionPlugin
};
window.flatpickr = flatpickr;
window.$ = window.jQuery = $;

Alpine.start();
