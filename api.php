<?php


require 'vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use GuzzleHttp\Client;

class DB{
    private $bd;

    function __construct(){
        $this->bd = new mysqli("localhost", "dks", "29121990marie", "topup");
        if ($this->bd->connect_errno) {
            echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            die("");
        }/*else{
            echo $bd->host_info . "\n";
        }*/
    }

    public function getConnexionDB(){
        return $this->bd;
    }
}

class Api{

    private $connexionDB;

    function __construct(){
        $bd = new DB();
        $this->connexionDB = $bd->getConnexionDB();
    }

    public function subscription(){
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://httpbin.org',
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);
        
        // Create a client with a base URI
        $client = new GuzzleHttp\Client(['base_uri' => 'https://bosdia.com/TopUp/api/']);
        // Provide the body as a string.
        $response = $client->request('POST', 'top_up', ['json' => [
            "api_user_name" => "emmanuelapi",
            "api_password" => "emmanuelapi@1000",
            "destination_msisdn" => "2347088887979",
            "product" => "50"
        ]]);
        // var_dump($request);
        if ($response->getBody()) {
            // echo $response->getBody();
            var_dump($response->getBody()->getContents());
            // var_dump($response->getStatusCode());
            // JSON string: { ... }
        }
        
    }

    public function getAllReseau(){
        $sql = "Select * from t_reseau";
        $result = $this->connexionDB->query($sql);
        $data = [];
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()) {
                $data[] = ["id" => $row["id"], "libelle" => $row["libelle"]];
            }
        }
        return json_encode($data);
    }

    public function getAllForfaitsByReseau($reseau_id){
        $sql = "Select * from t_forfait where reseau_id=".$reseau_id;
        $result = $this->connexionDB->query($sql);
        $data = [];
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()) {
                $data[] = ["id" => $row["id"], "prix_xof" => $row["prix_xof"], 
                "prix_usd" => $row["prix_usd"], "reseau_id" => $row["reseau_id"]];
            }
        }
        return json_encode($data);
    }

    public function saveSubscription($data){
        $this->subscription();
        $response = null;
        $sql = "INSERT INTO t_souscription (reseau_id, forfait_id, numeros)
        VALUES ('".$data['reseau_id']."', '".$data['forfait_id']."', '".$data['numeros']."')";
        if($this->connexionDB->query($sql) === TRUE){
            $response = ["id" => $this->connexionDB->insert_id, "reseau_id" => $data['reseau_id'] , 
            "forfait_id" => $data['forfait_id'], "numeros" => $data['numeros']];
        }else{
            $response = "Error: " . $sql . "<br>" . $this->connexionDB->error;
        }
        return json_encode($response);
    }

    
}

$api = new Api();

if($_GET){
    // recupere action 
    $action = $_GET['action'];
    if($action == "get-all-reseau"){
        echo $api->getAllReseau();
    }else if($action == "get-all-forfait-by-resau"){
        // recupere reseau_id
        $reseau_id = $_GET['reseau_id'];
        echo $api->getAllForfaitsByReseau($reseau_id);
    }else if($action == "save-subscription"){
        $data = json_decode(file_get_contents('php://input'), true);
        echo $api->saveSubscription($data);
    }
}

?>