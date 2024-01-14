<?php
	class DBel {
		private $conn;
		private $stmt;

		function __construct(){
			$this->conn = new PDO("mysql:host=".A.";dbname=".D.";charset=utf8mb4;", B, C);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		}

		public function q($st, $binder = 0, $binders = 0, $return_success = 0) {
			try {
				$this->stmt = $this->conn->prepare($st);

				if($binder > 0) {
					foreach(array_keys($binders) as $binder) {
						$this->stmt->bindParam($binder, $binders[$binder]);
					}
				}

				$success = 0;

				if($this->stmt->execute()) { $success = 1;}

			    $this->stmt->setFetchMode(PDO::FETCH_ASSOC); 
			    if($return_success == 1) {
			    	return array($this->stmt->fetchAll(), $success);
			    } else {
			    	return $this->stmt->fetchAll();
			    }
			} catch(PDOException $e) {
	   			echo "Error: " . $e->getMessage();
			}

		}

		public function q_with_array($query, $ids, $orderby = "") {
			$in = join(',', array_fill(0, count($ids), '?'));

			$order_clause = "";
			if(!empty($orderby)) {
				$order_clause = " ORDER BY FIND_IN_SET(". $orderby .", '".join(',',$ids)."')";
			}

			$bindarray = array(1 => $ids[0]);

			for($i=1;$i<count($ids);$i++) {
				$bindarray[] = $ids[$i];
			}

			$genre = $this->q($query . "(" . $in . ")" . $order_clause, count($ids), $bindarray);

			return $genre;
		}

		function __destruct() {
			$this->conn = null;
		}
	}
?>