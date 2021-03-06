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

// IF REQUEST METHOD IS NOT EQUAL TO POST
if($_SERVER["REQUEST_METHOD"] != "POST"):
    $returnData = msg(0,404,'Page Not Found!');

    // CHECKING EMPTY FIELDS
elseif(!isset($data->supervisor_id) 
|| empty(trim($data->supervisor_id))
):

$fields = ['fields' => ['supervisor_id']];
$returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else:

    $supervisor_id = trim($data->supervisor_id);

        try{
            
            $fetch_questionRate_by_id = "SELECT * FROM supervisor WHERE supervisor_id = :supervisor_id";
            $query_stmt = $conn->prepare($fetch_questionRate_by_id);
            $query_stmt->bindValue(':supervisor_id', $supervisor_id,PDO::PARAM_STR);
            $query_stmt->execute();

            // IF THE question rates are found
            if($query_stmt->rowCount()):
                $row = $query_stmt->fetch(PDO::FETCH_ASSOC);
                $row = array($row);

                echo json_encode($row);
                    
                // IF INVALID
                else:
                    echo "Invalid";   
                endif;
        }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());

            echo json_encode($returnData);

        }

    endif;




?>