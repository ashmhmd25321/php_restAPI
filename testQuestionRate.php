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
            $db = mysqli_connect('localhost', 'root', '', 'php_auth_api');

            $insert_query = $db->query("INSERT INTO question_rate(question_id, question_rating, note, isImageInserted, date, site_id, supervisor_id, inserted) VALUES ('".$question_id."','".$question_rating."','".$note."','".$isImageInserted."','".$date."','".$site_id."','".$supervisor_id."','".$inserted."')");
            
            if($insert_query){
                
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

                                                if($delete){
                                                    echo json_encode(array("message"=>"Delete Success"));
                                                }else{
                                                    echo json_encode(array("message"=>"ERROR ".mysqli_error($db)));
                                                }

            $image[] = $_FILES['image']['name'];

            $tmpFile[] = $_FILES['image']['tmp_name'];

            foreach($image as $key => $value){
                foreach($tmpFile as $key => $tmpFilevalue){
                    if(move_uploaded_file($tmpFilevalue, 'pictures/'.$value)){
                        $save = $db->query("INSERT INTO images (image, site_id, supervisor_id, question_id, date) VALUES ('".$value."', '".$site_id."', '".$supervisor_id."', '".$question_id."', '".$date."')");

                        if($save){
                            echo json_encode(array("message"=>"Success"));
                        }else{
                            echo json_encode(array("message"=>"ERROR ".mysqli_error($db)));
                        }
                    }
                }
            }

            $returnData = msg(1,201,'You have successfully Answered the question.');
            $insert = $db->query("INSERT INTO question_rate_finished (`question_id`, `question_rating`, `note`, `isImageInserted`, `date`, `site_id`, `supervisor_id`, `inserted`) SELECT `question_id`, `question_rating`, `note`, `isImageInserted`, `date`, `site_id`, `supervisor_id`, `inserted` FROM question_rate");

            $delete2 = $db->query("DELETE q1 FROM question_rate_finished q1
                                            INNER  JOIN question_rate_finished q2
                                            WHERE
                                                q1.rate_id < q2.rate_id AND
                                                q1.question_id = q2.question_id AND
                                                q1.date = q2.date AND
                                                q1.site_id = q2.site_id AND
                                                q1.question_rating = q2.question_rating AND
                                                q1.note = q2.note AND
                                                q1.isImageInserted = q2.isImageInserted AND
                                                q1.inserted = q2.inserted");
            
            }else {
                echo json_encode(array("message"=>"ERROR ".mysqli_error($db)));
            }
            

        }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
        }
    endif;

echo json_encode($returnData);

?>