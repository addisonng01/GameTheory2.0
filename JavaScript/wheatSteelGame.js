document.addEventListener("DOMContentLoaded", () => {
    let period = 1;
    let totalTime = 80; // Default total hours
    let totalWheatConsumed = 0;
    let totalSteelConsumed = 0;
    let wheatTime = 1; // Default production time for wheat
    let steelTime = 2; // Default production time for steel
    let wheatGoal = 200; // Default consumption goal for wheat
    let steelGoal = 150; // Default consumption goal for steel
    let gameId; // Store the game ID after creation
    let teamId; // Store the team ID after creation

    // Start Game Button
    document.getElementById('startButton').onclick = async function() {
        const teamData = document.getElementById('teamSelect').value.split(',');
        totalTime = parseInt(teamData[0]);
        wheatTime = parseInt(teamData[1]);
        steelTime = parseInt(teamData[2]);
        wheatGoal = parseInt(teamData[3]);
        steelGoal = parseInt(teamData[4]);

        // Disable the team selection dropdown
        document.getElementById('teamSelect').disabled = true;

        document.getElementById('timeRemaining').innerText = totalTime;
        document.getElementById('wheatGoal').innerText = wheatGoal;
        document.getElementById('steelGoal').innerText = steelGoal;
        document.getElementById('gameSection').style.display = 'block';
        document.getElementById('startButton').style.display = 'none';
    };

    // Trade Buttons
    document.getElementById('tradeWheatForSteel').onclick = () => initiateTrade('wheat', 'steel', 10);
    document.getElementById('tradeSteelForWheat').onclick = () => initiateTrade('steel', 'wheat', 10);

    // Function to handle trades
    async function initiateTrade(giveResource, receiveResource, amount) {
        if (giveResource === 'wheat' && totalWheatConsumed < amount) {
            alert("Not enough wheat to trade!");
            return;
        }
        if (giveResource === 'steel' && totalSteelConsumed < amount) {
            alert("Not enough steel to trade!");
            return;
        }

        // Adjust resources locally
        if (giveResource === 'wheat') {
            totalWheatConsumed -= amount;
            totalSteelConsumed += amount;  // Received steel
        } else {
            totalSteelConsumed -= amount;
            totalWheatConsumed += amount;  // Received wheat
        }

        // Update the displayed values
        document.getElementById('wheatConsumed').innerText = totalWheatConsumed;
        document.getElementById('steelConsumed').innerText = totalSteelConsumed;

        // Sync the trade with the backend
        await fetch(`/api/wheatSteel/${gameId}/trade`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                teamId: teamId,
                trade: {
                    give: giveResource,
                    receive: receiveResource,
                    amount: amount
                }
            })
        });
    }

    // Update Time Spent
    function updateTimeSpent() {
        const wheatAmount = parseInt(document.getElementById('wheatAmount').value);
        const steelAmount = parseInt(document.getElementById('steelAmount').value);
        const timeSpent = (wheatAmount * wheatTime) + (steelAmount * steelTime);
        document.getElementById('timeSpent').innerText = timeSpent;
    }

    // Production and period code here...
    // Attach input change event for dynamic time update
    document.getElementById('wheatAmount').onchange = updateTimeSpent;
    document.getElementById('steelAmount').onchange = updateTimeSpent;


    //Poll page updates
    function pollUpdates() {
        fetch(`/api/wheatSteel/${gameId}/updates`)
            .then(response => response.json())
            .then(data => {
                data.updates.forEach(update => {
                    //any other functions that require updates can be added here
                    //trade request updates
                    if (update.type === 'tradeRequest') {
                        handleTradeRequest(update);
                    } else if (update.type === 'tradeAccepted') {
                        alert(`Trade accepted by Team ${update.byTeamId}`);
                        updateResources(update.tradeWheat, update.tradeSteel);
                    }
                });
            })
            .catch(error => console.error('Polling error:', error));
    }

    // Start polling every 5 seconds
    setInterval(pollUpdates, 5000);
    
    // Trade functionality. change if needed
    document.getElementById('tradeButton').onclick = async function() {
        const tradeWheat = parseInt(document.getElementById('tradeWheat').value);
        const tradeSteel = parseInt(document.getElementById('tradeSteel').value);
        const tradeTeamId = document.getElementById('tradeTeamSelect').value;

        if (tradeWheat > totalWheatConsumed || tradeSteel > totalSteelConsumed) {
            alert("Insufficient resources for this trade.");
            return;
        }

        const response = await fetch(`/api/wheatSteel/${gameId}/trade`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                tradeTeamId,
                tradeWheat,
                tradeSteel
            })
        });
        const data = await response.json();

        // Update local resources if trade is successful
        if (data.success) {
            totalWheatConsumed -= tradeWheat;
            totalSteelConsumed -= tradeSteel;
            document.getElementById('wheatConsumed').innerText = totalWheatConsumed;
            document.getElementById('steelConsumed').innerText = totalSteelConsumed;
            alert("Trade successful!");
        } else {
            alert("Trade failed.");
        }
    };

    // Reset Period
    function resetPeriod() {
        totalTime = parseInt(document.getElementById('teamSelect').value.split(',')[0]);
        document.getElementById('timeRemaining').innerText = totalTime;
        updateTimeSpent(); // Reset time spent display
    }

    // Calculate Points
    function calculatePoints() {
        let points = 5;

        if (totalWheatConsumed >= wheatGoal && totalSteelConsumed >= steelGoal) {
            points = 10; // Base points for meeting goals
            const excessWheat = totalWheatConsumed - wheatGoal;
            const excessSteel = totalSteelConsumed - steelGoal;
            const totalExcess = excessWheat + excessSteel;

            if (totalExcess > 0) {
                points += 10; // Bonus points for exceeding goals
            }
        }

        alert(`Game Over! You scored ${points} points.`);    
        // Save final score to the database
        fetch(`/api/wheatSteel/${gameId}/score`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ score: points })
        });
    }

    // Attach input change event for dynamic time update
    document.getElementById('wheatAmount').onchange = updateTimeSpent;
    document.getElementById('steelAmount').onchange = updateTimeSpent;


    //Poll page updates
    function pollUpdates() {
        fetch(`/api/wheatSteel/${gameId}/updates`)
            .then(response => response.json())
            .then(data => {
                data.updates.forEach(update => {
                    //any other functions that require updates can be added here
                    //trade request updates
                    if (update.type === 'tradeRequest') {
                        handleTradeRequest(update);
                    } else if (update.type === 'tradeAccepted') {
                        alert(`Trade accepted by Team ${update.byTeamId}`);
                        updateResources(update.tradeWheat, update.tradeSteel);
                    }
                });
            })
            .catch(error => console.error('Polling error:', error));
    }

    // Start polling every 5 seconds
    setInterval(pollUpdates, 5000);
    
    // Trade functionality. change if needed
    document.getElementById('tradeButton').onclick = async function() {
        const tradeWheat = parseInt(document.getElementById('tradeWheat').value);
        const tradeSteel = parseInt(document.getElementById('tradeSteel').value);
        const tradeTeamId = document.getElementById('tradeTeamSelect').value;

        if (tradeWheat > totalWheatConsumed || tradeSteel > totalSteelConsumed) {
            alert("Insufficient resources for this trade.");
            return;
        }

        const response = await fetch(`/api/wheatSteel/${gameId}/trade`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                tradeTeamId,
                tradeWheat,
                tradeSteel
            })
        });
        const data = await response.json();

        // Update local resources if trade is successful
        if (data.success) {
            totalWheatConsumed -= tradeWheat;
            totalSteelConsumed -= tradeSteel;
            document.getElementById('wheatConsumed').innerText = totalWheatConsumed;
            document.getElementById('steelConsumed').innerText = totalSteelConsumed;
            alert("Trade successful!");
        } else {
            alert("Trade failed.");
        }
    };
});
