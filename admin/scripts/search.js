document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const eventTable = document.getElementById("eventTable");
    const rows = eventTable.getElementsByTagName("tr");

    searchInput.addEventListener("keyup", function () {
        const filter = searchInput.value.toLowerCase().trim();

        for (let i = 1; i < rows.length; i++) { // Start from 1 to skip header row
            const cells = rows[i].getElementsByTagName("td");
            let match = false;

            for (let j = 0; j < cells.length - 1; j++) { // Exclude action buttons column
                if (cells[j].textContent.toLowerCase().includes(filter)) {
                    match = true;
                    break;
                }
            }

            rows[i].style.display = match ? "" : "none"; // Hide non-matching rows
        }
    });
});
