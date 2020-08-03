// Toggle the visiblity of given element
function toggleVisibility(activeIDs, inactiveIDs) {
    activeIDs.forEach(elementID => {
        var element = document.getElementById(elementID);
        if(element.style.display == "block")
            element.style.display = "none";
        else
            element.style.display = "block";
    });

    inactiveIDs.forEach(elementID => {
        var element = document.getElementById(elementID);
        element.style.display = "none";

    });
} 