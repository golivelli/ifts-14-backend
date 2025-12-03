// 1. Importaciones
const express = require('express');
const dotenv = require('dotenv');
const cors = require('cors');
const horariosRoutes = require('./routes/horarios');

// Cargar variables de entorno desde el archivo .env
dotenv.config();

// 2. Inicialización
const app = express();
const port = process.env.PORT || 3000;

// 3. Middlewares
app.use(cors()); // Permite peticiones desde el frontend
app.use(express.json()); // Permite al servidor entender JSON

// 4. Rutas
app.get('/', (req, res) => {
  res.send('¡API del IFTS 14 funcionando!');
});

// Le decimos a la app que use nuestras rutas de horarios para cualquier petición que empiece con /api/horarios
app.use('/api/horarios', horariosRoutes);

// 5. Iniciar Servidor
app.listen(port, () => {
  console.log(`Servidor corriendo en http://localhost:${port}`);
});