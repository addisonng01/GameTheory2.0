document.addEventListener("DOMContentLoaded", async () => {
    const totalRounds = 12;
    const firms = 4;
    let currentRound = 1;
    let firmDecisions = Array(firms).fill(0); // Stores decisions for each firm
    let totalOutput = 0;
    let marketPrice = 0;
    let profits = Array(firms).fill(0); // Stores profits for each firm
    let turnIndex = 0; // Tracks which firm's turn it is (for last 4 rounds)
    const sessionId = "your-session-id"; // Replace with actual session ID

    // DOM Elements
    const roundNumberEl = document.getElementById("roundNumber");
    const feedbackEl = document.getElementById("feedback");
    const resultsListEl = document.getElementById("roundResults");
    const firmInputs = Array.from(document.querySelectorAll(".firm-input"));
    const submitButton = document.getElementById("submitRound");

    // Fetch current game progress
    const fetchGameProgress = async () => {
        try {
            const response = await fetch(`/api/oil-game-progress?sessionId=${sessionId}`);
            if (!response.ok) {
                throw new Error(`Error fetching game progress: ${response.statusText}`);
            }
            const { currentRound: fetchedRound, firmDecisions: fetchedDecisions } = await response.json();
            currentRound = fetchedRound || currentRound;
            firmDecisions = fetchedDecisions || firmDecisions;
            roundNumberEl.innerText = currentRound;
        } catch (error) {
            console.error("Error fetching game progress:", error);
        }
    };

    // Hide results for first 8 rounds
    const hideResults = (hide) => {
        feedbackEl.style.display = hide ? "none" : "block";
        resultsListEl.style.display = hide ? "none" : "block";
    };

    // Hide a specific firm input
    const hideFirmInput = (index) => {
        const input = firmInputs[index];
        input.style.visibility = "hidden";
        input.disabled = true;
    };

    // Initialize round
    const startRound = () => {
        firmDecisions.fill(0);
        totalOutput = 0;
        marketPrice = 0;
        profits.fill(0);
        turnIndex = 0;

        firmInputs.forEach((input) => {
            input.value = "";
            input.disabled = currentRound > 8; // Disable all inputs for last 4 rounds initially
            input.style.visibility = "visible"; // Make input visible at the start of the round
        });

        submitButton.innerText = currentRound > 8 ? "Submit Firm Decision" : "Submit Round";
        hideResults(true); // Hide results initially
    };

    // Calculate market price and profits
    const calculateResults = () => {
        totalOutput = firmDecisions.reduce((sum, output) => sum + output, 0);
        marketPrice = Math.max(100 - totalOutput, 0); // Example price formula
        profits = firmDecisions.map((q) => (marketPrice - 20) * q);
    };

    // Display round results
    const displayResults = () => {
        hideResults(false); // Show results
        resultsListEl.innerHTML = `
            <li>Total Output: ${totalOutput}</li>
            <li>Market Price: $${marketPrice}</li>
            ${profits.map((profit, index) => `<li>Firm ${index + 1} Profit: $${profit}</li>`).join("")}
        `;
    };

    // Submit button handler
    submitButton.addEventListener("click", async () => {
        if (currentRound <= 8) {
            // First 8 rounds: Collect all inputs at once
            firmInputs.forEach((input, index) => {
                firmDecisions[index] = parseInt(input.value) || 0;
                hideFirmInput(index); // Hide each firm's input after entry
            });
            await submitRoundData();
            displayResults();
            currentRound++;
        } else {
            // Last 4 rounds: Sequential decision-making
            firmDecisions[turnIndex] = parseInt(firmInputs[turnIndex].value) || 0;
            hideFirmInput(turnIndex); // Hide the active firm's input after entry
            turnIndex++;

            if (turnIndex >= firms) {
                await submitRoundData();
                displayResults();
                currentRound++;
            } else {
                firmInputs[turnIndex].disabled = false; // Enable next firm's input
                firmInputs[turnIndex].style.visibility = "visible"; // Show the active firm's input
            }
        }

        // Update round number
        if (currentRound <= totalRounds) {
            roundNumberEl.innerText = currentRound;
            if (currentRound > 8 && turnIndex < firms) {
                feedbackEl.innerHTML = `Firm ${turnIndex + 1}, it's your turn to decide.`;
            }
        } else {
            feedbackEl.innerHTML = "Game Over! Review the results.";
            setTimeout(() => {
                window.location.href = "https://cssgametheory.com/CSSGametheory/HTML/admin/index.html";
            }, 3000); // Wait for 3 seconds
        }
    });

    // Submit round data to backend
    const submitRoundData = async () => {
        try {
            await fetch('/api/oil-game-round', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ currentRound, firmDecisions, sessionId })
            });
        } catch (error) {
            console.error("Error submitting round data:", error);
        }
    };

    await fetchGameProgress();
    startRound();
});
