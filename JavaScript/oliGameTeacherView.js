document.addEventListener("DOMContentLoaded", () => {
    const totalRounds = 12;
    let currentRound = 1;
    const summaryListEl = document.getElementById("summaryList");
    const nextRoundButton = document.getElementById("nextRound");

    // Fetch and display student submissions
    const fetchSubmissions = async () => {
        try {
            const response = await fetch(`/api/oligopoly/submissions?round=${currentRound}`);
            const data = await response.json();

            // Display results
            const resultItem = document.createElement("li");
            resultItem.innerHTML = `
                <strong>Round ${currentRound}</strong>: 
                Decisions = [${data.decisions.join(", ")}]
            `;
            summaryListEl.appendChild(resultItem);
        } catch (error) {
            console.error("Error fetching submissions:", error);
        }
    };

    // Approve the next round
    const approveNextRound = async () => {
        try {
            await fetch("/api/oligopoly/approve", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ round: currentRound }),
            });

            currentRound++;
            if (currentRound > totalRounds) {
                nextRoundButton.textContent = "Finish";
                window.location.href = ""; // Adjust to the finish page URL as needed
            }
        } catch (error) {
            console.error("Error approving the next round:", error);
        }
    };

    // Fetch submissions initially and on button click
    nextRoundButton.addEventListener("click", async () => {
        await fetchSubmissions();
        await approveNextRound();
    });
});
