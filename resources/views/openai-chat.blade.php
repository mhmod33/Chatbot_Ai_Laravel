<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OpenAI Chatbot</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #000;
            color: #111;
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .chat-container {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 0 24px #0002;
            padding: 24px 24px 16px 24px;
            position: relative;
            border: 1px solid #eee;
        }
        .back-btn {
            position: absolute;
            left: 16px;
            top: 16px;
            background: #111;
            color: #fff;
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
            background: #fff;
            color: #111;
            border: 1px solid #111;
            box-shadow: 0 4px 16px #0002;
        }
        .chat-log {
            min-height: 200px;
            max-height: 300px;
            overflow-y: auto;
            background: #f7f7f7;
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
            background: #111;
            color: #fff;
            align-self: flex-end;
            margin-left: auto;
            font-weight: 600;
            box-shadow: 0 2px 8px #0002;
        }
        .user .bubble:hover {
            background: #333;
            color: #fff;
            box-shadow: 0 4px 16px #0003;
        }
        .bot .bubble {
            background: #f1f1f1;
            color: #111;
            align-self: flex-start;
            margin-right: auto;
            font-weight: 400;
            box-shadow: 0 2px 8px #0001;
        }
        .bot .bubble:hover {
            background: #e0e0e0;
            color: #111;
            box-shadow: 0 4px 16px #0002;
        }
        .typing .bubble {
            background: #e0e0e0;
            color: #888;
        }
        .chat-form {
            display: flex;
            gap: 8px;
        }
        .chat-form input, .chat-form select {
            flex: 1;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #eee;
            background: #fff;
            color: #111;
            font-family: 'Poppins', Arial, sans-serif;
            font-size: 1rem;
            transition: background 0.2s, color 0.2s, border 0.2s;
        }
        .chat-form input:focus, .chat-form select:focus {
            background: #f7f7f7;
            color: #111;
            border: 1px solid #111;
            outline: none;
        }
        .chat-form button {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            background: #111;
            color: #fff;
            font-family: 'Poppins', Arial, sans-serif;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 2px 8px #0002;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }
        .chat-form button:hover {
            background: #fff;
            color: #111;
            border: 1px solid #111;
            box-shadow: 0 4px 16px #0002;
        }
        img.chat-image {
            max-width: 100%;
            border-radius: 10px;
            margin-top: 8px;
            box-shadow: 0 2px 8px #0002;
        }
        h2 {
            font-family: 'Poppins', Arial, sans-serif;
            font-weight: 600;
            color: #111;
            text-align: center;
            margin-bottom: 24px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <button class="back-btn" onclick="window.location.href='/'">Back</button>
        <h2>OpenAI Chatbot</h2>
        <div class="chat-log" id="chat-log"></div>
        <form class="chat-form" id="chat-form">
            <input type="text" id="message" placeholder="Type your message..." required autocomplete="off">
            <select id="output-type">
                <option value="o1-mini">Text (o1-mini)</option>
                <option value="dall-e-2">Image (dall-e-2)</option>
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
                const res = await fetch('/chat/openai', {
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
                if (type === 'dall-e-2' && data.image_url) {
                    appendMessage('bot', data.image_url, true);
                } else if (data.text) {
                    appendMessage('bot', data.text);
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