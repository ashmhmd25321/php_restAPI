<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__.'/classes/Database.php';

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}

$db_connection = new Database();
$conn = $db_connection->dbConnection();

$data = json_decode(file_get_contents("php://input"));
$returnData = [];

// IF REQUEST METHOD IS NOT EQUAL TO POST
if($_SERVER["REQUEST_METHOD"] != "POST"):
    $returnData = msg(0,404,'Page Not Found!');


else:

    $question_id = trim($data->question_id);
    $date = ($data->date);
    $site_id = trim($data->site_id);
    $supervisor_id = trim($data->supervisor_id);

        try{
            
            $fetch_isUpdate = "SELECT * FROM images WHERE question_id = :question_id && date = :date && site_id = :site_id && supervisor_id = :supervisor_id";
            $query_stmt = $conn->prepare($fetch_isUpdate);
            $query_stmt->bindValue(':question_id', $question_id,PDO::PARAM_STR);
            $query_stmt->bindValue(':date', $date,PDO::PARAM_STR);
            $query_stmt->bindValue(':site_id', $site_id,PDO::PARAM_STR);
            $query_stmt->bindValue(':supervisor_id', $supervisor_id,PDO::PARAM_STR);
            $query_stmt->execute();

            // IF THE USER IS FOUNDED BY EMAIL
            if($query_stmt->rowCount()){
                $row = $query_stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($row as $row){
                    $row = $row['isUpdated'];

                    if($row = 1){
                        $update = "UPDATE images SET isUpdated = 0 WHERE question_id = :question_id && date = :date && site_id = :site_id && supervisor_id = :supervisor_id";
                        $query_stmt2 = $conn->prepare($update);
                        $query_stmt2->bindValue(':question_id', $question_id,PDO::PARAM_STR);
                        $query_stmt2->bindValue(':date', $date,PDO::PARAM_STR);
                        $query_stmt2->bindValue(':site_id', $site_id,PDO::PARAM_STR);
                        $query_stmt2->bindValue(':supervisor_id', $supervisor_id,PDO::PARAM_STR);
                        $query_stmt2->execute();
                    }
                }
                
                }
                else{
                    $returnData = msg(0,422,'Invalid');
                    echo json_encode($returnData);
             }
            
        }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
            echo json_encode($returnData);
        }

    endif;

?>