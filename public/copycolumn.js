document.getElementById("copyCodeButton").addEventListener("click", function () {
    var codeColumn = document.querySelectorAll("#dataTable tbody td:nth-child(3)");
    var codeValues = Array.from(codeColumn).map(function (cell) {
        return cell.textContent.trim();
    });

    // Create a temporary text area element
    var tempTextArea = document.createElement("textarea");

    // Set the code values as the text area's value, separated by newlines
    tempTextArea.value = codeValues.join("\n");

    // Append the text area to the document body
    document.body.appendChild(tempTextArea);

    // Programmatically select the text within the text area
    tempTextArea.select();

    // Execute the copy command to copy the selected text to the clipboard
    document.execCommand("copy");

    // Remove the temporary text area from the document
    document.body.removeChild(tempTextArea);

    // Optionally, provide user feedback (e.g., show a success message)
    alert("Αντιγράφτηκαν " + codeValues.length + " Κωδικοί/ΑΦΜ για επικόλληση σε αρχείο xlsx!");
});