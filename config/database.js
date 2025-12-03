const mysql = require('mysql2/promise');
const dotenv = require('dotenv');

// Cargar variables de entorno
dotenv.config();

// Crear un "pool" de conexiones a la base de datos
// Un pool es más eficiente que crear una conexión por cada consulta
const pool = mysql.createPool({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
  waitForConnections: true,
  connectionLimit: 10, // Puedes ajustar este número según tus necesidades
  queueLimit: 0
});

// Exportamos el pool para poder usarlo en otras partes de la aplicación (como en los controladores)
module.exports = pool;