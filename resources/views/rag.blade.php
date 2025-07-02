<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAG System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { background: #181818; color: #f1f1f1; font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .center-container { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; }
        .rag-form { background: #232323; padding: 32px 40px; border-radius: 12px; box-shadow: 0 2px 12px #0008; display: flex; flex-direction: column; gap: 18px; min-width: 350px; }
        .rag-form input { padding: 12px; border-radius: 6px; border: none; font-size: 1rem; }
        .rag-form button { background: #7ecfff; color: #181818; border: none; border-radius: 8px; padding: 12px 0; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: background 0.2s; }
        .rag-form button:hover { background: #5bb8e6; }
        .rag-answer { margin-top: 32px; background: #232323; padding: 24px; border-radius: 10px; box-shadow: 0 2px 8px #0006; }
        .back-btn { margin-top: 24px; background: #444; color: #fff; border: none; border-radius: 8px; padding: 10px 24px; font-size: 1rem; cursor: pointer; }
        .back-btn:hover { background: #222; }
    </style>
</head>
<body>
    <div class="center-container">
        <h2>Retrieval-Augmented Generation (RAG) System</h2>
        <form class="rag-form" id="ragForm">
            <label for="question">Ask a question:</label>
            <input type="text" id="question" name="question" placeholder="Type your question here...">
            <button type="submit">Ask</button>
        </form>
        <div id="ragAnswer" class="rag-answer" style="display:none;"></div>
        <button class="back-btn" onclick="window.location.href='/'">Back</button>
    </div>
    <script>
        document.getElementById('ragForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const question = document.getElementById('question').value;
            const res = await fetch('/rag/query', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ question })
            });
            const data = await res.json();
            const answerDiv = document.getElementById('ragAnswer');
            if (data.answer) {
                answerDiv.style.display = 'block';
                answerDiv.innerHTML =
                    (data.knowledge ? `<h4>Knowledge Used:</h4><strong>${data.knowledge.title}</strong><br><pre>${data.knowledge.content}</pre><hr>` : '') +
                    `<h3>Answer:</h3><p>${data.answer}</p>`;
            } else {
                answerDiv.style.display = 'block';
                answerDiv.innerHTML = `<span style='color:#ff6a6a'><strong>Error:</strong> ${data.error || 'Unknown error'}</span>`;
            }
        });
    </script>
</body>
</html> 