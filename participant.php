<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buzzer App - Participant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        input {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            margin: 10px 0;
        }
        button:hover {
            background-color: #45a049;
        }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .buzz-btn {
            font-size: 72px;
            height: 200px;
            margin: 30px 0;
            background: linear-gradient(145deg, #ff6b6b, #ee5a6f);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .buzz-btn:hover:not(:disabled) {
            background: linear-gradient(145deg, #ff5252, #e91e63);
            transform: scale(1.05);
        }
        .buzz-btn:disabled {
            background: #ccc;
        }
        #status {
            text-align: center;
            color: #666;
            margin: 20px 0;
            font-size: 18px;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: #f44336;
        }
        .leaderboard {
            margin-top: 20px;
            background: #f0f7ff;
            padding: 15px;
            border-radius: 10px;
        }
        .leaderboard h2 {
            margin-top: 0;
            color: #1976d2;
            font-size: 20px;
            text-align: center;
        }
        .leaderboard-item {
            padding: 10px;
            margin: 5px 0;
            background: white;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .leaderboard-rank {
            font-weight: bold;
            color: #666;
            margin-right: 10px;
        }
        .leaderboard-name {
            flex: 1;
        }
        .leaderboard-score {
            font-weight: bold;
            color: #4CAF50;
            font-size: 18px;
        }
        .current-user {
            background: #e8f5e9;
            border: 2px solid #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>?? Buzzer App - Participant</h1>
        
        <div id="joinView">
            <input type="text" id="nameInput" placeholder="Enter your name" />
            <input type="text" id="sessionKeyInput" placeholder="Enter session key" maxlength="6" style="text-transform: uppercase;" />
            <button onclick="joinSession()">Join Session</button>
            <div id="status"></div>
        </div>

        <div id="buzzerView" style="display: none;">
            <div id="status">Waiting to buzz...</div>
            <button class="buzz-btn" id="buzzBtn" onclick="buzz()">??</button>
            <input type="text" id="answerInput" placeholder="Enter your answer (optional)" />
            
            <div class="leaderboard">
                <h2>?? Leaderboard</h2>
                <div id="leaderboardList"></div>
            </div>
        </div>
    </div>

    <script>
        let currentSessionKey;
        let participantId;
        let participantName;
        let hasBuzzed = false;
        let lastUpdate = 0;
        let polling = false;

        document.getElementById('sessionKeyInput').addEventListener('input', (e) => {
            e.target.value = e.target.value.toUpperCase();
        });

        async function joinSession() {
            const name = document.getElementById('nameInput').value.trim();
            const sessionKey = document.getElementById('sessionKeyInput').value.trim();

            if (!name) {
                showStatus("Please enter your name", "error");
                return;
            }

            if (!sessionKey || sessionKey.length !== 6) {
                showStatus("Please enter a valid 6-character session key", "error");
                return;
            }

            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=joinSession&sessionKey=${sessionKey}&participantName=${encodeURIComponent(name)}`
                });
                
                const data = await response.json();

                if (data.success) {
                    currentSessionKey = sessionKey;
                    participantId = data.participantId;
                    participantName = name;
                    
                    document.getElementById('joinView').style.display = 'none';
                    document.getElementById('buzzerView').style.display = 'block';
                    showStatus(`Connected to session ${sessionKey}`, "success");
                    
                    startPolling();
                } else {
                    showStatus(data.error || "Session not found or inactive", "error");
                }
            } catch (error) {
                showStatus("Failed to join session: " + error.message, "error");
            }
        }

        function startPolling() {
            if (polling) return;
            polling = true;
            poll();
        }

        async function poll() {
            if (!polling) return;
            
            try {
                const response = await fetch(`api.php?action=pollUpdates&sessionKey=${currentSessionKey}&lastUpdate=${lastUpdate}&participantId=${participantId}`);
                const data = await response.json();
                
                if (data.success && data.session) {
                    if (!data.session.isActive) {
                        showStatus("Session ended by host", "error");
                        polling = false;
                        setTimeout(() => location.reload(), 2000);
                        return;
                    }
                    
                    lastUpdate = data.lastUpdate;
                    
                    // Check if we buzzed
                    if (data.session.participants[participantId] && data.session.participants[participantId].buzzedAt && !hasBuzzed) {
                        hasBuzzed = true;
                        document.getElementById('buzzBtn').disabled = true;
                        showStatus("You buzzed! ??", "success");
                    }
                    
                    // Check if buzzers were reset
                    if (data.session.participants[participantId] && !data.session.participants[participantId].buzzedAt && hasBuzzed) {
                        hasBuzzed = false;
                        document.getElementById('buzzBtn').disabled = false;
                        document.getElementById('answerInput').value = '';
                        showStatus("Buzzers reset! Get ready...", "");
                    }
                    
                    updateLeaderboard(data.session.participants, participantName);
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
            
            // Continue polling
            if (polling) {
                setTimeout(poll, 100);
            }
        }

        async function buzz() {
            if (!hasBuzzed) {
                const answer = document.getElementById('answerInput').value.trim();
                
                try {
                    await fetch('api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=buzz&sessionKey=${currentSessionKey}&participantId=${participantId}&answer=${encodeURIComponent(answer)}`
                    });
                } catch (error) {
                    console.error('Buzz error:', error);
                }
            }
        }

        function updateLeaderboard(participants, currentUserName) {
            if (!participants) return;
            
            const leaderboardList = document.getElementById('leaderboardList');
            leaderboardList.innerHTML = '';
            
            const participantsArray = Object.values(participants);
            const sorted = [...participantsArray].sort((a, b) => b.score - a.score);
            
            sorted.forEach((participant, index) => {
                const div = document.createElement('div');
                div.className = 'leaderboard-item' + (participant.name === currentUserName ? ' current-user' : '');
                div.innerHTML = `
                    <span><span class="leaderboard-rank">#${index + 1}</span><span class="leaderboard-name">${escapeHtml(participant.name)}</span></span>
                    <span class="leaderboard-score">${participant.score}</span>
                `;
                leaderboardList.appendChild(div);
            });
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        function showStatus(message, className) {
            const status = document.getElementById('status');
            status.textContent = message;
            status.className = className;
        }

        window.addEventListener('beforeunload', () => {
            polling = false;
        });
    </script>
</body>
</html>
