<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot Home</title>
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
        .chat-btn {
            background: #7ecfff;
            color: #181818;
            border: none;
            border-radius: 8px;
            padding: 18px 40px;
            font-size: 1.3rem;
            font-weight: bold;
            margin: 18px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .chat-btn:hover {
            background: #5bb8e6;
        }
    </style>
</head>
<body>
    <div class="center-container">
        <h1>Welcome to AI Chatbot</h1>
        <a href="/gemini-chat"><button class="chat-btn">Gemini Chat</button></a>
        <a href="/openai-chat"><button class="chat-btn">OpenAI Chat</button></a>
        <a href="/reminder"><button class="chat-btn">Reminder</button></a>
        <a href="/rag"><button class="chat-btn">RAG System</button></a>
    </div>
</body>
</html>
