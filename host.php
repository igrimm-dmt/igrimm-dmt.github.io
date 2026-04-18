<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buzzer App - Host</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        .session-layout {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        .main-content {
            flex: 1;
            min-width: 0;
        }
        .leaderboard-panel {
            width: 300px;
            flex-shrink: 0;
            position: sticky;
            top: 20px;
            background: #f0f7ff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .session-key {
            font-size: 48px;
            font-weight: bold;
            text-align: center;
            color: #4CAF50;
            padding: 20px;
            margin: 20px 0;
            background: #f0f0f0;
            border-radius: 5px;
            letter-spacing: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        button:hover {
            background-color: #45a049;
        }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .reset-btn {
            background-color: #ff9800;
        }
        .reset-btn:hover {
            background-color: #e68900;
        }
        .end-btn {
            background-color: #f44336;
        }
        .end-btn:hover {
            background-color: #da190b;
        }
        .participants {
            margin-top: 30px;
        }
        .participant {
            padding: 15px;
            margin: 10px 0;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .participant.buzzed {
            background: #ffeb3b;
            border-left: 5px solid #fbc02d;
        }
        .buzz-time {
            font-size: 12px;
            color: #666;
            margin-left: 10px;
        }
        .participant-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        .participant-name {
            flex: 1;
        }
        .participant-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .participant-answer {
            font-style: italic;
            color: #555;
            margin-top: 8px;
            padding: 8px;
            background: #fff;
            border-radius: 3px;
            border-left: 3px solid #4CAF50;
        }
        .participant-score {
            font-weight: bold;
            color: #4CAF50;
        }
        .score-buttons {
            display: flex;
            gap: 5px;
        }
        .score-btn {
            background: none;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 5px 12px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
            margin: 0;
        }
        .score-btn:hover {
            background: #f0f0f0;
        }
        .award-btn {
            color: #4CAF50;
            border-color: #4CAF50;
        }
        .award-btn:hover {
            background: #e8f5e9;
        }
        .remove-btn {
            color: #f44336;
            border-color: #f44336;
        }
        .remove-btn:hover {
            background: #ffebee;
        }
        .leaderboard-panel h2 {
            margin-top: 0;
            color: #1976d2;
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
        .controls {
            text-align: center;
            margin: 20px 0;
        }
        #status {
            text-align: center;
            color: #666;
            margin: 10px 0;
        }
        @media (max-width: 900px) {
            .session-layout {
                flex-direction: column;
            }
            .leaderboard-panel {
                width: 100%;
                position: static;
                max-height: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>?? Buzzer App - Host View</h1>
        
        <div id="setup">
            <div class="controls">
                <button id="createSessionBtn" onclick="createSession()">Create Session</button>
            </div>
            <div id="status">Click "Create Session" to start</div>
        </div>

        <div id="sessionView" style="display: none;">
            <div class="session-key" id="sessionKey"></div>
            <div id="status">Share this key with participants</div>
            
            <div class="controls">
                <button class="reset-btn" onclick="resetBuzzers()">Reset Buzzers</button>
                <button class="end-btn" onclick="endSession()">End Session</button>
            </div>

            <div class="session-layout">
                <div class="main-content">
                    <div class="participants">
                        <h2>Participants</h2>
                        <div id="participantsList"></div>
                    </div>
                </div>

                <div class="leaderboard-panel">
                    <h2>?? Leaderboard</h2>
                    <div id="leaderboardList"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentSessionKey;
        let lastUpdate = 0;
        let polling = false;

        async function createSession() {
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=createSession'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    currentSessionKey = data.sessionKey;
                    document.getElementById('setup').style.display = 'none';
                    document.getElementById('sessionView').style.display = 'block';
                    document.getElementById('sessionKey').textContent = currentSessionKey;
                    updateLeaderboard([]);
                    startPolling();
                } else {
                    alert('Failed to create session');
                }
            } catch (error) {
                alert('Error: ' + error.message);
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
                const response = await fetch(`api.php?action=pollUpdates&sessionKey=${currentSessionKey}&lastUpdate=${lastUpdate}`);
                const data = await response.json();
                
                if (data.success && data.session) {
                    if (!data.session.isActive) {
                        alert('Session ended');
                        location.reload();
                        return;
                    }
                    
                    lastUpdate = data.lastUpdate;
                    updateParticipantsList(data.session.participants);
                    updateLeaderboard(data.session.participants);
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
            
            // Continue polling
            if (polling) {
                setTimeout(poll, 100);
            }
        }

        function updateParticipantsList(participants) {
            if (!participants) return;
            
            const list = document.getElementById('participantsList');
            list.innerHTML = '';
            
            const participantsArray = Object.values(participants);
            
            const buzzed = participantsArray.filter(p => p.buzzedAt).sort((a, b) => {
                return a.buzzedAt - b.buzzedAt;
            });
            
            const notBuzzed = participantsArray.filter(p => !p.buzzedAt).sort((a, b) => {
                return a.name.localeCompare(b.name);
            });

            buzzed.forEach((participant, index) => {
                const div = document.createElement('div');
                div.className = 'participant buzzed';
                
                let html = '<div class="participant-header">';
                html += `<span class="participant-name"><strong>#${index + 1}</strong> - ${escapeHtml(participant.name)}</span>`;
                html += `<div class="participant-info">`;
                html += `<span class="participant-score">Score: ${participant.score}</span>`;
                html += `<div class="score-buttons">`;
                html += `<button class="score-btn award-btn" onclick="awardPoint('${participant.id}')">+1</button>`;
                html += `<button class="score-btn remove-btn" onclick="removePoint('${participant.id}')">-1</button>`;
                html += `</div>`;
                html += `<span class="buzz-time">Buzzed in!</span>`;
                html += `</div>`;
                html += '</div>';
                if (participant.answer) {
                    html += `<div class="participant-answer">`;
                    html += `<span>Answer: ${escapeHtml(participant.answer)}</span>`;
                    html += `</div>`;
                }
                div.innerHTML = html;
                list.appendChild(div);
            });

            notBuzzed.forEach((participant) => {
                const div = document.createElement('div');
                div.className = 'participant';
                let html = '<div class="participant-header">';
                html += `<span class="participant-name">${escapeHtml(participant.name)}</span>`;
                html += `<div class="participant-info">`;
                html += `<span class="participant-score">Score: ${participant.score}</span>`;
                html += `<div class="score-buttons">`;
                html += `<button class="score-btn award-btn" onclick="awardPoint('${participant.id}')">+1</button>`;
                html += `<button class="score-btn remove-btn" onclick="removePoint('${participant.id}')">-1</button>`;
                html += `</div>`;
                html += `</div>`;
                html += '</div>';
                if (participant.answer) {
                    html += `<div class="participant-answer">`;
                    html += `<span>Answer: ${escapeHtml(participant.answer)}</span>`;
                    html += `</div>`;
                }
                div.innerHTML = html;
                list.appendChild(div);
            });
        }

        function updateLeaderboard(participants) {
            if (!participants) return;
            
            const leaderboardList = document.getElementById('leaderboardList');
            leaderboardList.innerHTML = '';
            
            const participantsArray = Object.values(participants);
            const sorted = [...participantsArray].sort((a, b) => b.score - a.score);
            
            sorted.forEach((participant, index) => {
                const div = document.createElement('div');
                div.className = 'leaderboard-item';
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

        async function awardPoint(participantId) {
            try {
                await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=awardPoint&sessionKey=${currentSessionKey}&participantId=${participantId}`
                });
            } catch (error) {
                console.error('Error awarding point:', error);
            }
        }

        async function removePoint(participantId) {
            try {
                await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=removePoint&sessionKey=${currentSessionKey}&participantId=${participantId}`
                });
            } catch (error) {
                console.error('Error removing point:', error);
            }
        }

        async function resetBuzzers() {
            try {
                await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=resetBuzzers&sessionKey=${currentSessionKey}`
                });
            } catch (error) {
                console.error('Error resetting buzzers:', error);
            }
        }

        async function endSession() {
            if (confirm("Are you sure you want to end this session?")) {
                try {
                    await fetch('api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=endSession&sessionKey=${currentSessionKey}`
                    });
                    polling = false;
                    alert('Session ended');
                    location.reload();
                } catch (error) {
                    console.error('Error ending session:', error);
                }
            }
        }

        window.addEventListener('beforeunload', () => {
            polling = false;
        });
    </script>
</body>
</html>
