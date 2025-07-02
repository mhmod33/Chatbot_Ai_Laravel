<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Set a Reminder</title>
    <style>
        body {
            background: #181818;
            color: #f1f1f1;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .center-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .reminder-form {
            background: #232323;
            padding: 32px 40px;
            border-radius: 12px;
            box-shadow: 0 2px 12px #0008;
            display: flex;
            flex-direction: column;
            gap: 18px;
            min-width: 350px;
        }
        .reminder-form input, .reminder-form textarea {
            padding: 12px;
            border-radius: 6px;
            border: none;
            font-size: 1rem;
        }
        .reminder-form button {
            background: #7ecfff;
            color: #181818;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .reminder-form button:hover {
            background: #5bb8e6;
        }
        .reminder-details {
            margin-top: 32px;
            background: #232323;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #0006;
        }
        .back-btn {
            margin-top: 24px;
            background: #444;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            font-size: 1rem;
            cursor: pointer;
        }
        .back-btn:hover {
            background: #222;
        }
    </style>
</head>
<body>
    <div class="center-container">
        <h2>Set a Reminder</h2>
        <form class="reminder-form" id="reminderForm">
            <label for="message">Reminder Request</label>
            <textarea id="message" name="message" rows="3" placeholder="e.g. Please remind me to take a break while I'm studying on 2024-07-02 18:00"></textarea>
            <label for="email">Email (if not logged in)</label>
            <input type="email" id="email" name="email" placeholder="your@email.com">
            <button type="submit">Set Reminder</button>
        </form>
        <div id="reminderDetails" class="reminder-details" style="display:none;"></div>
        <button class="back-btn" onclick="window.location.href='/'">Back</button>
    </div>
    <script>
        document.getElementById('reminderForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = document.getElementById('message').value;
            const email = document.getElementById('email').value;
            const res = await fetch('/reminders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ message, email })
            });
            const data = await res.json();
            const detailsDiv = document.getElementById('reminderDetails');
            if (data.success) {
                detailsDiv.style.display = 'block';
                detailsDiv.innerHTML = `<h3 style='color:#4caf50'>Set!</h3>
                    <p><strong>Task:</strong> ${data.reminder.task}</p>
                    <p><strong>Remind At:</strong> ${data.reminder.remind_at}</p>
                    <p><strong>Email:</strong> ${data.reminder.email}</p>`;
            } else {
                detailsDiv.style.display = 'block';
                detailsDiv.innerHTML = `<span style='color:#ff6a6a'><strong>Error:</strong> ${data.error || 'Unknown error'}</span>`;
            }
        });
    </script>
</body>
</html> 