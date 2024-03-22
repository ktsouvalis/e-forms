var clipboard = new ClipboardJS('.copy-button');

clipboard.on('success', function (e) {
    alert('Αντιγράφτηκε: ' + e.text);
});

clipboard.on('error', function (e) {
    alert('Αποτυχία αντιγραφής');
});

$(document).on("mouseup", '.copy-button', function (event) {
    event.preventDefault();
});