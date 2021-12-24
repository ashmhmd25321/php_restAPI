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
    || !isset($data->question_rating)
    || !isset($data->date)
    || !isset($data->site_id)
    || !isset($data->supervisor_id)
    || empty(trim($data->question_id))
    || empty(trim($data->date))
    || empty(trim($data->site_id))
    || empty(trim($data->supervisor_id))
    ):

    $fields = ['fields' => ['question_id','question_rating','note', 'image', 'date', 'site_id', 'supervisor_id']];
    $returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else:
    
    $question_id = trim($data->question_id);
    $question_rating = trim($data->question_rating);
    $note = trim($data->note);
    $image = ($data->image);
    $date = ($data->date);
    $site_id = trim($data->site_id);
    $supervisor_id = trim($data->supervisor_id);
    
        try{

            $update_query = "UPDATE
                                    question_rate
                                        SET
                                        question_id = :question_id, 
                                        question_rating = :question_rating, 
                                        note = :note, 
                                        image = :image, 
                                        date = :date,
                                        site_id = :site_id,
                                        supervisor_id = :supervisor_id
                                        WHERE 
                                        question_id = :question_id AND date = :date AND site_id = :site_id AND supervisor_id = :supervisor_id";

            $update_stmt = $conn->prepare($update_query);
            // DATA BINDING
            $update_stmt->bindValue(':question_id', $question_id,PDO::PARAM_STR);
            $update_stmt->bindValue(':question_rating', $question_rating,PDO::PARAM_STR);
            $update_stmt->bindValue(':note', $note,PDO::PARAM_STR);
            $update_stmt->bindValue(':image', $image,PDO::PARAM_STR);
            $update_stmt->bindValue(':date', $date,PDO::PARAM_STR);
            $update_stmt->bindValue(':site_id', $site_id,PDO::PARAM_STR);
            $update_stmt->bindValue(':supervisor_id', $supervisor_id,PDO::PARAM_STR);
            $update_stmt->execute();

            if($update_stmt->rowCount()):

            $returnData = msg(1,201,'You have successfully Updated the question.');
            echo json_encode($returnData);
    

            else:
                echo "Invalid data or You have already updated the question";
            endif;

        }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
            echo json_encode($returnData);
        }
    endif;

?>