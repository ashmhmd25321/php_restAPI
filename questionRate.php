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
    || empty(trim($data->question_rating))
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

            $insert_query = "INSERT INTO `question_rate`(`question_id`, `question_rating`, `note`, `image`, `date`, `site_id`, `supervisor_id`) VALUES (:question_id,:question_rating,:note,:image,:date,:site_id,:supervisor_id)";

            $insert_stmt = $conn->prepare($insert_query);

            // DATA BINDING
            $insert_stmt->bindValue(':question_id', $question_id,PDO::PARAM_STR);
            $insert_stmt->bindValue(':question_rating', $question_rating,PDO::PARAM_STR);
            $insert_stmt->bindValue(':note', $note,PDO::PARAM_STR);
            $insert_stmt->bindValue(':image', $image,PDO::PARAM_STR);
            $insert_stmt->bindValue(':date', $date,PDO::PARAM_STR);
            $insert_stmt->bindValue(':site_id', $site_id,PDO::PARAM_STR);
            $insert_stmt->bindValue(':supervisor_id', $supervisor_id,PDO::PARAM_STR);

            $insert_stmt->execute();

            $returnData = msg(1,201,'You have successfully Answered the question.');

        }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
        }
    endif;

echo json_encode($returnData);

?>