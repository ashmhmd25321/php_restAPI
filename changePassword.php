<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}

// INCLUDING DATABASE AND MAKING OBJECT
require __DIR__.'/classes/Database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));
$returnData = [];

// IF REQUEST METHOD IS NOT POST
if($_SERVER["REQUEST_METHOD"] != "POST"):
    $returnData = msg(0,404,'Page Not Found!');

// CHECKING EMPTY FIELDS
elseif(!isset($data->supervisor_id) 
    || !isset($data->current_password)
    || !isset($data->confirm_current_password)
    || !isset($data->new_password)
    || empty(trim($data->supervisor_id))
    || empty(trim($data->current_password))
    || empty(trim($data->confirm_current_password))
    || empty(trim($data->new_password))
    ):

    $fields = ['fields' => ['supervisor_id','current_password','confirm_new_password', 'new_password']];
    $returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else:
    
    $supervisor_id = trim($data->supervisor_id);
    $current_password = trim($data->current_password);
    $confirm_new_password = trim($data->confirm_new_password);
    $new_password = ($data->new_password);

    if(strlen($new_password) < 8){
        echo "Your password must be atleast 8 characters long";
    }else{

    $current_password = md5($current_password);
    $confirm_new_password = md5($confirm_new_password);
    $new_password = md5($new_password);
    
        try{

                $select_query =  "SELECT * FROM supervisor WHERE supervisor_id= :supervisor_id";
                $query_stmt = $conn->prepare($select_query);
                $query_stmt->bindValue(':supervisor_id', $supervisor_id,PDO::PARAM_STR);
                $query_stmt->execute();

                //if the user is found
                if($query_stmt->rowCount()){
                    $row = $query_stmt->fetch(PDO::FETCH_ASSOC);
                    $fetchpass = $row['password'];
                    $passmd5 = $current_password;

                    // VERIFYING THE PASSWORD (IS CORRECT OR NOT?)
                    if($fetchpass == $passmd5){
                        
                        if($new_password == $confirm_new_password){

                            $update_query = "UPDATE
                                                    supervisor
                                                        SET
                                                        password = :new_password
                                                        WHERE 
                                                        supervisor_id = :supervisor_id";

                            $update_stmt = $conn->prepare($update_query);
                            // DATA BINDING
                            $update_stmt->bindValue(':new_password', $new_password, PDO::PARAM_STR);
                            $update_stmt->bindValue(':supervisor_id', $supervisor_id,PDO::PARAM_STR);
                            $update_stmt->execute();

                            if($update_stmt->rowCount()){

                                $returnData = msg(1,201,'You have successfully Updated the Password.');
                                echo json_encode($returnData);
                        
                    
                            }else{
                                    echo "You have already updated the Password";
                            }
                        }else {
                            echo "New password doesn't match with confirm password";
                        }
                }else{
                    echo "Password doesn't match";
                }
            }else{
                echo "User not found";
            }
        }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
            echo json_encode($returnData);
        }
    }
    endif;

?>