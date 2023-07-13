function show_edit_option(id, canedit=0) {
    if (document.getElementById("user" + id).checked) {
        // alert(canedit);
        // Create the main container div
        var mainDiv = document.createElement("div");
        mainDiv.className = "vstack";
        mainDiv.id = "div" + id;

        // Create the first radio button and label
        var radio1 = document.createElement("input");
        radio1.type = "radio";
        radio1.id = "edit_yes" + id;
        radio1.value = "yes";
        radio1.name = "edit" + id;
        if (canedit === "checked") {
            radio1.checked = true;
        }

        var label1 = document.createElement("label");
        label1.htmlFor = "edit_yes" + id;
        label1.textContent = "Can Edit";

        // Create the second radio button and label
        var radio2 = document.createElement("input");
        radio2.type = "radio";
        radio2.id = "edit_no" + id;
        radio2.value = "no";
        radio2.name = "edit" + id;
        if (canedit != "checked") {
            radio2.checked = true;
        }

        var label2 = document.createElement("label");
        label2.htmlFor = "edit_no" + id;
        label2.textContent = "Can't Edit";

        // Append the radio buttons and labels to the main container div
        mainDiv.appendChild(createDivWithChildren([radio1, label1]));
        mainDiv.appendChild(createDivWithChildren([radio2, label2]));

        // Function to create a div with children elements
        function createDivWithChildren(children) {
            var div = document.createElement("div");
            div.className = "hstack gap-2";
            children.forEach(function (child) {
                div.appendChild(child);
            });
            return div;
        }

        // Append the main container div to the "space" div
        var spaceDiv = document.getElementById("space" + id);
        spaceDiv.appendChild(mainDiv);
    }
    else {
        // Remove the dynamically generated elements
        var elementToRemove = document.getElementById("div" + id);
        if (elementToRemove) {
            elementToRemove.parentNode.removeChild(elementToRemove);
        }
    }
}