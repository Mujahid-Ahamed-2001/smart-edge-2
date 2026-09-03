$(document).ready(function() {

    var shop_name = $("#shop_name").val() || '';
    var shop_address_one = $('#shop_address_one').val() || '';
    var shop_address_two = $('#shop_address_two').val() || '';
    var shop_city = $('#shop_city').val() || '';
    var shop_number = $('#shop_number').val() || '';
    var reportname = $("#title").val() || '';

    if(reportname) {
        document.title = reportname;
    }

    var table = $('#tbl_category').DataTable({
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
        autoWidth: false,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdfHtml5',
                title: '',
                customize: function (doc) {
                    doc.content.splice(0, 1, {
                        text: [
                            { text: shop_name + '\n', bold: true, fontSize: 18 },
                            { text: shop_address_one + '\n', fontSize: 14 },
                            { text: shop_address_two + '\n', fontSize: 14 },
                            { text: shop_city + '\n', fontSize: 14 },
                            { text: shop_number + '\n', fontSize: 14 }
                        ],
                        margin: [0, 0, 0, 12],
                        alignment: 'center'
                    });
                }
            },
            {
                extend: 'print',
                title: '',
                customize: function (win) {

                    var formattedCity = shop_city
                        ? shop_city.charAt(0).toUpperCase() + shop_city.slice(1).toLowerCase()
                        : '';

                    $(win.document.body).prepend(
                        '<div style="text-align: center; margin-bottom: 20px;">' +
                            '<h2>' + shop_name + '</h2>' +
                            '<p style="margin-bottom: 5px;">' + shop_address_one + ', ' + shop_address_two + '</p>' +
                            '<p style="margin-bottom: 5px;">' + formattedCity + '</p>' +
                            '<p style="margin-bottom: 5px;">' + shop_number + '</p>' +
                        '</div>'
                    );
                }
            },
            { extend: 'excelHtml5', title: '' },
            { extend: 'csvHtml5' }
        ]
    });

    $(".dt-buttons").addClass('w-100 d-flex justify-content-center');
    $(".dt-button").addClass('btn btn-default w-10');
});
