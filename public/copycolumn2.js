// Attach an event listener to the button or clickable element
document.getElementById("copyMailButton").addEventListener("click", function () {
    // Iterate through the table rows and extract the values from the "mail" column
    var mailColumn = document.querySelectorAll("#dataTable tbody td:nth-child(5)");
    var mailValues = Array.from(mailColumn).map(function (cell) {
        return cell.textContent.trim();
    });

    // Concatenate the values with a delimiter (comma in this example)
    var concatenatedValues = mailValues.join(",");

    // Create a temporary text area element
    var tempTextArea = document.createElement("textarea");

    // Set the concatenated values as the text area's value
    tempTextArea.value = concatenatedValues;

    // Append the text area to the document body
    document.body.appendChild(tempTextArea);

    // Programmatically select the text within the text area
    tempTextArea.select();

    // Execute the copy command to copy the selected text to the clipboard
    document.execCommand("copy");

    // Remove the temporary text area from the document
    document.body.removeChild(tempTextArea);

    // Optionally, provide user feedback (e.g., show a success message)
    alert("Αντιγράφτηκαν " + mailValues.length + " emails");
});
