<?php

namespace Micheledef\PdoSessionHandler;

use \SessionHandlerInterface;
use \PDO;

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
 
     public function open($savePath, $sessionName): bool{
         $this->sessionName = $sessionName;
         return true;
     }
 
 
     public function close(): bool{
         $this->pdo = null;
         return true;
     }
 
 
     public function read($id){
         $sql = "SELECT value FROM session WHERE name = :name AND id = :id";
         $sth = $this->pdo->prepare($sql);
         $sth->execute([":name" => $this->sessionName, ":id" => $id]);
         $result = $sth->fetch(PDO::FETCH_ASSOC);
         return !isset($result["value"]) ? "" : $result["value"];
     }
 
     public function write($id, $value): bool{
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
 
     public function destroy($id): bool{
         $sql = "DELETE FROM session WHERE name = :name and id = :id";
         $sth = $this->pdo->prepare($sql);
         return $sth->execute([":name" => $this->sessionName, ":id" => $id]);
     }
 
     public function gc($maxlifetime){
         $sql = "DELETE FROM session WHERE last_update < :lifetime";
         $sth = $this->pdo->prepare($sql);
         return $sth->execute([
             ":lifetime" => strtotime(date("Y-m-d H:i:s")) - $maxlifetime,
         ]);
     }
 }