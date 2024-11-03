document.addEventListener("DOMContentLoaded", () => {
    let period = 1;
    let totalTime = 80; // Default total hours
    let totalWheatConsumed = 0;
    let totalSteelConsumed = 0;
    let wheatTime = 1; // Default production time for wheat
    let steelTime = 2; // Default production time for steel
    let wheatGoal = 200; // Default consumption goal for wheat
    let steelGoal = 150; // Default consumption goal for steel

    // Start Game Button
    document.getElementById('startButton').onclick = function() {
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

    // Update Time Spent
    function updateTimeSpent() {
        const wheatAmount = parseInt(document.getElementById('wheatAmount').value);
        const steelAmount = parseInt(document.getElementById('steelAmount').value);
        const timeSpent = (wheatAmount * wheatTime) + (steelAmount * steelTime);
        document.getElementById('timeSpent').innerText = timeSpent;
    }

    // Wheat and Steel Production
    document.getElementById('produceButton').onclick = function() {
        const wheatAmount = parseInt(document.getElementById('wheatAmount').value);
        const steelAmount = parseInt(document.getElementById('steelAmount').value);

        // Calculate time spent and consumption
        const timeSpent = (wheatAmount * wheatTime) + (steelAmount * steelTime);
        if (timeSpent > totalTime) {
            alert("Not enough time! You have " + totalTime + " hours.");
            return;
        }

        // Update consumption
        totalWheatConsumed += wheatAmount;
        totalSteelConsumed += steelAmount;
        totalTime -= timeSpent;

        // Update displayed values
        document.getElementById('wheatConsumed').innerText = totalWheatConsumed;
        document.getElementById('steelConsumed').innerText = totalSteelConsumed;
        document.getElementById('timeRemaining').innerText = totalTime;

        // Reset input fields
        document.getElementById('wheatAmount').value = 0;
        document.getElementById('steelAmount').value = 0;
        updateTimeSpent(); // Reset the time spent display
    };

    // Next Period Button
    document.getElementById('nextPeriodButton').onclick = function() {
        if (totalTime < parseInt(document.getElementById('teamSelect').value.split(',')[0])) {
            if (period < 5) {
                period++;
                document.getElementById('periodCounter').innerText = period;
                resetPeriod();
            } else {
                calculatePoints();
            }
        } else {
            alert("You must produce in this period before moving to the next.");
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
    }

    // Attach input change event for dynamic time update
    document.getElementById('wheatAmount').onchange = updateTimeSpent;
    document.getElementById('steelAmount').onchange = updateTimeSpent;
});
