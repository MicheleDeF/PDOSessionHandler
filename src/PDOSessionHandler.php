<?php

namespace Micheledef\PdoSessionHandler;

/**
 * PDOSessionHandler class
 * 
 * @author Michele De Falco <https://github.com/MicheleDeF>
 * 
 * @license https://github.com/MicheleDeF/PDOSessionHandler/blob/main/LICENSE MIT License
 * 
 * @tutorial https://github.com/MicheleDeF/PDOSessionHandler
 * 
 * @version v1.0.0
 */

class PDOSessionHandler implements SessionHandlerInterface
{
    private $pdo;
    private $sessionName;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    /**
     * open function
     * 
     * @param string $savePath
     * 
     * @param string $sessionName
     * 
     * @return bool
     * 
     */

    public function open(string $savePath, string $sessionName): bool{
        $this->sessionName = $sessionName;
        return true;
    }


   /**
     * close function
     * 
     * @return bool
     * 
     */

    public function close(): bool{
        $this->pdo = null;
        return true;
    }


   /**
     * read function
     * 
     * @param string $id
     *  
     * @return string|false
     * 
     */

    public function read(string $id): string|false{
        $sql = "SELECT value FROM session WHERE name = :name AND id = :id";
        $sth = $this->pdo->prepare($sql);
        $sth->execute([":name" => $this->sessionName, ":id" => $id]);
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return !isset($result["value"]) ? "" : $result["value"];
    }

   /**
     * write function
     * 
     * @param string $id
     * 
     * @param string $value
     *  
     * @return bool
     * 
     */

    public function write(string $id, string $value): bool{
        $sql = "SELECT value FROM session WHERE name = :name AND id = :id";
        $sth = $this->pdo->prepare($sql);
        $sth->execute([":name" => $this->sessionName, ":id" => $id]);

        if (count($sth->fetchAll()) == 0) {
            $sql =
                "INSERT INTO session (id, name, value, last_update) values (:id, :name, :value, :last_update)";
        } else {
            $sql =
                "UPDATE session SET value = :value, last_update = :last_update WHERE id = :id AND name = :name";
        }

        $sth = $this->pdo->prepare($sql);

        return $sth->execute([
            ":id" => $id,
            ":name" => $this->sessionName,
            ":value" => $value,
            ":last_update" => strtotime(date("Y-m-d H:i:s")),
        ]);
    }

   /**
     * destroy function
     * 
     * @param string $id
     *  
     * @return bool
     * 
     */

    public function destroy(string $id): bool{
        $sql = "DELETE FROM session WHERE name = :name and id = :id";
        $sth = $this->pdo->prepare($sql);
        return $sth->execute([":name" => $this->sessionName, ":id" => $id]);
    }


   /**
     * gc function
     * 
     * @param int $maxlifetime
     *  
     * @return int|false
     * 
     */

    public function gc(int $maxlifetime): int|false{
        $sql = "DELETE FROM session WHERE last_update < :lifetime";
        $sth = $this->pdo->prepare($sql);
        return $sth->execute([
            ":lifetime" => strtotime(date("Y-m-d H:i:s")) - $maxlifetime,
        ]);
    }
}