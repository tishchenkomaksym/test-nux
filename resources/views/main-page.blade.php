<!DOCTYPE html>
<html>
<head>
    <title>Page A</title>
</head>
<body>
<h1>Welcome, {{ $user['username'] }}</h1>
<button onclick="generateNewLink()">Generate New Link</button>
<button onclick="deactivateLink()">Deactivate Link</button>
<button onclick="imFeelingLucky()">I'm Feeling Lucky</button>
<button onclick="viewHistory()">History</button>

<div id="result"></div>
<div id="history"></div>
<div id="new-link-container">
</div>

<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }
    th {
        background-color: #f4f4f4;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
</style>

<script>
    function generateNewLink() {
        fetch('/api/main-page/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user: {{ $user['id'] }}, link: '{{ request()->fullUrl() }}' })
        })
            .then(response => response.json())
            .then(data => {
                // Обновляем HTML-контейнер с новой ссылкой
                const linkContainer = document.getElementById('new-link-container');
                linkContainer.innerHTML = `
            <p>Ваша новая ссылка:</p>
            <a href="${data.link}" target="_blank">${data.link}</a>
        `;
            })
            .catch(error => {
                console.error('Error generating link:', error);
                alert('Произошла ошибка при генерации ссылки.');
            });
    }


    function deactivateLink() {
        fetch('/api/main-page/deactivate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user: {{ $user['id'] }}, link: '{{ request()->fullUrl() }}' })
        }).then(response => response.json())
            .then(data => alert(data.message));
    }

    function imFeelingLucky() {
        fetch('/api/main-page/imfeelinglucky', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user: {{ $user['id'] }}, link: '{{ request()->fullUrl() }}' })
        }).then(response => response.json())
            .then(data => {
                document.getElementById('result').innerHTML = `Number: ${data.number}, Result: ${data.result}, Win Amount: ${data.winAmount}`;
            });
    }

    function viewHistory() {
        fetch('/api/main-page/history', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user: {{ $user['id'] }}, link: '{{ request()->fullUrl() }}' })
        })
            .then(response => response.json())
            .then(data => {
                const historyContainer = document.getElementById('history');
                console.log(data)
                if (data.length > 0) {
                    let table = `
                <table border="1" cellpadding="5" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Число</th>
                            <th>Результат</th>
                            <th>Выигрыш</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
                    data.forEach((entry, index) => {
                        console.log(entry)
                        table += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${entry.number ?? 'N/A'}</td>
                        <td>${entry.result ?? 'N/A'}</td>
                        <td>${entry.winAmount ?? 0}</td>
                    </tr>
                `;
                    });
                    table += '</tbody></table>';
                    historyContainer.innerHTML = table;
                } else {
                    historyContainer.innerHTML = '<p>История пуста.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching history:', error);
                document.getElementById('history').innerHTML = '<p>Произошла ошибка при загрузке истории.</p>';
            });
    }

</script>
</body>
</html>

