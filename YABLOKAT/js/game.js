class Game {
    constructor() {
        this.player1Name = '';
        this.player2Name = '';
        this.player1Score = 0;
        this.player2Score = 0;
        this.currentPlayer = 1;
        this.difficulty = 'Новичок';
        this.roundNumber = 1;
        this.totalRounds = 0;
        this.initializeElements();
        this.attachEventListeners();
    }
    initializeElements() {
        this.mainPage = document.getElementById('mainPage');
        this.loadingAnimation = document.getElementById('loadingAnimation');
        this.gameMenu = document.getElementById('gameMenu');
        this.gameScreen = document.getElementById('gameScreen');
        this.resultsScreen = document.getElementById('resultsScreen');
        this.difficultySelect = document.getElementById('difficultySelect');
        this.player1NameInput = document.getElementById('player1Name');
        this.player2NameInput = document.getElementById('player2Name');
        this.firstPlayerInfo = document.getElementById('firstPlayerInfo');
        this.currentPlayerName = document.getElementById('currentPlayerName');
        this.player1NameDisplay = document.getElementById('player1NameDisplay');
        this.player2NameDisplay = document.getElementById('player2NameDisplay');
        this.player1ScoreDisplay = document.getElementById('player1Score');
        this.player2ScoreDisplay = document.getElementById('player2Score');
        this.roundNumberDisplay = document.getElementById('roundNumber');
        this.resultsContent = document.getElementById('resultsContent');
        this.saveResultBtn = document.getElementById('saveResultBtn');
    }
    attachEventListeners() {
        document.getElementById('startGameBtn').addEventListener('click', () => this.showLoadingAnimation());
        document.getElementById('rulesBtn').addEventListener('click', () => {
            window.location.href = '/rules.php';
        });
        document.getElementById('startPlayingBtn').addEventListener('click', () => this.startGame());
        document.getElementById('backToMainBtn').addEventListener('click', () => this.showPage(this.mainPage));
        this.player1NameInput.addEventListener('input', () => this.checkPlayerNames());
        this.player2NameInput.addEventListener('input', () => this.checkPlayerNames());
        document.querySelectorAll('.btn-score').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const points = parseInt(e.target.dataset.points);
                this.addPoints(points);
            });
        });
        document.getElementById('nextTurnBtn').addEventListener('click', () => this.nextTurn());
        document.getElementById('endGameBtn').addEventListener('click', () => this.endGame());
        document.getElementById('playAgainBtn').addEventListener('click', () => this.resetGame());
        document.getElementById('backToMainFromResultsBtn').addEventListener('click', () => this.showPage(this.mainPage));
        if (this.saveResultBtn) {
            this.saveResultBtn.addEventListener('click', () => this.saveResult());
        }
    }
    showPage(page) {
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        page.classList.add('active');
    }
    showLoadingAnimation() {
        this.loadingAnimation.classList.add('active');
        setTimeout(() => {
            this.loadingAnimation.classList.remove('active');
            this.showPage(this.gameMenu);
        }, 2000);
    }
    checkPlayerNames() {
        const name1 = this.player1NameInput.value.trim();
        const name2 = this.player2NameInput.value.trim();
        if (name1 && name2) {
            this.currentPlayer = Math.random() < 0.5 ? 1 : 2;
            const firstPlayerName = this.currentPlayer === 1 ? name1 : name2;
            this.firstPlayerInfo.textContent = `Первым ходит: ${firstPlayerName}`;
            this.firstPlayerInfo.classList.add('show');
        } else {
            this.firstPlayerInfo.classList.remove('show');
        }
    }
    startGame() {
        const name1 = this.player1NameInput.value.trim();
        const name2 = this.player2NameInput.value.trim();
        if (!name1 || !name2) {
            alert('Введите имена обоих игроков');
            return;
        }
        this.player1Name = name1;
        this.player2Name = name2;
        this.difficulty = this.difficultySelect.value;
        this.player1Score = 0;
        this.player2Score = 0;
        this.roundNumber = 1;
        this.totalRounds = 0;
        this.updateGameDisplay();
        this.showPage(this.gameScreen);
    }
    updateGameDisplay() {
        this.currentPlayerName.textContent = this.currentPlayer === 1 ? this.player1Name : this.player2Name;
        this.player1NameDisplay.textContent = this.player1Name;
        this.player2NameDisplay.textContent = this.player2Name;
        this.player1ScoreDisplay.textContent = this.player1Score;
        this.player2ScoreDisplay.textContent = this.player2Score;
        this.roundNumberDisplay.textContent = this.roundNumber;
    }
    addPoints(points) {
        if (this.currentPlayer === 1) {
            this.player1Score += points;
        } else {
            this.player2Score += points;
        }
        this.updateGameDisplay();
    }
    nextTurn() {
        this.currentPlayer = this.currentPlayer === 1 ? 2 : 1;
        if (this.currentPlayer === 1) {
            this.roundNumber++;
        }
        this.totalRounds++;
        this.updateGameDisplay();
    }
    endGame() {
        const winner = this.player1Score < this.player2Score ? this.player1Name : this.player2Name;
        const winnerScore = Math.min(this.player1Score, this.player2Score);
        const loserScore = Math.max(this.player1Score, this.player2Score);
        this.resultsContent.innerHTML = `
            <div class="winner-announcement">🏆 Победитель: ${winner}!</div>
            <p><strong>${this.player1Name}:</strong> ${this.player1Score} баллов</p>
            <p><strong>${this.player2Name}:</strong> ${this.player2Score} баллов</p>
            <p>Сложность: ${this.difficulty}</p>
            <p>Сыграно раундов: ${this.roundNumber - 1}</p>
        `;
        const isLoggedIn = document.querySelector('.user-welcome') !== null;
        if (isLoggedIn && this.saveResultBtn) {
            this.saveResultBtn.style.display = 'inline-block';
        }
        this.showPage(this.resultsScreen);
    }
    async saveResult() {
        const isPlayer1Winner = this.player1Score < this.player2Score;
        const result = {
            playerName: this.player1Name,
            opponentName: this.player2Name,
            difficulty: this.difficulty,
            playerScore: this.player1Score,
            opponentScore: this.player2Score,
            roundsPlayed: this.roundNumber - 1
        };
        try {
            const response = await fetch('/api/save_result.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(result)
            });
            const data = await response.json();
            if (data.success) {
                alert('Результат сохранен!');
                this.saveResultBtn.disabled = true;
                this.saveResultBtn.textContent = 'Сохранено';
            } else {
                alert('Ошибка при сохранении: ' + data.message);
            }
        } catch (error) {
            alert('Ошибка соединения с сервером');
        }
    }
    resetGame() {
        this.player1NameInput.value = '';
        this.player2NameInput.value = '';
        this.firstPlayerInfo.classList.remove('show');
        this.saveResultBtn.disabled = false;
        this.saveResultBtn.textContent = 'Сохранить результат';
        this.showPage(this.gameMenu);
    }
}
document.addEventListener('DOMContentLoaded', () => {
    new Game();
});