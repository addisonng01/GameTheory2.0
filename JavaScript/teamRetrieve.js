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
    const gameSessionId = urlParams.get('sessionId'); // Get game session ID from URL

    try {
        // Fetch game session data for the current student
        const response = await fetch(`/api/red-black-game-data?sessionId=${gameSessionId}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const gameData = await response.json();
        const playerOneName = gameData.playerOneName;
        const playerTwoName = gameData.playerTwoName;
        const gameDate = gameData.gameDate;

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
    } catch (error) {
        console.error("Error fetching Red Black game data:", error);
    }
}


async function displayInfoWheatSteel() {
    // Get game date and team name from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const gameSessionId = urlParams.get('sessionId'); // Get game session ID from URL

    try {
        // Fetch game data for Wheat and Steel from the API for the current session
        const response = await fetch(`/api/wheat-steel-game-data?sessionId=${gameSessionId}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Update game date and team name
        document.getElementById('gameDate').innerHTML = data.gameDate;
        document.getElementById('teamName').innerHTML = data.teamName;

        // Update round data for Wheat
        for (let i = 1; i <= 5; i++) {
            document.getElementById(`round${i}Wheat`).innerHTML = data[`round${i}Wheat`];
            document.getElementById(`round${i}WheatTrade`).innerHTML = data[`round${i}WheatTrade`];
            document.getElementById(`round${i}WheatConsume`).innerHTML = data[`round${i}WheatConsume`];
        }

        // Update round data for Steel
        for (let i = 1; i <= 5; i++) {
            document.getElementById(`round${i}Steel`).innerHTML = data[`round${i}Steel`];
            document.getElementById(`round${i}SteelTrade`).innerHTML = data[`round${i}SteelTrade`];
            document.getElementById(`round${i}SteelConsume`).innerHTML = data[`round${i}SteelConsume`];
        }
    } catch (error) {
        console.error("Error fetching Wheat and Steel game data:", error);
    }
}

