// Файл: api/chat.js
// Serverless Function для Vercel

const express = require('express');
const serverless = require('serverless-http');
const { GoogleGenAI } = require('@google/genai');

const app = express();

// Инициализируем Gemini. Ключ GEMINI_API_KEY берется из переменных окружения Vercel
const ai = new GoogleGenAI({}); 
const model = "gemini-1.5-flash"; 

// Поскольку CORS теперь обрабатывается через vercel.json, 
// мы можем удалить сложные настройки app.use(cors) и app.options.

app.use(express.json());

// ==========================================================
// ⭐️ СИСТЕМНАЯ ИНСТРУКЦИЯ И ЛОГИКА
// ==========================================================
const systemInstruction = `
    Ты — дружелюбный и компетентный ИИ-помощник интернет-магазина "МехТехСервис Кокшетау" (сельхоззапчасти).

    О компании "МехТехСервис Кокшетау":
    - ТОО «МехТехСервис Кокшетау» - динамично развивающаяся компания, основана в 2018 году.
    - Специализация: комплексное обслуживание сельскохозяйственной техники и поставка оригинальных запчастей.
    - Расположение: город Кокшетау, Казахстан (сердце сельскохозяйственного региона).
    - Преимущество: объединяет лучших специалистов с многолетним опытом, предлагает комплексные решения для эффективной работы в поле.

    Твоя главная задача:
    1. Отвечать на вопросы клиентов о компании, наличии запчастей, доставке, гарантии и услугах, используя приведенную выше информацию.
    2. Если пользователь спрашивает о конкретной детали, цене или наличии, всегда предлагай проверить информацию на страницах "Каталог" или "Схемы".
    3. Контакты компании для связи с менеджером: E-mail Mts_parts@mail.ru или телефон +7 776 405 2323.
    4. Отвечай кратко, профессионально, но дружелюбно.
`;

// Маршрут для POST-запроса (логика чата)
app.post('/api/chat', async (req, res) => {
    
    // Добавление заголовка Access-Control-Allow-Origin здесь является избыточным, 
    // но если vercel.json не сработает, это может помочь. 
    // Однако, вернее всего положиться на vercel.json.

    try {
        const chatHistory = req.body.history || [];
        
        const result = await ai.models.generateContent({
            model: model,
            contents: chatHistory,
            config: {
                systemInstruction: systemInstruction,
                temperature: 0.7,
            },
        });

        const responseText = result.text;
        res.json({ response: responseText });

    } catch (error) {
        console.error('Gemini API Error:', error);
        res.status(500).json({ error: 'Произошла ошибка на сервере AI.' });
    }
});

// Экспортируем функцию для Vercel
module.exports.handler = serverless(app);
