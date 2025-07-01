<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gemini AI Chatbot</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg,rgb(6, 9, 41) 0%,rgb(8, 0, 31) 100%);
            color: #f1f1f1;
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .chat-container {
            max-width: 500px;
            margin: 40px auto;
            background: #23223aee;
            border-radius: 16px;
            box-shadow: 0 0 24px #0008;
            padding: 24px 24px 16px 24px;
            position: relative;
        }
        .back-btn {
            position: absolute;
            left: 16px;
            top: 16px;
            background: #fff;
            color: #7c4dff;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-family: 'Poppins', Arial, sans-serif;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 2px 8px #0002;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            z-index: 2;
        }
        .back-btn:hover {
            background: #7c4dff;
            color: #fff;
            box-shadow: 0 4px 16px #7c4dff44;
        }
        .chat-log {
            min-height: 200px;
            max-height: 300px;
            overflow-y: auto;
            background: #1a1a2a;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .chat-message {
            margin-bottom: 12px;
            display: flex;
        }
        .bubble {
            padding: 12px 20px;
            border-radius: 20px;
            max-width: 75%;
            word-break: break-word;
            display: inline-block;
            font-size: 1.05rem;
            font-family: 'Poppins', Arial, sans-serif;
            transition: box-shadow 0.2s, background 0.2s, color 0.2s;
        }
        .user .bubble {
            background: linear-gradient(135deg, #fff 0%, #e3e3fd 100%);
            color: #1a237e;
            align-self: flex-end;
            margin-left: auto;
            font-weight: 600;
            box-shadow: 0 2px 8px #7c4dff22;
        }
        .user .bubble:hover {
            background: #ede7f6;
            box-shadow: 0 4px 16px #7c4dff33;
        }
        .bot .bubble {
            background: linear-gradient(135deg, #7c4dff 0%, #00bcd4 100%);
            color: #fff;
            align-self: flex-start;
            margin-right: auto;
            font-weight: 400;
            box-shadow: 0 2px 8px #00bcd422;
        }
        .bot .bubble:hover {
            background: linear-gradient(135deg, #00bcd4 0%, #7c4dff 100%);
            color: #fffde7;
            box-shadow: 0 4px 16px #00bcd433;
        }
        .typing .bubble {
            background: #444;
            color: #ccc;
        }
        .chat-form {
            display: flex;
            gap: 8px;
        }
        .chat-form input, .chat-form select {
            flex: 1;
            padding: 10px;
            border-radius: 6px;
            border: none;
            background: #2a2a3a;
            color: #f1f1f1;
            font-family: 'Poppins', Arial, sans-serif;
            font-size: 1rem;
            transition: background 0.2s, color 0.2s;
        }
        .chat-form input:focus, .chat-form select:focus {
            background: #fff;
            color: #7c4dff;
            outline: none;
        }
        .chat-form button {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            background: linear-gradient(135deg, #7c4dff 0%, #00bcd4 100%);
            color: #fff;
            font-family: 'Poppins', Arial, sans-serif;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 2px 8px #7c4dff22;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }
        .chat-form button:hover {
            background: linear-gradient(135deg, #00bcd4 0%, #7c4dff 100%);
            color: #fffde7;
            box-shadow: 0 4px 16px #00bcd433;
        }
        img.chat-image {
            max-width: 100%;
            border-radius: 10px;
            margin-top: 8px;
            box-shadow: 0 2px 8px #00bcd422;
        }
        h2 {
            font-family: 'Poppins', Arial, sans-serif;
            font-weight: 600;
            color: #fff;
            text-align: center;
            margin-bottom: 24px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <button class="back-btn" onclick="window.location.href='/'">Back</button>
        <h2>Gemini AI Chatbot</h2>
        <div class="chat-log" id="chat-log"></div>
        <form class="chat-form" id="chat-form">
            <input type="text" id="message" placeholder="Type your message..." required autocomplete="off">
            <select id="output-type">
                <option value="text">Text</option>
                <option value="image">Image</option>
            </select>
            <button type="submit">Send</button>
        </form>
    </div>
    <script>
        const chatLog = document.getElementById('chat-log');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message');
        const outputType = document.getElementById('output-type');

        function appendMessage(sender, text, isImage = false) {
            const div = document.createElement('div');
            div.className = 'chat-message ' + sender;
            const bubble = document.createElement('div');
            bubble.className = 'bubble';
            if (isImage) {
                const img = document.createElement('img');
                img.src = text;
                img.className = 'chat-image';
                bubble.appendChild(img);
            } else {
                bubble.textContent = text;
            }
            div.appendChild(bubble);
            chatLog.appendChild(div);
            chatLog.scrollTop = chatLog.scrollHeight;
        }

        function showTyping() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'chat-message bot typing';
            typingDiv.id = 'typing-indicator';
            const bubble = document.createElement('div');
            bubble.className = 'bubble';
            bubble.textContent = 'typing...';
            typingDiv.appendChild(bubble);
            chatLog.appendChild(typingDiv);
            chatLog.scrollTop = chatLog.scrollHeight;
        }

        function removeTyping() {
            const typingDiv = document.getElementById('typing-indicator');
            if (typingDiv) typingDiv.remove();
        }

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            const type = outputType.value;
            if (!message) return;
            appendMessage('user', message);
            messageInput.value = '';
            showTyping();
            try {
                const res = await fetch('/chat/gemini', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message, type })
                });
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch {
                    data = { error: text };
                }
                removeTyping();
                if (type === 'image' && data.image_url) {
                    appendMessage('bot', data.image_url, true);
                } else if (data.candidates && data.candidates[0]?.content?.parts[0]?.text) {
                    appendMessage('bot', data.candidates[0].content.parts[0].text);
                } else if (data.error) {
                    appendMessage('bot', data.error);
                } else {
                    appendMessage('bot', 'No response.');
                }
            } catch (err) {
                removeTyping();
                appendMessage('bot', 'Error: ' + err.message);
            }
        });
    </script>
</body>
</html> 