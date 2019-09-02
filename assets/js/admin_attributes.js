$(document).ready(function () {
    var $wrapper = $('.attributes-wrapper');

    $wrapper.on('click', '.remove-attribute', function (e) {
        e.preventDefault();

        // Remove closest attribute item
        $(this).closest('.attribute-item').fadeOut().remove();
    });

    $('#addAttributeBtn').click(function (e) {
        e.preventDefault();

        // Get data-prototype
        var prototype = $wrapper.data('prototype');

        // Get new index
        var index = $wrapper.data('index');

        // Replace '__name__' in the prototype's HTML form,
        // with index, the number of how many items we have
        var newForm = prototype.replace(/__name__/g, index);

        // Increase the index by one for the next item
        $wrapper.data('index', index+1);

        $(this).before(newForm);
    });
});