document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('dl-xlsx').addEventListener('click', function () {
        var ws = XLSX.utils.table_to_sheet(document.getElementById('myTable'));
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
        var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
		let fname = document.getElementById('trg_name');
        saveAs(new Blob([wbout], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }), fname.textContent+'.xlsx');
    });
});