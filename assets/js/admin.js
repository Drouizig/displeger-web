
require('select2');
$(document).ready(function() {

  $('select').select2();

  jQuery('.add-element').click(function (e) {
    var list = jQuery(jQuery(this).attr('data-list-selector'));
    var counter = list.data('widget-counter') || list.children().length;

    var newWidget = list.attr('data-prototype');
    newWidget = newWidget.replace(/__name__/g, counter);
    counter++;
    list.data('widget-counter', counter);

    var newElem = jQuery(newWidget);
    newElem.find('.delete-element').click(function (e) {
      $(this).parent().parent().parent().remove();
    });

    newElem.appendTo(list);
    newElem.find('select').select2();
  });

  jQuery('.delete-element').click(function (e) {
    $(this).parent().parent().parent().remove();
  });

});

