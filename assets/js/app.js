//var $ = require('jquery');

// This seems legacy stuff...
window.$ = $;

require('bootstrap-sass');

require('chart.js');
require('bootstrap-datepicker');

$(document).ready(function() {
    $('.js-datepicker').datepicker();
});
