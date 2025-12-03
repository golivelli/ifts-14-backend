const express = require('express');
const router = express.Router();

// Importamos el controlador que acabamos de crear
const { getHorarios } = require('../controllers/horarioController');

// Definimos la ruta. Cuando alguien haga un GET a '/', se ejecutará getHorarios
router.get('/', getHorarios);

// Exportamos el router para usarlo en app.js
module.exports = router;