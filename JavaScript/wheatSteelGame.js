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

    //TODO: add flexible period amount as a game setting for the teacher side
    const maxPeriods = 5; //Change length of game accordingly

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

    // Function to update the time spent based on input values
    function updateTimeSpent() {
        wheatAmount = parseInt(document.getElementById('wheatAmount').value) || 0;  // Get wheat amount
        steelAmount = parseInt(document.getElementById('steelAmount').value) || 0;  // Get steel amount
        
        const timeSpent = (wheatAmount * wheatTime) + (steelAmount * steelTime); // Calculate total time spent
        document.getElementById('timeSpent').innerText = timeSpent; // Update time spent display
        
        // Validate if the total time spent exceeds the remaining time
        if (timeSpent > timeRemaining) {
            document.getElementById('produceButton').disabled = true; // Disable "Produce" button if over the time
            document.getElementById('errorMessage').innerText = "You cannot produce more than the available time remaining.";
        } else {
            document.getElementById('produceButton').disabled = false; // Enable the button if within time limits
            document.getElementById('errorMessage').innerText = ""; // Clear error message
        }
    }

    // Production and period code here...
    // TODO: Production currently has no limits, so you can trade past the allotted hours
    // ex. Use 100 hours of wheat and production will still go through, the time remaining will become NaN
    // Function to handle the "Produce" button
    document.getElementById("produceButton").onclick = () => {
        wheatAmount = parseInt(document.getElementById('wheatAmount').value) || 0;  // Get wheat amount
        steelAmount = parseInt(document.getElementById('steelAmount').value) || 0;  // Get steel amount
        
        const timeSpent = (wheatAmount * wheatTime) + (steelAmount * steelTime); // Calculate total time spent
        document.getElementById('timeSpent').innerText = timeSpent; // Update time spent display

        // Check if amount produce exceeds hours
        if(wheatAmount > totalTime || steelAmount > totalTime){
            alert("You cannot produce more than the available time remaining.");
            return;
        }
    
        // Update totals
        totalWheatConsumed += wheatAmount;
        totalSteelConsumed += steelAmount;
        timeRemaining -= timeSpent;
    
        // Ensure timeRemaining is not NaN or negative
        if (isNaN(timeRemaining) || timeRemaining < 0) {
            timeRemaining = 0; // Set timeRemaining to 0 if it's invalid or negative
            document.getElementById('timeRemaining').innerText = timeRemaining;
        }

        // Update displayed values
        document.getElementById('wheatConsumed').innerText = totalWheatConsumed;
        document.getElementById('steelConsumed').innerText = totalSteelConsumed;
        document.getElementById('timeRemaining').innerText = timeRemaining;
    
        // Reset inputs after production
        document.getElementById('wheatAmount').value = 0;
        document.getElementById('steelAmount').value = 0;
        document.getElementById('timeSpent').innerText = 0;
    
        alert("Production complete. Prepare for the next period.");
    };

    // Function to handle the "Next Period" button
    document.getElementById("nextPeriodButton").onclick = () => {
        // Proceed to the next period
        if(period >= maxPeriods || timeRemaining <= 0){
            periodCounter = period++;
            //resetPeriod();
        }else{
            alert("You cannot proceed to the next period without spending all remaining hours.");
        }
        // Update period and time displayed on the UI
        document.getElementById('periodNumber').innerText = period;
        document.getElementById('timeRemaining').innerText = timeRemaining;

        alert(`Welcome to Period ${period}. Time has been reset to ${totalTime}.`);
        if (period > 5) {
            alert("The game is over! Final scores will be calculated.");
            calculatePoints();
            stopGame();
            return;
        }
    };

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
    
    // Trade functionality
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
        //TODO: upload to the correct database
        fetch(`/api/wheatSteel/${gameId}/score`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ score: points })
        });
    }

    function stopGame() {
        document.getElementById('produceButton').disabled = true;
        document.getElementById('nextPeriodButton').disabled = true;
    }

    // Attach input change event for dynamic time update
    document.getElementById('wheatAmount').onchange = updateTimeSpent;
    document.getElementById('steelAmount').onchange = updateTimeSpent;
});
