<?php

class MidtermDao {

    private $conn;

    /**
    * constructor of dao class
    */
    public function __construct(){
        try {

        /** TODO
        * List parameters such as servername, username, password, schema. Make sure to use appropriate port
        */
        $host = 'db-mysql-nyc1-54161-do-user-3246313-0.b.db.ondigitalocean.com';
        $database = 'midterm-2023'; 
        $username = 'doadmin';
        $password = 'AVNS_8zpdyL4XMeOPlFwrH1b';
        $port = '25060';


        /*options array neccessary to enable ssl mode - do not change*/
        $options = array(
        	PDO::MYSQL_ATTR_SSL_CA => 'https://drive.google.com/file/d/1IUuXFceXGAH_rydvtJwW5jYzlnZ9FWBw/view?usp=sharing',
        	PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,

        );
        /** TODO
        * Create new connection
        * Use $options array as last parameter to new PDO call after the password
        */
        $this->conn = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password,$options);

        // set the PDO error mode to exception
          $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          echo "Connected successfully";
        } catch(PDOException $e) {
          echo "Connection failed: " . $e->getMessage();
        }
    }

    /** TODO
    * Implement DAO method used to get cap table
    */
    public function cap_table(){
      $query = "SELECT sc.description AS class, scc.description AS category, 
      GROUP_CONCAT(CONCAT('{investor: \"', i.first_name, ' ', i.last_name, '\", diluted_shares: ', ct.diluted_shares, '}') SEPARATOR ', ') AS investors
      FROM cap_table ct
      JOIN share_classes sc ON ct.share_class_id = sc.id
      JOIN share_class_categories scc ON ct.share_class_category_id = scc.id
      JOIN investors i ON ct.investor_id = i.id
      GROUP BY sc.id, scc.id
      ORDER BY sc.id, scc.id";

      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** TODO
    * Implement DAO method used to add cap table record
    */
    public function add_cap_table_record($record){
      $stmt = $this->conn->prepare("INSERT INTO cap_table (share_class_id, share_class_category_id, investor_id, diluted_shares) VALUES
      (:share_class_id, :share_class_category_id, :investor_id, :diluted_shares);");
      $stmt->execute($record);
      $record['id'] = $this->conn->lastInsertId();
      return $record;

    }

    /** TODO
    * Implement DAO method to return list of categories with total shares amount
    */
    public function categories(){
      $stmt = $this->conn->prepare("SELECT scc.description as category, SUM(ct.diluted_shares) as total_shares
      FROM cap_table ct
      JOIN share_class_categories scc
      ON ct.share_class_category_id = scc.id
      GROUP BY ct.share_class_category_id;");
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** TODO
    * Implement DAO method to delete investor
    */
    public function delete_investor($id){
      $stmt = $this->conn->prepare("DELETE FROM investors WHERE id = :id");
      $stmt->bindParam(":id",$id); //prevents an SQL injection
      $stmt->execute();
    }
}
?>
