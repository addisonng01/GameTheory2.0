async function displayInfoRedBlack() {
    try {
        // Extract date from the URL
        let urlFull = window.location.href; // full URL
        let dateIndex = urlFull.search("date="); // index of how much to cut off from front
        let urlDate = urlFull.slice(dateIndex + 5); // slice out dateIndex variable, adding length of "date="

        // Fetch game data from the API
        const response = await fetch(`/api/red-black-game?date=${encodeURIComponent(urlDate)}`);
        const gameData = await response.json();

        // Update HTML elements with fetched data
        document.getElementById('gameDate_1').innerHTML = urlDate;
        document.getElementById('playerNum_1').innerHTML = gameData.players.length; // Assuming players is an array
        document.getElementById('pairNum_1').innerHTML = gameData.pairs.length; // Assuming pairs is an array
        document.getElementById('roundNum_1').innerHTML = gameData.rounds.length; // Assuming rounds is an array

        document.getElementById('blackTotal').innerHTML = gameData.blackCardsTotal;
        document.getElementById('blackAverage').innerHTML = (gameData.blackCardsTotal / gameData.rounds.length).toFixed(2);
        document.getElementById('redTotal').innerHTML = gameData.redCardsTotal;
        document.getElementById('redAverage').innerHTML = (gameData.redCardsTotal / gameData.rounds.length).toFixed(2);

        document.getElementById('highestName').innerHTML = gameData.highestScorePlayer.name;
        document.getElementById('highestScore').innerHTML = gameData.highestScorePlayer.score;
        document.getElementById('lowestName').innerHTML = gameData.lowestScorePlayer.name;
        document.getElementById('lowestScore').innerHTML = gameData.lowestScorePlayer.score;

        document.getElementById('highestBlackName').innerHTML = gameData.highestBlackCardsPlayer.name;
        document.getElementById('highestBlackScore').innerHTML = gameData.highestBlackCardsPlayer.count;
        document.getElementById('lowestBlackName').innerHTML = gameData.lowestBlackCardsPlayer.name;
        document.getElementById('lowestBlackScore').innerHTML = gameData.lowestBlackCardsPlayer.count;

        document.getElementById('highestRedName').innerHTML = gameData.highestRedCardsPlayer.name;
        document.getElementById('highestRedScore').innerHTML = gameData.highestRedCardsPlayer.count;
        document.getElementById('lowestRedName').innerHTML = gameData.lowestRedCardsPlayer.name;
        document.getElementById('lowestRedScore').innerHTML = gameData.lowestRedCardsPlayer.count;

        // Update player links
        let teamInfoDiv = document.getElementById("teamInfo");
        teamInfoDiv.innerHTML += gameData.pairs.map(pair =>
            `<a href="redBlackPairView.html?d=${encodeURIComponent(urlDate)}&p1=${encodeURIComponent(pair.playerOne)}&p2=${encodeURIComponent(pair.playerTwo)}">${pair.playerOne} & ${pair.playerTwo}</a>`
        ).join(', ');

        // Adjust links to ensure they contain the right date
        let allLinks = document.getElementsByTagName("a");
        for (let link of allLinks) {
            if (link.href.includes("redBlackPairView.html")) {
                link.href = link.href.replace(/d=[^&]*/, `d=${encodeURIComponent(urlDate)}`);
            }
        }

    } catch (error) {
        console.error("Error fetching game info:", error);
    }
}