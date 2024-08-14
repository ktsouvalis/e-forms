function showLeaveProcedure(leaveType, leaveDays, isDirector, isPermanent) {
    if(!isPermanent) {
        return 'Οι άδειες των αναπληρωτών εκπαιδευτικών αναρτώνται στο invoices.';
    }
    switch (leaveType) {
        case 'Κανονική':
            if(leaveDays <= 10) {
                if(isDirector) {
                    return '<strong>Διαδικασία:</strong> Η κανονική άδεια του Διευθυντή/Προϊσταμένου Σχ. Μονάδας εγκρίνεται από τη Διεύθυνση. <br>Υποβολή στη Διεύθυνση: <ul><li>Αίτηση Δ/ντή-Προϊστ. σε ειδικό έντυπο.</li></ul><br>Έως 10 ημέρες.';
                } else {
                    return '<strong>Διαδικασία:</strong> Η κανονική άδεια εκπαιδευτικού εγκρίνεται από το Διεθυντή του Σχολείου. <br>Υποβολή στη Διεύθυνση: <ul><li>Αίτηση Εκπαιδευτικού</li><li>Χορήγηση Σχολείου</li></ul>.<br>Έως 10 ημέρες.';
                }
            } else {
                return 'Προσοχή! Υπάρχουν ήδη εγκεκριμένες περισσότερες από 10 μέρες κανονικής άδειας.';
            }
        break;
        case 'ΑΝΑΡΡΩΤΙΚΗ - με Υπεύθυνη Δήλωση':
            if(leaveDays <= 1) {
                return '<strong>Διαδικασία:</strong> Η αναρρωτική άδεια με Υπεύθυνη Δήλωση εκπαιδευτικού εγκρίνεται από τον Διευθυντή του Σχολείου. <br>Υποβολή στη Διεύθυνση: <ul><li>Αίτηση Εκπαιδευτικού (έντυπο με Υ.Δ.)</li><li>Χορήγηση Σχολείου</li></ul>.<br>Έως 2 ημέρες, όχι συνεχόμενα.';
            } else {
                return 'Προσοχή! Υπάρχουν ήδη 2 εγκεκριμένες μέρες αναρρωτικής άδειας με Υπεύθυνη Δήλωση.';
            }
        break;
        case 'ΑΝΑΡΡΩΤΙΚΗ - με Ιατρική Γνωμάτευση':

        break;

        case 'ΑΝΑΡΡΩΤΙΚΗ - με Γνωμάτευση Α/βάθμιας Υγειονομικής Επιτροπής (όχι Επαπειλούμενης Κύησης)':

        break;
        case 'ΑΝΑΡΡΩΤΙΚΗ - με Γνωμάτευση Β/βάθμιας Υγειονομικής Επιτροπής':

        break;
        case 'ΑΝΑΡΡΩΤΙΚΗ - με Γνωμάτευση Νοσοκομείου (ν.3528/2007 άρ.56, παρ.3)':

        break; 
    }

}

$(document).ready(function() {                
    $(document).on('mousedown', 'a[data-toggle="modal"]', function (event) {
        var leaves
        event.preventDefault();
        const getTeacherLeavesURL = $(this).data('get-teacher-leaves-url');
        var leaveId = $(this).data('leave-id');

        $.ajax({
            url: getTeacherLeavesURL,
            type: 'GET',
            // data: {
            //     private_note: note
            // },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                leaves = response.leaves;
                isDirector = response.isDirector;
                isPermanent = leaves[0].am === null ? false : true;
                //H javascript μεταβλητή leaves περιλαμβάνει όλες τις άδειες της συγκεκριμένης κατηγορίας για το συγκεκριμένο εκπαιδευτικό
                //Μένει να φιλτραριστεί το έτος και να μετρηθούν τα συγκεντρωτικά αποτελέσματα για το συγκεκριμένο έτος
                var currentYear = new Date().getFullYear();
                var leavesOfYear = [];
                for(let leave of leaves){
                    var leaveYear = new Date(leave.leave_start_date).getFullYear();
                    if(leaveYear === currentYear){
                        leavesOfYear.push(leave);
                    }
                }
                var totalLeaveDays = leavesOfYear.reduce((acc, leave) => acc + leave.leave_days, 0);
                $('#infoModal .modal-header .modal-title').text('Άδεια ' + leavesOfYear[0].leave_type);
                $('#infoModal .modal-body p:eq(0)').text('Εκπαιδευτικός: ' + leavesOfYear[0].surname + ' ' + leavesOfYear[0].name);
                $('#infoModal .modal-body p:eq(1)').text('Συνολικός Αριθμός Ημερών το ' + currentYear + ': ' + totalLeaveDays);
                $('#infoModal .modal-body p:eq(2)').text(' ');
                $('#infoModal .modal-body p:eq(3)').html(showLeaveProcedure(leavesOfYear[0].leave_type, totalLeaveDays, isDirector, isPermanent));
                $('#infoModal .modal-body p:eq(4)').html('<strong>Έντυπα:</strong> <a href="https://drive.google.com/drive/folders/197AHhCQGq3129WLs_U1QlmpOqEYi2Zey" target="_blank"> Έντυπα Αδειών</a>');
                $('#infoModal .modal-body p:eq(5)').text('');
                setTimeout(function() {
                    $('#infoModal').modal('show');
                }, 50);
            },
            error: function (error) {
                console.log("An error occurred: " + error);
            }
        }); //ajax
    });   //mousedown      
}); //document.ready
