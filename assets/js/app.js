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

  $('.copy').click(function() {
    var text = $(this).parent().find('.js-verb').text();
    if (!navigator.clipboard) {
      $(this).parent().find('.js-verb').focus().select();
      try {
        var successful = document.execCommand('copy');
        if(successful) {
          displayMessage('Eilet eo bet ar verb displeget er golver!', 'ok');
        } else {
          displayMessage('Fazi en ur eilañ ar verb displeget er golver', 'error');
        }
      } catch (err) {
        displayMessage('Fazi en ur eilañ ar verb displeget er golver', 'error');
      }
      return;
    }
    navigator.clipboard.writeText(text).then(function() {
      displayMessage('Eilet eo bet ar verb displeget er golver!', 'ok');
    }, function(err) {
      displayMessage('Fazi en ur eilañ ar verb displeget er golver', 'error');
    });
  });

  $('.message').click(function() {
    $(this).slideUp();
  });

  $('.missing_translation_notice').click(function() {
    $('.missing_translation_form').slideToggle();
  });
  
});

function displayMessage(message, type) {
  var messageContainer = $('.message');
  if(messageContainer.hasClass('error')) {
      messageContainer.removeClass('error');
  }
  if(messageContainer.hasClass('ok')) {
      messageContainer.removeClass('ok');
  }
  messageContainer.addClass(type);
  messageContainer.text(message);
  messageContainer.slideDown();
}