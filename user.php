<?php

require_once 'psql_config.php';

function isUser() {
    return isset($_SESSION['user']) ? true : false;
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


class User {
    private $id;
    private $name;
    private $email;
    private $access_level; 
    private $reports;

    function __construct($id, $name, $email, $access_level) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->access_level = $access_level;
        $this->reports = null;
    }

    function getId() { return $this->id; }
    function getName() { return $this->name; }
    function getEmail() { return $this->email; }
    function getAccessLevel() { return $this->access_level; }

    function getFolder() {
        return "users/".$this->email;
    }

    private function connectToDB() {
        try {
            return new PDO("pgsql:host=127.0.0.1;port=5432;dbname=conference;user=conference"); //Почему не видит переменные из psql_config??? В других местах видит
        } 
        catch (PDOException $e) {
            return false;
        }
    }

    function getReports() {
        $connection = $this->connectToDB();
        if ($connection !== false) {

            $queryArgs = [];
            
            if ($this->access_level === 0) {
                $query = $connection->prepare("SELECT r.name AS report_name, u.name, u.email, r.category, r.description, r.id FROM reports AS r JOIN users AS u ON u.id = r.user_id");
            }
            else if ($this->access_level === 1) {
                $query = $connection->prepare("SELECT r.name AS report_name, u.name, u.email, r.category, r.description, r.id FROM reports AS r JOIN users AS u ON u.id = r.user_id WHERE u.id = ?");
                $queryArgs = [$this->id];
            }
            
            if ($query->execute($queryArgs) === true) {
                $connection = null;
                return $query->fetchAll();
            }
            else {
                $connection = null;
                return null;
            }
        }

        return null;
    }

    function getDetailedReport($report_id) {
        $connection = $this->connectToDB();

        if ($connection !== false) {

            $queryArgs = [];
            
            if ($this->access_level === 0) {
                $query = $connection->prepare("SELECT r.name AS report_name, r.user_info, r.category, r.description, r.speech_file, r.present_file, r.id AS report_id, u.email FROM reports AS r JOIN users AS u ON r.user_id = u.id WHERE r.id = ?");
                $queryArgs = [$report_id];
            }
            else if ($this->access_level === 1) {
                $query = $connection->prepare("SELECT r.name AS report_name, r.user_info, r.category, r.description, r.speech_file, r.present_file, r.id AS report_id FROM reports AS r WHERE r.id = ? AND r.user_id = ?");
                $queryArgs = [$report_id, $this->id];
            }
            
            if ($query->execute($queryArgs) === true) {
                $connection = null;
                return $query->fetchAll()[0];
            }
            else {
                $connection = null;
                return null;
            }
        }

        return null;
    }
}

?>