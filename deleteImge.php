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
elseif(!isset($data->question_id) 
    || !isset($data->date)
    || !isset($data->site_id)
    || !isset($data->supervisor_id)
    || empty(trim($data->question_id))
    || empty(trim($data->date))
    || empty(trim($data->site_id))
    || empty(trim($data->supervisor_id))
    ):

    $fields = ['fields' => ['question_id', 'date', 'site_id', 'supervisor_id']];
    $returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else:
    
    $question_id = trim($data->question_id);
    $date = trim($data->date);
    $site_id = trim($data->site_id);
    $supervisor_id = trim($data->supervisor_id);
    
        try{
            $select = "SELECT * FROM images WHERE 
            question_id = :question_id AND date = :date AND site_id = :site_id AND supervisor_id = :supervisor_id";
            
            $select_stmt = $conn->prepare($select);
            
            // DATA BINDING
            $select_stmt->bindValue(':question_id', $question_id,PDO::PARAM_STR);
            $select_stmt->bindValue(':date', $date,PDO::PARAM_STR);
            $select_stmt->bindValue(':site_id', $site_id,PDO::PARAM_STR);
            $select_stmt->bindValue(':supervisor_id', $supervisor_id,PDO::PARAM_STR);
            $select_stmt->execute();
            
            $row = $select_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($row as $row){
            
            $row = $row['image'];
            
            unlink("pictures/$row");
            }
            

            $delete_query = "DELETE FROM images WHERE 
            question_id = :question_id AND date = :date AND site_id = :site_id AND supervisor_id = :supervisor_id";

            $delete_stmt = $conn->prepare($delete_query);

            // DATA BINDING
            $delete_stmt->bindValue(':question_id', $question_id,PDO::PARAM_STR);
            $delete_stmt->bindValue(':date', $date,PDO::PARAM_STR);
            $delete_stmt->bindValue(':site_id', $site_id,PDO::PARAM_STR);
            $delete_stmt->bindValue(':supervisor_id', $supervisor_id,PDO::PARAM_STR);
            $delete_stmt->execute();
            
            if($delete_stmt->rowcount()){
            
                
                $returnData = msg(1,201,'You have successfully Deleted the Image.');
            
            } else {
                $returnData = msg(0,404,'ERROR');
            }
            }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
        }
    endif;

echo json_encode($returnData);

?>