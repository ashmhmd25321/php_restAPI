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
            
            $fetch_site_by_supervisor = "SELECT super_site.site_id, supervisor_site.site_id, supervisor_site.site_name, supervisor_site.site_address FROM super_site INNER JOIN supervisor_site ON super_site.site_id = supervisor_site.site_id INNER JOIN `supervisor` ON super_site.supervisor_id = supervisor.supervisor_id WHERE `super_site`.`supervisor_id`=:supervisor_id"; 
            $query_stmt = $conn->prepare($fetch_site_by_supervisor);
            $query_stmt->bindValue(':supervisor_id', $supervisor_id,PDO::PARAM_STR);
            $query_stmt->execute();

            // IF THE USER IS FOUNDED BY EMAIL
            if($query_stmt->rowCount()):
                $row = $query_stmt->fetchAll(PDO::FETCH_ASSOC);
               
                    // $returnData = [
                    //     'success' => 1,
                    //     'message' => 'Success',
                    //     'sites' => $row
                    // ];
                    

                // IF INVALID
                else:
                    $returnData = msg(0,422);
                endif;
        }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
        }

    endif;

echo json_encode($row);


?>