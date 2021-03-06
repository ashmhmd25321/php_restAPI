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

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));
$returnData = [];

// IF REQUEST METHOD IS NOT POST
if($_SERVER["REQUEST_METHOD"] != "POST"):
    $returnData = msg(0,404,'Page Not Found!');

// IF THERE ARE NO EMPTY FIELDS THEN-
else:
    
    $question_id = $_POST['question_id'];
    $question_rating = $_POST['question_rating'];
    $note = $_POST['note'];
    $isImageInserted = $_POST['isImageInserted'];
    $date = $_POST['date'];
    $site_id = $_POST['site_id'];
    $supervisor_id = $_POST['supervisor_id'];
    $inserted = $_POST['inserted'];
    
        try{

            // INCLUDING DATABASE AND MAKING OBJECT
            $db = mysqli_connect('localhost', 'id18071921_ashmhmd', 'V*Lh)L2*2OhtK6t^', 'id18071921_project_db');

            $update_query = $db->query("UPDATE
                                    question_rate
                                        SET
                                        question_id = '".$question_id."', 
                                        question_rating = '".$question_rating."', 
                                        note = '".$note."', 
                                        isImageInserted = '".$isImageInserted."', 
                                        date = '".$date."',
                                        site_id = '".$site_id."',
                                        supervisor_id = '".$supervisor_id."',
                                        inserted = '".inserted."'
                                        WHERE 
                                        question_id = '".$question_id."' AND date = '".$date."' AND site_id = '".$site_id."' AND supervisor_id = '".$supervisor_id."' AND inserted = 1");

            if($update_query){
                
                $delete = $db->query("DELETE q1 FROM question_rate q1
                                            INNER  JOIN question_rate q2
                                            WHERE
                                                q1.rate_id < q2.rate_id AND
                                                q1.question_id = q2.question_id AND
                                                q1.date = q2.date AND
                                                q1.site_id = q2.site_id AND
                                                q1.question_rating = q2.question_rating AND
                                                q1.note = q2.note AND
                                                q1.isImageInserted = q2.isImageInserted AND
                                                q1.inserted = q2.inserted");
                                                

            $image[] = $_FILES['image']['name'];

            $tmpFile[] = $_FILES['image']['tmp_name'];

            $selectI = $db->query("SELECT * FROM images WHERE question_id = '".$question_id."' AND date = '".$date."' AND site_id = '".$site_id."' AND supervisor_id = '".$supervisor_id."'");

            $row = $delete->fetch_assoc();

            $imageId = $row['id'];

            foreach($image as $key => $value){
                foreach($tmpFile as $key => $tmpFilevalue){
                    if(move_uploaded_file($tmpFilevalue, 'pictures/'.$value)){
                        $save = $db->query("UPDATE images SET image = '".$value."', site_id = '".$site_id."', supervisor_id = '".$supervisor_id."', question_id = '".$question_id."', date = '".$date."' WHERE id = '".$imageId."'");

                        if($save){
                            echo json_encode(array("message"=>"Success"));
                        }else{
                            echo json_encode(array("message"=>"ERROR ".mysqli_error($db)));
                        }
                    }
                }
            }

            if($update_stmt->rowCount()):

            $returnData = msg(1,201,'You have successfully Updated the question.');
            echo json_encode($returnData);
    

            else:
                echo "Invalid data or You have already updated the question";
            endif;

        }
    }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
            echo json_encode($returnData);
        }
    endif;

?>