import Alpine from 'alpinejs';
import $ from 'jquery';
import toastr from 'toastr';
import { Chart, registerables } from 'chart.js';
import L from 'leaflet';

Chart.register(...registerables);

window.$ = window.jQuery = $;
window.toastr = toastr;
window.Chart = Chart;
window.L = L;
window.Alpine = Alpine;

Alpine.start();
