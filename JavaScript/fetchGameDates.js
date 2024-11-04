document.addEventListener("DOMContentLoaded", () => {
    fetchGameDates("Red Card Black Card", "redBlackDatesTable", "redBlackView.html");
    fetchGameDates("Wheat & Steel", "wheatSteelDatesTable", "wheatSteelView.html");
});

async function fetchGameDates(gameType, tableId, linkPage) {
    try {
        // Fetch dates from the backend for each game type
        const response = await fetch(`/api/game-dates?gameType=${encodeURIComponent(gameType)}`);
        const dates = await response.json();

        // Get the table body where dates will be appended
        const tableBody = document.getElementById(tableId);
        let row;

        dates.forEach((date, index) => {
            // Create a new row for every 2 dates
            if (index % 2 === 0) {
                row = document.createElement("tr");
                tableBody.appendChild(row);
            }

            // Format date for link
            const dateFormatted = new Date(date).toLocaleDateString("en-US");

            // Create cell with a link to the game view page
            const cell = document.createElement("td");
            cell.className = "gameDateCell";
            cell.innerHTML = `<a href="${linkPage}?date=${dateFormatted}">${dateFormatted}</a>`;
            row.appendChild(cell);
        });
    } catch (error) {
        console.error("Error fetching game dates:", error);
    }
}
