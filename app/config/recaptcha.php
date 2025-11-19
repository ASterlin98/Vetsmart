<?php
// Configuración para reCAPTCHA v3. Preferible definir las claves en variables de entorno.
// reCAPTCHA v3 es invisible; verifica en background y devuelve un score (0-1).
// score ~1.0 = humano, ~0.0 = probable bot.
return [
    'site_key'   => getenv('RECAPTCHA_SITE_KEY') ?: '6LfXpAssAAAAAGXY0ZSq5t-EU8Gv4y7s94VEx5JM',
    'secret_key' => getenv('RECAPTCHA_SECRET_KEY') ?: '6LfXpAssAAAAADTySUEh02PjkkSVGS6slU006qlV',
    'score_threshold' => 0.5, // Rechazar si score < 0.5 (ajusta según tu criterio)
    'version' => 'v3', // reCAPTCHA v3
];
