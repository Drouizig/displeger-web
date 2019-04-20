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
require('bootstrap');
require('webpack-jquery-ui/css');
$(document).ready(function() {
  var autocompleteInput = $('.autocomplete');
  var url = autocompleteInput.data('url');
  autocompleteInput.autocomplete({
      source: url,
      select: function( event, ui ) {
        var url = ui.item['value'];
        window.location.href = url;
        return false;
      },
      focus: function(event, ui) {
        this.value = ui.item['label'];
        return false;
      }
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

$('.js-contact-form').submit(function() {
  $.post(
    $(this).attr('action'),
    $(this).serialize(),
    function(data) {
        if (data.result == 'ok') {
          displayMessage('Kaset eo bet ar gemennadenn gant berzh!', 'ok');
          $('.js-contact-form input').val('');
          $('.missing_translation_form').slideUp();
        } else {
          displayMessage('Degouezhet ez eus bet ur fazi en ur gas ar gemennadenn, klaskit en-dro mar plij', 'error');
        }
    }
  );
  return false;
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
  setTimeout(slideUpMessage, 2000);
}

function slideUpMessage()
{
  $('.message').slideUp();
}