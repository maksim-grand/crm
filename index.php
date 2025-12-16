<?php
// Файл: crm/index.php (Ваш фронтенд)
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Помощник МехТехСервис</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .chat-container { width: 400px; margin: 50px auto; background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; height: 500px; }
        .chat-header { background-color: #007bff; color: white; padding: 15px; border-top-left-radius: 8px; border-top-right-radius: 8px; text-align: center; font-size: 1.2em; }
        .chat-box { flex-grow: 1; padding: 15px; overflow-y: auto; border-bottom: 1px solid #ddd; }
        .input-area { display: flex; padding: 10px; border-top: 1px solid #ddd; }
        .input-area input { flex-grow: 1; padding: 10px; border: 1px solid #ccc; border-radius: 5px; margin-right: 10px; }
        .input-area button { background-color: #28a745; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; }
        .message { margin-bottom: 10px; padding: 8px; border-radius: 5px; max-width: 80%; }
        .user-message { background-color: #d1e7dd; margin-left: auto; text-align: right; }
        .ai-message { background-color: #e9ecef; margin-right: auto; text-align: left; }
        .loading { text-align: center; color: #6c757d; font-style: italic; }
    </style>
</head>
<body>

    <div class="chat-container">
        <div class="chat-header">
            AI Помощник МехТехСервис
        </div>
        <div class="chat-box" id="chat-box">
            <div class="message ai-message">Здравствуйте! Я ваш AI-помощник ТОО «МехТехСервис Кокшетау». Чем я могу помочь?</div>
        </div>
        <div class="input-area">
            <input type="text" id="user-input" placeholder="Введите ваш вопрос...">
            <button onclick="sendMessage()">Отправить</button>
        </div>
    </div>

    <script>
        // ⭐️ КЛЮЧЕВОЕ ИЗМЕНЕНИЕ: ОТНОСИТЕЛЬНЫЙ ПУТЬ ДЛЯ NETLIFY
        // Netlify перенаправит /api/chat на вашу функцию.
        const API_ENDPOINT = '/api/chat'; 
        
        const chatBox = document.getElementById('chat-box');
        const userInput = document.getElementById('user-input');
        
        // История для Gemini (хранит только текст)
        let chatHistory = [];

        function addMessage(sender, text) {
            const messageElement = document.createElement('div');
            messageElement.className = 'message ' + (sender === 'user' ? 'user-message' : 'ai-message');
            messageElement.textContent = text;
            chatBox.appendChild(messageElement);
            // Прокрутка вниз
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function showLoading() {
            const loadingElement = document.createElement('div');
            loadingElement.className = 'loading';
            loadingElement.id = 'loading-message';
            loadingElement.textContent = 'AI печатает...';
            chatBox.appendChild(loadingElement);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function hideLoading() {
            const loadingElement = document.getElementById('loading-message');
            if (loadingElement) {
                loadingElement.remove();
            }
        }

        async function sendMessage() {
            const message = userInput.value.trim();
            if (message === '') return;

            // 1. Отобразить сообщение пользователя
            addMessage('user', message);
            userInput.value = '';
            showLoading();

            // 2. Добавить сообщение в историю для отправки
            chatHistory.push({ 
                role: 'user', 
                parts: [{ text: message }] 
            });

            try {
                const response = await fetch(API_ENDPOINT, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ history: chatHistory }),
                });

                if (!response.ok) {
                    throw new Error(`Ошибка сети или сервера: ${response.status}`);
                }

                const data = await response.json();
                hideLoading();
                
                const aiResponse = data.response;

                // 3. Отобразить ответ AI
                addMessage('ai', aiResponse);

                // 4. Обновить историю для следующего запроса
                chatHistory.push({ 
                    role: 'model', 
                    parts: [{ text: aiResponse }] 
                });

            } catch (error) {
                hideLoading();
                console.error('Ошибка при получении ответа от AI:', error);
                addMessage('ai', 'Произошла ошибка при получении ответа от ИИ. Пожалуйста, проверьте настройки Netlify Functions и лог.');
                
                // Если ошибка, не добавляем неверный ответ в историю
                chatHistory.pop(); 
            }
        }

        // Отправка сообщения по нажатию Enter
        userInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>
</html>
