document.addEventListener("DOMContentLoaded", () => {
    let period = 1;
    let totalTime = 80; // Default total hours
    let totalWheatConsumed = 0;
    let totalSteelConsumed = 0;
    let wheatTime = 1; // Default production time for wheat
    let steelTime = 2; // Default production time for steel
    let wheatGoal = 200; // Default consumption goal for wheat
    let steelGoal = 150; // Default consumption goal for steel
    let gameId;
    let teamId;
    let timeRemaining = 80;
    let resourcesChanged = false;
    const maxPeriods = 5;

    // Initialize event listeners
    document.getElementById('startButton').onclick = handleStartButton;
    document.getElementById('tradeWheatForSteel').onclick = () => initiateTrade('wheat', 'steel', 10);
    document.getElementById('tradeSteelForWheat').onclick = () => initiateTrade('steel', 'wheat', 10);
    document.getElementById("produceButton").onclick = handleProduceButton;
    document.getElementById("nextPeriodButton").onclick = handleNextPeriodButton;
    document.getElementById('wheatAmount').onchange = updateTimeSpent;
    document.getElementById('steelAmount').onchange = updateTimeSpent;

    // Handle the Start button
    async function handleStartButton() {
        const teamData = document.getElementById('teamSelect').value.split(',');
        [totalTime, wheatTime, steelTime, wheatGoal, steelGoal] = teamData.map(Number);
        
        document.getElementById('teamSelect').disabled = true;
        updateGameUI();
    }

    // Update the UI with game settings
    function updateGameUI() {
        document.getElementById('timeRemaining').innerText = totalTime;
        document.getElementById('wheatGoal').innerText = wheatGoal;
        document.getElementById('steelGoal').innerText = steelGoal;
        document.getElementById('gameSection').style.display = 'block';
        document.getElementById('startButton').style.display = 'none';
    }

    // Handle trade actions
    async function initiateTrade(giveResource, receiveResource, amount) {
        if (giveResource === 'wheat' && totalWheatConsumed < amount) {
            alert("Not enough wheat to trade!");
            return;
        }
        if (giveResource === 'steel' && totalSteelConsumed < amount) {
            alert("Not enough steel to trade!");
            return;
        }

        if (giveResource === 'wheat') {
            totalWheatConsumed -= amount;
            totalSteelConsumed += amount;
        } else {
            totalSteelConsumed -= amount;
            totalWheatConsumed += amount;
        }

        updateResourcesDisplay();
        await syncTradeWithBackend(giveResource, receiveResource, amount);
    }

    // Sync trade with the backend
    async function syncTradeWithBackend(giveResource, receiveResource, amount) {
        await fetch(`/api/wheatSteel/${gameId}/trade`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ teamId, trade: { give: giveResource, receive: receiveResource, amount } })
        });
    }

    // Update the displayed resources
    function updateResourcesDisplay() {
        document.getElementById('wheatConsumed').innerText = totalWheatConsumed;
        document.getElementById('steelConsumed').innerText = totalSteelConsumed;
    }

    // Update the time spent based on input values
    function updateTimeSpent() {
        const wheatAmount = parseInt(document.getElementById('wheatAmount').value) || 0;
        const steelAmount = parseInt(document.getElementById('steelAmount').value) || 0;

        const timeSpent = (wheatAmount * wheatTime) + (steelAmount * steelTime);
        document.getElementById('timeSpent').innerText = timeSpent;

        if (timeSpent > timeRemaining) {
            document.getElementById('produceButton').disabled = true;
            document.getElementById('errorMessage').innerText = "You cannot produce more than the available time remaining.";
        } else {
            document.getElementById('produceButton').disabled = false;
            document.getElementById('errorMessage').innerText = "";
        }
    }

    // Handle the "Produce" button
    function handleProduceButton() {
        const wheatAmount = parseInt(document.getElementById('wheatAmount').value) || 0;
        const steelAmount = parseInt(document.getElementById('steelAmount').value) || 0;
        const timeSpent = (wheatAmount * wheatTime) + (steelAmount * steelTime);

        if (wheatAmount > totalTime || steelAmount > totalTime) {
            alert("You cannot produce more than the available time remaining.");
            return;
        }

        totalWheatConsumed += wheatAmount;
        totalSteelConsumed += steelAmount;
        timeRemaining -= timeSpent;

        if (totalWheatConsumed > 1 || totalSteelConsumed > 1) resourcesChanged = true;
        if (isNaN(timeRemaining) || timeRemaining < 0) timeRemaining = 0;

        updateResourcesDisplay();
        document.getElementById('timeRemaining').innerText = timeRemaining;

        resetInputFields();
        alert("Production complete. Prepare for the next period.");
    }

    // Reset the input fields after production
    function resetInputFields() {
        document.getElementById('wheatAmount').value = 0;
        document.getElementById('steelAmount').value = 0;
        document.getElementById('timeSpent').innerText = 0;
    }

    // Handle the "Next Period" button
    function handleNextPeriodButton() {
        if (timeRemaining <= 0 && resourcesChanged && period < maxPeriods) {
            period++;
            alert(`Welcome to Period ${period}. Time has been reset to ${totalTime} hours.`);
            resetPeriod();
        } else if (timeRemaining > 0) {
            alert("You cannot proceed to the next period without spending all remaining hours.");
        } else {
            alert("The game is over! Final scores will be calculated.");
            calculatePoints();
            stopGame();
            document.getElementById('nextPeriodButton').innerText = "Results";
        }
    }

    // Reset period settings
    function resetPeriod() {
        totalTime = parseInt(document.getElementById('teamSelect').value.split(',')[0]);
        updateGameUI();
        updateTimeSpent();
    }

    // Calculate final points
    function calculatePoints() {
        let points = 5;

        if (totalWheatConsumed >= wheatGoal && totalSteelConsumed >= steelGoal) {
            points = 10;
            const excessWheat = totalWheatConsumed - wheatGoal;
            const excessSteel = totalSteelConsumed - steelGoal;
            if (excessWheat + excessSteel > 0) points += 10;
        }

        alert(`Game Over! You scored ${points} points.`);
        saveScore(points);
    }

    // Save final score to the database
    async function saveScore(points) {
        await fetch(`/api/wheatSteel/${gameId}/score`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ score: points })
        });
    }

    // Stop the game
    function stopGame() {
        document.getElementById('produceButton').disabled = true;
        document.getElementById('nextPeriodButton').disabled = true;
    }
});
