function createTicket(appname) {
    // console.log('Sending request to: ' + '/api/create_ticket/' + appname + '/' + school_code);
    // console.log('CSRF token: ' + document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    fetch('/microapp_create_ticket/' + appname+'/', {
        method: 'POST',
        // credentials: 'same-origin', 
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            // 'Content-Type': 'application/json'
        },
    })
        .then(response => {
            console.log('Received response', response);
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.error);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data', data);
            if (data.success) {
                // Open the ticket profile page in a new tab
                window.open('/ticket_profile/' + data.ticket_id + '#bottom', '_blank');
            }
        })
        .catch(error => {
            // Handle error
            console.error('An error occurred', error);
        });
}

document.getElementById('createTicketButton').addEventListener('click', function () {
    createTicket(appname);
});