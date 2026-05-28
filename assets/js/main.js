$(function() {
    $('form.ajax-submit').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();
        $.post(url, data, function(response) {
            if (response.redirect) {
                window.location.href = response.redirect;
                return;
            }
            if (response.message) {
                alert(response.message);
            }
            if (response.reload) {
                window.location.reload();
            }
        }, 'json').fail(function() {
            alert('Server error occurred.');
        });
    });
});
function tableToExcel(tableId, filename = 'export.xlsx') {
    var workbook = XLSX.utils.table_to_book(document.getElementById(tableId), {sheet: 'Sheet1'});
    XLSX.writeFile(workbook, filename);
}
