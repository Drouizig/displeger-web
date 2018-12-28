/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/global.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
require('webpack-jquery-ui');
require('webpack-jquery-ui/css');
$(document).ready(function() {
  var autocompleteInput = $('.autocomplete');
  var url = autocompleteInput.data('url');
  autocompleteInput.autocomplete({
      source: url
    });
  } );