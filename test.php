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
            $select = "SELECT type, SUM(question_rating) FROM question_rate WHERE date = :date && supervisor_id = :supervisor_id && site_id = :site_id";
            $query_stmt0 = $conn->prepare($select);
            $query_stmt0->bindValue(':site_id', $site_id,PDO::PARAM_STR);
            $query_stmt0->bindValue(':date', $date,PDO::PARAM_STR);
            $query_stmt0->bindValue(':supervisor_id', $supervisor_id,PDO::PARAM_STR);
            

            if($query_stmt0->execute()){

            while($row = $query_stmt0->fetch(PDO::FETCH_ASSOC)){
                echo json_encode("Total_rating: ". $row['SUM(question_rating)']);
            }
        }
        else{
            echo json_encode('ERROR');
        }
        }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
            echo json_encode($returnData);
        }

    endif;

?>