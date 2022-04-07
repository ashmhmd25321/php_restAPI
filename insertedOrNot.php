<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__.'/classes/Database.php';

$db_connection = new Database();
$conn = $db_connection->dbConnection();

$data = json_decode(file_get_contents("php://input"));
$returnData = [];

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'question_id' => $status,
        'inserted' => $message
    ],$extra);
}

// IF REQUEST METHOD IS NOT EQUAL TO POST
if($_SERVER["REQUEST_METHOD"] != "POST"):
    $returnData = msg(0,404,'Page Not Found!');

    // CHECKING EMPTY FIELDS
elseif(!isset($data->site_id) 
|| empty(trim($data->site_id))
):

$fields = ['fields' => ['site_id', 'supervisor_id', 'date']];
$returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else:
    $site_id = trim($data->site_id);
    $supervisor_id = trim($data->supervisor_id);
    $date = ($data->date);

        try{
            $fetch_questionRate_by_id = "SELECT inserted FROM question_rate WHERE `question_rate`.`date` = :date && `question_rate`.`site_id` = :site_id && `question_rate`.`supervisor_id` = :supervisor_id";
            $query_stmt = $conn->prepare($fetch_questionRate_by_id);
            $query_stmt->bindValue(':date', $date,PDO::PARAM_STR);
            $query_stmt->bindValue(':site_id', $site_id,PDO::PARAM_STR);
            $query_stmt->bindValue(':supervisor_id', $supervisor_id,PDO::PARAM_STR);
            $query_stmt->execute();

            // IF THE USER IS FOUNDED BY EMAIL
            if($query_stmt->rowCount()){
                $row = $query_stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $row = json_encode($row);
                echo $row;

                // IF INVALID
                }else{
                    $returnData = msg(0,NULL,NULL);
                    echo json_encode($returnData);
                }
        }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
            echo json_encode($returnData);
        }

    endif;

?>