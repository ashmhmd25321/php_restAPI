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
elseif(!isset($data->site_id)   
|| empty(trim($data->site_id))
):

$fields = ['fields' => ['site_id', 'date', 'supervisor_id']];
$returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else:
    $site_id = trim($data->site_id);
    $supervisor_id = trim($data->supervisor_id);
    $date = ($data->date);

        try{
            
            $fetch_question_by_site = "SELECT question_site.question_id, questions_table.question_id, questions_table.question FROM question_site 
                                        INNER JOIN questions_table ON question_site.question_id = questions_table.question_id 
                                        INNER JOIN `supervisor_site` ON question_site.site_id = supervisor_site.site_id 
                                        WHERE `question_site`.`site_id`=:site_id";
            $query_stmt = $conn->prepare($fetch_question_by_site);
            $query_stmt->bindValue(':site_id', $site_id,PDO::PARAM_STR);
            $query_stmt->execute();

            // IF THE USER IS FOUNDED BY EMAIL
            if($query_stmt->rowCount()){
                $row = $query_stmt->fetchAll(PDO::FETCH_ASSOC);

                
                if($row){
                    foreach($row as $row){
                        $question_id = $row['question_id'];


                        $fetch_questionRate_by_id = "SELECT inserted FROM question_rate 
                                                        WHERE question_id = $question_id && date = :date && site_id = :site_id && supervisor_id = :supervisor_id";
                        $query_stmt1 = $conn->prepare($fetch_questionRate_by_id);
                        $query_stmt1->bindValue(':date', $date,PDO::PARAM_STR);
                        $query_stmt1->bindValue(':site_id', $site_id,PDO::PARAM_STR);
                        $query_stmt1->bindValue(':supervisor_id', $supervisor_id,PDO::PARAM_STR);
                        $query_stmt1->execute();
                        $row1 = $query_stmt1->fetchAll(PDO::FETCH_ASSOC);

                        $row1 = trim(json_encode($row1));
                        $row1 = json_decode($row1);

                        $row1 = array("isInserted" => $row1);

                        $show = array_merge($row, $row1);

                        $show = json_encode($show);

                        $show = print ($show );
                        $show;
                    }
                    
                }
              
                // IF INVALID
             } else{
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