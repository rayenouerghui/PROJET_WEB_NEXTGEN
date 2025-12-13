<?php include __DIR__ . '/header.php'; ?>

    <div class="don-section">
        <div class="don-card">
            <h1>Points transformés en dons</h1>
            <p>Suivez votre solde solidaire et convertissez-le en actions concrètes.</p>

            <div class="points-balance">
                <div>
                    <span class="points-label">Solde disponible</span>
                    <span class="points-value" id="pointsBalanceValue">0 pts</span>
                </div>
                <p class="points-history" id="pointsHistory">Dernière mise à jour : -</p>
            </div>

            <div class="points-actions">
                <label for="convertAmount">Convertir vos points en dons :</label>
                <div class="points-convert-row">
                    <input type="text" id="convertAmount" value="50" />
                    <button type="button" id="convertButton" class="btn btn-primary">Convertir</button>
                </div>
                <p class="points-impact" id="pointsImpact">50 points = 1 panier alimentaire.</p>
            </div>

            <ul>
                <li>1 réservation = des points solidaires crédités automatiquement.</li>
                <li>100 points = 1 panier alimentaire offert.</li>
                <li>Conversion immédiate depuis votre solde disponible.</li>
            </ul>
            <p class="don-note" id="conversionMessage">Merci de contribuer à rendre chaque événement plus solidaire.</p>
        </div>
    </div>

    <div id="pointsToast" class="reservation-toast"></div>

    <script type="text/javascript">
        (function () {
            var pointsKey = 'nextgenPoints';
            var pointsBalance = parseInt(window.localStorage.getItem(pointsKey) || '0', 10);
            var pointsBalanceValue = document.getElementById('pointsBalanceValue');
            var pointsHistory = document.getElementById('pointsHistory');
            var convertInput = document.getElementById('convertAmount');
            var convertButton = document.getElementById('convertButton');
            var conversionMessage = document.getElementById('conversionMessage');
            var pointsImpact = document.getElementById('pointsImpact');
            var toast = document.getElementById('pointsToast');

            function updatePointsDisplay() {
                pointsBalanceValue.innerHTML = pointsBalance + ' pts';
                pointsHistory.innerHTML = 'Dernière mise à jour : ' + new Date().toLocaleString();
                var amount = parseInt(convertInput.value, 10);
                if (isNaN(amount) || amount < 0) {
                    amount = 0;
                }
                pointsImpact.innerHTML = amount + ' points = ' + Math.floor(amount / 100) + ' panier(s) alimentaire(s).';
            }

            function showToast(message) {
                if (!toast) {
                    return;
                }
                toast.innerHTML = message;
                toast.classList.add('show');
                setTimeout(function () {
                    toast.classList.remove('show');
                }, 2500);
            }

            function convertPoints(amount) {
                if (amount <= 0) {
                    conversionMessage.innerHTML = 'Le nombre de points doit être supérieur à 0.';
                    return;
                }
                if (amount > pointsBalance) {
                    conversionMessage.innerHTML = 'Solde insuffisant pour convertir ' + amount + ' points.';
                    return;
                }
                pointsBalance -= amount;
                window.localStorage.setItem(pointsKey, pointsBalance);
                conversionMessage.innerHTML = 'Merci ! ' + amount + ' points ont été convertis en dons. Solde restant : ' + pointsBalance + ' pts.';
                updatePointsDisplay();
                showToast('Conversion effectuée ✅');
            }

            convertInput.addEventListener('input', function () {
                updatePointsDisplay();
            });

            convertButton.addEventListener('click', function () {
                var amount = parseInt(convertInput.value, 10);
                if (isNaN(amount)) {
                    amount = 0;
                }
                convertPoints(amount);
            });

            updatePointsDisplay();
        })();
    </script>

</body>
</html>

