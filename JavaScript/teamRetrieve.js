async function displayInfoRedBlack() {
    // Find out what the contents of each card cell are to color the background appropriately
    let cardElements = document.querySelectorAll(".card");
    for (let card of cardElements) {
        if (card.innerHTML === "R") {
            card.style.backgroundColor = "pink";
        } else if (card.innerHTML === "B") {
            card.style.backgroundColor = "gray";
        }
    }

    // Get player names and game date from the API
    const urlParams = new URLSearchParams(window.location.search);
    const gameDate = urlParams.get('d');
    const playerOneName = urlParams.get('p1');
    const playerTwoName = urlParams.get('p2');

    // Update the HTML with the retrieved names and date
    document.getElementById('playerOneName').innerHTML = playerOneName;
    document.getElementById('headerPlayerOne').innerHTML = playerOneName;
    document.getElementById('tablePlayerOneCard').innerHTML = playerOneName;
    document.getElementById('tablePlayerOnePoints').innerHTML = playerOneName;

    document.getElementById('playerTwoName').innerHTML = playerTwoName;
    document.getElementById('headerPlayerTwo').innerHTML = playerTwoName;
    document.getElementById('tablePlayerTwoCard').innerHTML = playerTwoName;
    document.getElementById('tablePlayerTwoPoints').innerHTML = playerTwoName;
    document.getElementById('gameDate').innerHTML = gameDate;
}

async function displayInfoWheatSteel() {
    // Get game date and team name from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const gameDate = urlParams.get('date');
    const gameTeam = decodeURI(urlParams.get('t')); // Decode URI for spaces

    document.getElementById('gameDate').innerHTML = gameDate;
    document.getElementById('teamName').innerHTML = gameTeam;

    // Fetch round data from the API
    try {
        const response = await fetch(`/api/wheat-steel-data?date=${gameDate}&team=${encodeURIComponent(gameTeam)}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();

        // Update HTML with round data for Wheat
        for (let i = 1; i <= 5; i++) {
            document.getElementById(`roundOneWheat`).innerHTML = data[`round${i}Wheat`];
            document.getElementById(`roundOneWheatTrade`).innerHTML = data[`round${i}WheatTrade`];
            document.getElementById(`roundOneWheatConsume`).innerHTML = data[`round${i}WheatConsume`];
        }

        // Update HTML with round data for Steel
        for (let i = 1; i <= 5; i++) {
            document.getElementById(`roundOneSteel`).innerHTML = data[`round${i}Steel`];
            document.getElementById(`roundOneSteelTrade`).innerHTML = data[`round${i}SteelTrade`];
            document.getElementById(`roundOneSteelConsume`).innerHTML = data[`round${i}SteelConsume`];
        }
    } catch (error) {
        console.error("Error fetching Wheat and Steel data:", error);
    }
}
