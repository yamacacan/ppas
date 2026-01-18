
$(document).ready(function () {
    $('#print-button').click(function () {
        var printContent = $('.content').html();
        var originalContent = $('body').html();

        $('body').html(printContent);
        window.print();
        $('body').html(originalContent);
    });
});

