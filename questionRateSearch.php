<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"));
$returnData = [];

function msg($success,$extra = []){
    return array_merge([
        'inserted' => $success
    ],$extra);
}

// IF REQUEST METHOD IS NOT EQUAL TO POST
if($_SERVER["REQUEST_METHOD"] != "POST"):
    $returnData = msg(0,404,'Page Not Found!');

// IF THERE ARE NO EMPTY FIELDS THEN-
else:
    $question_id = $_POST['question_id'];
    $date = $_POST['date'];
    $site_id = $_POST['site_id'];
    $supervisor_id = $_POST['supervisor_id'];

        try{

            // INCLUDING DATABASE AND MAKING OBJECT
            $db = mysqli_connect('localhost', 'root', '', 'php_auth_api');
            
            $select =  $db->query("SELECT * FROM question_rate WHERE question_id = $question_id && date = $date && site_id = $site_id && supervisor_id = $supervisor_id");
            
            $list = array();

            while($rowdata= $select->fetch_assoc()) {
                $list[] = $rowdata;
            }

            echo json_encode($list);
        }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());

            echo json_encode($returnData);

        }

    endif;




?>