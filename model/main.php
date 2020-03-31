<?php

function isUser() 
{
    return isset($_SESSION['id']);
}

$categories = [
    1 => 'IoT и микроконтроллеры',
    2 => 'Десктопная разработка (Windows, MacOS, Linux)',
    3 => 'Мобильная разработка (Android, iOS)',
    4 => 'Сетевые технологии',
    5 => 'Frontend/Backend/Fullstack',
    6 => 'SEO и аналитика',
    7 => 'Дизайн, UX/UI',
    8 => 'Машинное обучение, BigData',
    9 => 'IT-безопасность',
    0 => 'Другое'
];
