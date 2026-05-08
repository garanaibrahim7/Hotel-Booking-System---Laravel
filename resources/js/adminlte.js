import $ from 'jquery';
window.jQuery = window.$ = $;

import * as bootstrap from 'bootstrap'
window.bootstrap = bootstrap

import { OverlayScrollbars } from 'overlayscrollbars';
import 'overlayscrollbars/overlayscrollbars.css';
window.OverlayScrollbars = OverlayScrollbars;

import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;

import '../vendor/adminlte/src/ts/adminlte';


import select2 from 'select2'
select2();

import jsVectorMap from "jsvectormap/src/js/index.js";
import "jsvectormap/dist/maps/world.js";

import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
window.Swal = Swal;

import './scripts';

// Swal.fire({
//     title: 'Test',
//     text: 'If styled → fixed',
//     icon: 'success'
// });

