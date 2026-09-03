function showButtonSpinner($btn) {
    $btn.prop('disabled', true); // disable button
    $btn.data('original-text', $btn.html()); // store original text
    $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
}

function hideButtonSpinner($btn) {
    $btn.prop('disabled', false); // enable button
    $btn.html($btn.data('original-text')); // restore original text
}
function exportTableButtons(table_id) {

    var shop_name = $("#shop_name").val() || '';
    var today = $("#today").val() || '';
    var print_access = $("#print_access").val() || '';
    var userType = $("#userType").val() || '';
    var shop_address_one = $('#shop_address_one').val() || '';
    var shop_address_two = $('#shop_address_two').val() || '';
    var shop_city = $('#shop_city').val() || '';
    var shop_number = $('#shop_number').val() || '';
    var reportname = $("#title").val() || 'Report';

    var $table = $('#' + table_id);

    if($table.length == 0) {
        console.log("Table not found: " + table_id);
        return;
    }

    // Create button container
    var $btnWrap = $('<div id="tableExport" class="d-flex justify-content-start gap-3 mb-3 mt-3"></div>');

    var $btnPdf = $('<button type="button" class="btn btn-outline-primary shadow-btn"><i class="ti ti-file-text"></i> PDF</button>');
    var $btnPrint = $('<button type="button" class="btn btn-outline-primary shadow-btn"><i class="ti ti-printer"></i> Print</button>');
    var $btnExcel = $('<button type="button" class="btn btn-outline-primary shadow-btn"><i class="ti ti-file-spreadsheet"></i> Excel</button>');
    var $btnCsv = $('<button type="button" class="btn btn-outline-primary shadow-btn"><i class="ti ti-file-spreadsheet"></i> CSV</button>');
    $btnWrap.append($btnPdf, $btnPrint, $btnExcel, $btnCsv);
    if (userType === '1' || print_access === '1') {
        if ($table.prev('.export-btn-wrap').length === 0) {
            $btnWrap.addClass('export-btn-wrap');
            $table.before($btnWrap);
        }
    }

    
    function getTableData() 
    {
        var dt = $table.DataTable();   // get datatable instance

        var headers = [];

        // Get headers except last
        $table.find('thead tr th:not(:last-child)').each(function () {
            headers.push($(this).text().trim());
        });

        var rows = [];

        // Get ALL rows from DataTables (ignore pagination)
        dt.rows({ search: 'applied' }).every(function () {

            var rowData = this.data(); // array of column values
            var row = [];

            // Exclude last column
            for (var i = 0; i < rowData.length - 1; i++) {

                // If column contains HTML (buttons etc), strip it
                var text = $('<div>').html(rowData[i]).text().trim();
                row.push(text);
            }

            rows.push(row);
        });

        return { headers: headers, rows: rows };
    }


    function buildHeaderHtml() {

        var html = '';
        html += '<div style="text-align:center; margin-bottom:15px;">';
        html += '<h2 style="margin:0;">Printed Date:' + today + '</h2>';
        html += '<h2 style="margin:0;">' + shop_name + '</h2>';
        html += '<div style="margin:0;">' + shop_address_one + ' ' + shop_address_two + '</div>';
        html += '<div style="margin:0;">' + shop_city + '</div>';
        html += '<div style="margin:0;">' + shop_number + '</div>';
        html += '<h3 style="margin-top:10px;">' + reportname + '</h3>';
        html += '</div>';

        return html;
    }

    $btnPdf.off('click').on('click', function () {
        var $this = $(this);
        showButtonSpinner($this);

        setTimeout(function () {

            var data = getTableData();

            if (data.headers.length == 0 || data.rows.length == 0) {
                alert("No data to export");
                hideButtonSpinner($this);
                return;
            }

            const { jsPDF } = window.jspdf;

            var doc = new jsPDF({
                orientation: 'p',
                unit: 'pt',
                format: 'a4'
            });

            const pageWidth = doc.internal.pageSize.getWidth();

            let margin = 20;   // 🔥 small margin (change 15–25 if needed)
            let y = 30;

            doc.setFontSize(12);
            doc.text("Printed Date - " + today, margin, y);

            y += 16;
            doc.setFontSize(14);
            doc.text(shop_name, margin, y);

            y += 16;
            doc.setFontSize(10);
            doc.text(shop_address_one + ' ' + shop_address_two, margin, y);

            y += 14;
            doc.text(shop_city, margin, y);

            y += 14;
            doc.text(shop_number, margin, y);

            y += 20;
            doc.setFontSize(12);
            doc.text(reportname, margin, y);

            doc.autoTable({
                head: [data.headers],
                body: data.rows,
                startY: y + 15,
                margin: {
                    left: margin,
                    right: margin
                },
                styles: {
                    fontSize: 8,
                    cellPadding: 4
                },
                tableWidth: pageWidth - (margin * 2)
            });

            doc.save(reportname.replace(/\s+/g, '_') + ".pdf");

            hideButtonSpinner($this);

        }, 50);
    });

    // Excel
    $btnExcel.off('click').on('click', function() {
        var $this = $(this);
        showButtonSpinner($this);
        setTimeout(function() {
            var wb = XLSX.utils.book_new();
            var ws_data = [];
            ws_data.push(["Printed Date -", today]);
            ws_data.push(["Shop Name", shop_name]);
            ws_data.push(["Address", shop_address_one + " " + shop_address_two]);
            ws_data.push(["City", shop_city]);
            ws_data.push(["Contact", shop_number]);
            ws_data.push(["Report", reportname]);
            ws_data.push([]);
            var data = getTableData();
            ws_data.push(data.headers);
            data.rows.forEach(r => ws_data.push(r));
            var ws = XLSX.utils.aoa_to_sheet(ws_data);
            XLSX.utils.book_append_sheet(wb, ws, "Report");
            XLSX.writeFile(wb, reportname.replace(/\s+/g, '_') + ".xlsx");
            hideButtonSpinner($this);
        }, 50);
    });

    // CSV
    $btnCsv.off('click').on('click', function () {
        var $this = $(this);
        showButtonSpinner($this);

        setTimeout(function () { // let spinner render
            var data = getTableData();

            var lines = [];
            lines.push('Printed Date -,' + '"' + today.replace(/"/g, '""') + '"');
            lines.push('Shop Name,' + '"' + shop_name.replace(/"/g, '""') + '"');
            lines.push('Address,' + '"' + (shop_address_one + ' ' + shop_address_two).replace(/"/g, '""') + '"');
            lines.push('City,' + '"' + shop_city.replace(/"/g, '""') + '"');
            lines.push('Contact,' + '"' + shop_number.replace(/"/g, '""') + '"');
            lines.push('Report,' + '"' + reportname.replace(/"/g, '""') + '"');
            lines.push('');

            // header row
            lines.push(data.headers.map(h => '"' + h.replace(/"/g, '""') + '"').join(','));

            // data rows
            data.rows.forEach(function(r){
                lines.push(r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(','));
            });

            var blob = new Blob([lines.join('\n')], { type: "text/csv;charset=utf-8;" });
            var link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = reportname.replace(/\s+/g, '_') + ".csv";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            hideButtonSpinner($this); // restore button
        }, 50);
    });
    // Print
    $btnPrint.off('click').on('click', function () {
        var $this = $(this);
        showButtonSpinner($this);

        setTimeout(function () {
            var data = getTableData();

            var printWindow = window.open('', '', 'height=800,width=1200');
            printWindow.document.write('<html><head><title>' + reportname + '</title>');

            printWindow.document.write('<style>');
            printWindow.document.write('table{width:100%; border-collapse:collapse;}');
            printWindow.document.write('th,td{border:1px solid #000; padding:6px; font-size:12px;}');
            printWindow.document.write('th{background:#f2f2f2;}');
            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');

            printWindow.document.write(buildHeaderHtml());

            // Build table manually
            printWindow.document.write('<table>');
            printWindow.document.write('<thead><tr>');
            data.headers.forEach(function (h) {
                printWindow.document.write('<th>' + h + '</th>');
            });
            printWindow.document.write('</tr></thead>');

            printWindow.document.write('<tbody>');
            data.rows.forEach(function (row) {
                printWindow.document.write('<tr>');
                row.forEach(function (cell) {
                    printWindow.document.write('<td>' + cell + '</td>');
                });
                printWindow.document.write('</tr>');
            });
            printWindow.document.write('</tbody>');
            printWindow.document.write('</table>');

            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();

            hideButtonSpinner($this); // restore button
        }, 50);
    });
}
