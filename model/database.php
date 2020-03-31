<?php

class Database
{
    private $conn = null;
    private $error = null;

    //public function __construct() {}

    public function connect()
    {
        $host = '127.0.0.1';
        $port = '5432';
        $dbname = 'conference';
        $user = 'conference';
        $password = 'qwe123';

        if ($this->conn === null) {
            try {
                $this->conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password");
                return true;
            } catch (Exception $e) {
                $this->error = $e->__toString();
                return false;
            }
        } else {
            return true;
        }
    }

    public function getUser($key)
    {
        if ($this->conn !== null) {
            if (ctype_digit($key)) {
                $query = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
            } else {
                $query = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            }

            if ($query->execute([$key]) === true) {
                return $query->fetchAll()[0];
            } else {
                $this->error = $query->errorInfo()[2];
                return false;
            }
        } else {
            return true;
        }
    }

    public function insertUser($name, $email, $password)
    {
        if ($this->conn !== null) {
            $query = $this->conn->prepare("INSERT INTO users VALUES (DEFAULT, ?, ?, ?, 1) RETURNING id");

            if ($query->execute([$name, $email, $password]) === true) {
                return $query->fetchAll()[0]['id'];
            } else {
                $this->error = $query->errorInfo()[2];
                return false;
            }
        } else {
            return true;
        }
    }

    public function getReport($report_id, $id, $access_level)
    {
        if ($this->conn !== null) {
            $request = "r.id AS report_id, r.user_id, r.user_info, r.category, r.name AS report_name, r.description, r.speech_file, r.present_file";
            if ($access_level === 0) {
                $query = $this->conn->prepare("SELECT $request, u.email, u.name FROM reports AS r JOIN users AS u ON r.user_id = u.id WHERE r.id = ?");
                $exec_arr = [$report_id];
            } elseif ($access_level === 1) {
                $query = $this->conn->prepare("SELECT $request FROM reports AS r WHERE r.id = ? AND r.user_id = ?");
                $exec_arr = [$report_id, $id];
            }

            if ($query->execute($exec_arr) === true) {
                return $query->fetchAll()[0];
            } else {
                $this->error = $query->errorInfo();
                return false;
            }
        } else {
            return true;
        }
    }

    public function getAllReports($id, $access_level)
    {
        $columns = "u.name, u.email, r.id, r.name AS report_name, r.category, r.description";

        if ($access_level === 0) {
            $query = $this->conn->prepare("SELECT {$columns} FROM users AS u JOIN reports AS r ON u.id = r.user_id");
            $exec_arr = [];
        } elseif ($access_level === 1) {
            $query = $this->conn->prepare("SELECT {$columns} FROM users AS u JOIN reports AS r ON u.id = r.user_id WHERE u.id = ?");
            $exec_arr = [$id];
        }

        if ($query->execute($exec_arr) === true) {
            return $query->fetchAll(PDO::FETCH_NAMED);
        } else {
            $this->error = $query->errorInfo();
            return false;
        }
    }

    public function addReport($id, $report)
    {
        if ($this->conn !== null) {
            $query = $this->conn->prepare("INSERT INTO reports VALUES (DEFAULT, ?, ?, ?, ?, ?, ?, ?) RETURNING id");

            if ($query->execute([$id, $report['speaker_info'], $report['category'], $report['name'], $report['description'], $report['speech_file'], $report['present_file']])) {
                return $query->fetchAll()[0][0];
            } else {
                $this->error = $query->errorInfo();
                return false;
            }
        } else {
            return true;
        }
    }

    public function getError()
    {
        return $this->error;
    }
}
