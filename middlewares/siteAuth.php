<?php
require __DIR__.'/../classes/JwtHandler.php';
class Auth extends JwtHandler{

    protected $db;
    protected $headers;
    protected $token;
    public function __construct($db,$headers) {
        parent::__construct();
        $this->db = $db;
        $this->headers = $headers;
    }

    protected function fetchUser($user_id){
         try{

             $fields = ['fields' => ['id']];
             $fetch_site_by_id = "SELECT supervisor_sites.site_name, supervisor_sites.site_id FROM supervisor_sites INNER JOIN users ON supervisor_sites.supervisor_id = users.id WHERE users.id = :id";
             $query_stmt1 = $this->db->prepare($fetch_site_by_id);
             $query_stmt1->bindValue(':id', $user_id,PDO::PARAM_INT);
             $query_stmt1->execute();

             if($query_stmt1->rowCount()):
                 $row1 = $query_stmt1->fetchAll(PDO::FETCH_ASSOC);
                
                 return [
                     'sites' => $row1
                 ];
             else:
                 return null;
             endif;
         }
         catch(PDOException $e){
             return null;
         }
     }      

}