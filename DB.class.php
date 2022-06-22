<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("EMAIL_MSG91_API", "https://control.msg91.com/api/v5/email/send", false);
define("MSG91_AUTH_KEY", "353151AmIpE5c162a1b2a5P1", false);
define("SMS_TEMPLATE_ID", "629fbec6d6fc05795b4cf9d2", false);
define("SEND_VERIFY_EMAIL_FROM", "verify@divyakalash.com", false);
class DB
{
    /* private $dbHost     = "localhost";
    private $dbUsername = "root";
    private $dbPassword = "";
    private $dbName     = "divyakalash_db"; */

    private $dbHost     = "localhost";
    private $dbUsername = "divyakalash_db";
    private $dbPassword = "@Xtct[V[kBjs";
    private $dbName     = "divyakalash_db";

    public function __construct()
    {
        if (!isset($this->db)) {
            // Connect to the database
            $conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
            $conn->set_charset("utf8");
            if ($conn->connect_error) {
                die("Failed to connect with MySQL: " . $conn->connect_error);
            } else {
                $this->db = $conn;
            }
        }
    }

    public function generateRandomDigit($n)
    {
        $generator = "1357902468";
        $result = "";
        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand() % (strlen($generator))), 1);
        }
        return $result;
    }

    public static function getBasePath()
    {
        return 'https://' . $_SERVER['HTTP_HOST'] . '/dvkadmin';
        //return 'http://'.$_SERVER['HTTP_HOST'];
    }

    /*
     * Returns rows from the database based on the conditions
     * @param string name of the table
     * @param array select, where, order_by, limit and return_type conditions
     */
    public function getSearchResult($table, $condtitions)
    {
        $sql = "SELECT * FROM $table $condtitions";
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return !empty($data) ? $data : false;
    }

    public function customQuery($sql)
    {
        $result = $this->db->query($sql);
        @$rows = $result->num_rows;
        if ($rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return empty($data) ? false : $data;
    }

    /*
     * Validate user check if Email id and User id is for same user
     * */
    public function validateUserByUidAndEmail($table)
    {
        if ($_COOKIE['eid'] != '' && isset($_COOKIE['eid']) && $_COOKIE['uid']) {
            $condtitions = 'WHERE id = ' . $_COOKIE['eid'] . ' AND username = \'' . $_COOKIE['uid'] . '\'';

            $sql = "SELECT * FROM $table $condtitions";
            $result = $this->db->query($sql);
            if (!empty($result) && $result->num_rows > 0) {
                return true;
            }
        } else {
            return false;
        }
    }

    /*
     * Validate user check if Email id is in use
     * */
    public function checkEmail($table, $email)
    {
        $condtitions = "WHERE email = '$email'";

        $sql = "SELECT * FROM " . $table . " $condtitions";
        $result = $this->db->query($sql);
        if (!empty($result) && $result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Validate user check if Email id and User id is for same user
     * */
    public function validateUsernameAvailable($username)
    {
        $condtitions = "WHERE username = '" . $username . "'";

        $sql = "SELECT id FROM users $condtitions";
        $result = $this->db->query($sql);
        if (!empty($result) && $result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getRows($table, $conditions = array())
    {
        $sql = 'SELECT ';
        $sql .= array_key_exists("select", $conditions) ? $conditions['select'] : '*';
        $sql .= ' FROM ' . $table;
        if (array_key_exists("where", $conditions)) {
            $sql .= ' WHERE ';
            $i = 0;
            foreach ($conditions['where'] as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $sql .= $pre . $key . " = '" . $value . "'";
                $i++;
            }
        }

        if (array_key_exists("wherenot", $conditions)) {
            $sql .= (strpos($sql, 'WHERE') !== false) ? ' AND ' : ' WHERE ';
            $i = 0;
            foreach ($conditions['wherenot'] as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $sql .= $pre . $key . " != '" . $value . "'";
                $i++;
            }
        }

        if (array_key_exists("like", $conditions) && !empty($conditions['like'])) {
            $sql .= (strpos($sql, 'WHERE') !== false) ? ' AND ' : ' WHERE ';
            $i = 0;
            $likeSQL = '';
            foreach ($conditions['like'] as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $likeSQL .= $pre . $key . " LIKE '%" . $value . "%'";
                $i++;
            }
            $sql .= '(' . $likeSQL . ')';
        }

        if (array_key_exists("like_or", $conditions) && !empty($conditions['like_or'])) {
            $sql .= (strpos($sql, 'WHERE') !== false) ? ' AND ' : ' WHERE ';
            $i = 0;
            $likeSQL = '';
            foreach ($conditions['like_or'] as $key => $value) {
                $pre = ($i > 0) ? ' OR ' : '';
                $likeSQL .= $pre . $key . " LIKE '%" . $value . "%'";
                $i++;
            }
            $sql .= '(' . $likeSQL . ')';
        }

        if (array_key_exists("order_by", $conditions)) {
            $sql .= ' ORDER BY ' . $conditions['order_by'];
        }

        if (array_key_exists("start", $conditions) && array_key_exists("limit", $conditions)) {
            $sql .= ' LIMIT ' . $conditions['start'] . ',' . $conditions['limit'];
        } elseif (!array_key_exists("start", $conditions) && array_key_exists("limit", $conditions)) {
            $sql .= ' LIMIT ' . $conditions['limit'];
        }
        $result = $this->db->query($sql);

        if (array_key_exists("return_type", $conditions) && $conditions['return_type'] != 'all') {
            switch ($conditions['return_type']) {
                case 'count':
                    $data = $result->num_rows;
                    break;
                case 'single':
                    $data = $result->fetch_assoc();
                    break;
                default:
                    $data = '';
            }
        } else {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
        }
        return !empty($data) ? $data : false;
    }

    /*
     * Insert data into the database
     * @param string name of the table
     * @param array the data for inserting into the table
     */
    public function registerUser($table, $data)
    {
        if (!empty($data) && is_array($data)) {
            $columns = '';
            $values  = '';
            $i = 0;
            $empid = $data['emp_id'];
            $email = $data['email'];
            $mobile = $data['mobile'];

            $check_query = "SELECT * FROM " . $table . " WHERE email = '$email' OR email = '$empid' OR mobile = '$mobile'";
            $check_row = $this->db->query($check_query);
            if ($check_row->num_rows == 0) {
                if (!array_key_exists('created_at', $data)) {
                    $data['created_at'] = date("Y-m-d H:i:s");
                }
                if (!array_key_exists('updated_at', $data)) {
                    $data['updated_at'] = date("Y-m-d H:i:s");
                }
                foreach ($data as $key => $val) {
                    $pre = ($i > 0) ? ', ' : '';
                    $columns .= $pre . $key;
                    if (isset($val)) {
                        $values  .= $pre . "'" . $this->db->real_escape_string($val) . "'";
                    }
                    $i++;
                }
                $query = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ")";
                $insert = $this->db->query($query);
                return $insert ? $this->db->insert_id : false;
            } else {
                return "used";
            }
        } else {
            return false;
        }
    }

    /*
     * Insert data into the database
     * @param string name of the table
     * @param array the data for inserting into the table
     */
    public function insert($table, $data)
    {
        if (!empty($data) && is_array($data)) {
            $columns = '';
            $values  = '';
            $i = 0;
            if (!array_key_exists('created_at', $data)) {
                $data['created_at'] = date("Y-m-d H:i:s");
            }
            if (!array_key_exists('updated_at', $data)) {
                $data['updated_at'] = date("Y-m-d H:i:s");
            }
            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $columns .= $pre . $key;
                if (isset($val)) {
                    $values  .= $pre . "'" . $this->db->real_escape_string($val) . "'";
                }
                $i++;
            }
            $query = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ")";
            $insert = $this->db->query($query);
            return $insert ? $this->db->insert_id : false;
        } else {
            return false;
        }
    }

    /*
     * Update data into the database
     * @param string name of the table
     * @param array the data for updating into the table
     * @param array where condition on updating data
     */
    public function update($table, $data, $conditions)
    {
        if (!empty($data) && is_array($data)) {
            $colvalSet = '';
            $whereSql = '';
            $i = 0;
            if (!array_key_exists('updated_at', $data)) {
                $data['updated_at'] = date("Y-m-d H:i:s");
            }
            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $colvalSet .= $pre . $key . "='" . $this->db->real_escape_string($val) . "'";
                $i++;
            }
            if (!empty($conditions) && is_array($conditions)) {
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach ($conditions as $key => $value) {
                    $pre = ($i > 0) ? ' AND ' : '';
                    if ($key == 'id') {
                        $whereSql .= $pre . $key . " = " . $value . "";
                    } else {
                        $whereSql .= $pre . $key . " = '" . $value . "'";
                    }
                    $i++;
                }
            }
            $query = "UPDATE " . $table . " SET " . $colvalSet . $whereSql;
            $update = $this->db->query($query);
            return $update ? $this->db->affected_rows : false;
        } else {
            return false;
        }
    }

    public function customeUpdate($query)
    {
        if (!empty($query)) {
            $update = $this->db->query($query);
            return $update ? $this->db->affected_rows : false;
        } else {
            return false;
        }
    }

    /*
     * Delete data from the database
     * @param string name of the table
     * @param array where condition on deleting data
     */
    public function delete($table, $conditions)
    {
        $whereSql = '';
        if (!empty($conditions) && is_array($conditions)) {
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach ($conditions as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $whereSql .= $pre . $key . " = '" . $value . "'";
                $i++;
            }
        }
        $query = "DELETE FROM " . $table . $whereSql;
        $delete = $this->db->query($query);
        return $delete ? true : false;
    }

    public function customDelete($query)
    {
        $delete = $this->db->query($query);
        return $delete ? true : false;
    }

    public function sendEmailVerifyOTP($email, $verifycode)
    {
        $authkey = MSG91_AUTH_KEY;
        $sender_email = SEND_VERIFY_EMAIL_FROM;
        // Setup request to send json via POST
        $data = array(
            'to' => array(
                array(
                    'email' => $email
                )
            ),
            'from' => array(
                "name" => "DivyaKalash",
                "email" => $sender_email
            ),
            'variables' => array(
                "VAR1" => "$verifycode"
            ),
            'template_id' => 'verifyotp',
            'authkey' => $authkey,
        );
        $url = 'https://control.msg91.com/api/v5/email/send';
        /* init the resource */
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
        ));
        /* Ignore SSL certificate verification */
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        /* get response */
        $res = json_decode(curl_exec($ch));
        if ($res->status == 'success') {
            $status = "success";
        } else {
            $status = "error";
        }
        return $status;
    }

    public function readExploreVideos()
    {
        $file = fopen("explore.json", "r");
        //Output lines until EOF is reached
        while (!feof($file)) {
            $line = fgets($file);
        }
        fclose($file);
        $explore_videos = json_decode($line, true);
        return $explore_videos['explore_list'];
    }

    public function get_youtube_id_from_url($url)
    {
        if (stristr($url, 'youtu.be/')) {
            preg_match('/(https:|http:|)(\/\/www\.|\/\/|)(.*?)\/(.{11})/i', $url, $final_ID);
            return $final_ID[4];
        } else {
            @preg_match('/(https:|http:|):(\/\/www\.|\/\/|)(.*?)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/i', $url, $IDD);
            return $IDD[5];
        }
    }
}
