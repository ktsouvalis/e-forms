// Attach an event listener to the button or clickable element
document.getElementById("copyCodeButton").addEventListener("click", function () {
    // Iterate through the table rows and extract the values from the "code" column
    var codeColumn = document.querySelectorAll("#dataTable tbody td:nth-child(3)");
    var codeValues = Array.from(codeColumn).map(function (cell) {
        return cell.textContent.trim();
    });

    // Concatenate the values with a delimiter (comma in this example)
    var concatenatedValues = codeValues.join(",");

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
    alert("Copied " + codeValues.length + " code values to clipboard!");
});
