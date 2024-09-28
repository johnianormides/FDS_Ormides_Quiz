<?php

interface CrudInterface{
    public function getAll();
    public function getOne();
    public function insert();
    public function update();
    public function delete();
}

class crud{

    protected $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function getAll(){
        $sql = "SELECT * FROM users";
        try{
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute()){
                $data =  $stmt->fetchAll();
                if ($stmt->rowCount() > 0){
                    return $data;
                }else{
                    http_response_code(404);
                    return 'There are no data present';
                }
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    } 

    public function getOne($data){
        $sql = "SELECT * FROM users WHERE User_ID = ?";
        try{
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->User_ID])){
                $data =  $stmt->fetchAll();
                if ($stmt->rowCount() > 0){
                    return $data;
                }else{
                    http_response_code(404);
                    return 'User does not exist';
                }
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function insert($data){
        $sql = 'INSERT INTO users(FirstName, LastName, is_Admin) VALUES(?, ?, Default)';

        if (!isset($data->FirstName) || !isset($data->LastName)) {
            return "Error: FirstName and LastName are required fields.";
        }

        if (empty($data->FirstName) || empty($data->LastName)) {
            return "Error: FirstName and LastName cannot be empty.";
        }

        try{
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->FirstName, $data->LastName])){
                $lastID = $this->pdo->lastInsertId();
                echo json_encode(["msg"=>"Data successfully inserted"]);
                return $this->getOne((object)['User_ID'=>$lastID]);
            }else{
                echo json_encode(["msg"=>"Data unsuccessfully inserted"]);
            }
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function update($data){
        $sql = "UPDATE users SET is_Admin = CASE WHEN is_Admin = 0 THEN 1 WHEN is_Admin = 1 THEN 0 END WHERE User_ID = ?";

        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->User_ID])) {
                if ($stmt->rowCount() > 0){
                    echo json_encode(["message"=>"Data successfully updated"]);
                    return $this->getOne((object)['User_ID' => $data->User_ID]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message"=>"Id or User does not exist"]);
                }
            }
        } catch (PDOException $e) {
            return $e->getMessage();  
        }
    } 

    public function delete($data){
        $sql = "DELETE FROM users WHERE User_ID = ?";
    
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute([$data->User_ID])) {
                if ($stmt->rowCount() > 0){
                echo json_encode(["message"=>"User successfully deleted"]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message"=>"User doesn't exist or is already deleted"]);
                }
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
}