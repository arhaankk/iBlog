document.addEventListener("DOMContentLoaded", function () {
    const editButton = document.getElementById("edit-button");
    const saveButton = document.getElementById("save-button");
    const inputs = document.querySelectorAll(".login__input");

    editButton.addEventListener("click", function () {
        inputs.forEach(input => input.removeAttribute("disabled"));
        saveButton.style.display = "block";
        editButton.style.display = "none";
    });

    saveButton.addEventListener("click", function () {
        inputs.forEach(input => input.setAttribute("disabled", true));
        saveButton.style.display = "none";
        editButton.style.display = "block";
    });
});
