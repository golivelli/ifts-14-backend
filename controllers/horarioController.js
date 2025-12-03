const pool = require('../config/database');

/**
 * Obtiene todos los horarios de la base de datos.
 */
const getHorarios = async (req, res) => {
  try {
    // Aquí hacemos la consulta a la base de datos.
    // NOTA: Esto asume que tienes una tabla llamada 'horarios'.
    // Si la tabla no existe, esto dará un error, pero la estructura es correcta.
    const [rows] = await pool.query('SELECT * FROM horarios');
    
    res.json(rows);
  } catch (error) {
    console.error('Error al obtener los horarios:', error);
    res.status(500).json({ message: 'Error interno del servidor' });
  }
};

// Exportamos la función para poder usarla en nuestras rutas
module.exports = {
  getHorarios,
};