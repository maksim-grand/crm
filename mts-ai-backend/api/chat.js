// api/chat.js (на GitHub/Vercel)

// ⚠️ ВРЕМЕННОЕ РЕШЕНИЕ ДЛЯ РАЗРАБОТКИ!
// ЭТО ПОЗВОЛИТ ВАШЕМУ ЛОКАЛЬНОМУ СЕРВЕРУ (http://localhost) ПОЛУЧАТЬ ОТВЕТЫ.
const allowedOrigin = [
    'http://localhost', 
    'http://127.0.0.1', 
    // Добавьте 'https://ваш_сайт.kz' после запуска сайта!
]; 

app.use(cors({ 
    origin: (origin, callback) => {
        // Разрешить, если источник есть в списке или это запрос без Origin (например, Postman)
        if (!origin || allowedOrigin.includes(origin)) {
            callback(null, true);
        } else {
            callback(new Error('Не разрешено политикой CORS'));
        }
    },
    methods: ['POST'],
}));

// ... (внутри app.post('/api/chat', ... ))
// Уберите старую жесткую проверку, если она была, и оставьте только проверку CORS выше.

// ...