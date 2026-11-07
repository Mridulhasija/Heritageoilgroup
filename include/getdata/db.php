<?php

trait GetdataDbTrait
{
    public function ExecuteQuery($qry, $params = [])
    {
        $conn = db();
        $stmt = $conn->prepare($qry);

        if (!$stmt) {
            // Handle prepare error
            $error = $conn->error;
            error_log("Query prepare error: $error");
            return false; // Return false indicating failure
        }

        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                $types .= is_int($param) ? 'i' : (is_float($param) ? 'd' : 's');
            }

            // Check if bind_param fails
            if (!$stmt->bind_param($types, ...$params)) {
                // Handle bind_param error
                $error = $stmt->error;
                error_log("Bind parameters error: $error");
                $stmt->close();
                return false; // Return false indicating failure
            }
        }

        $result = $stmt->execute();

        if (!$result) {
            // Handle execute error
            $error = $stmt->error;
            error_log("Query execute error: $error");
        }

        $stmt->close();
        return $result;
    }

    public function FetchQuery($qry, $params = [])
    {
        $conn = db();
        $stmt = $conn->prepare($qry);

        if (!$stmt) {
            // Handle prepare error
            $error = $conn->error;
            error_log("Query prepare error: $error");
            return false;
        }

        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                $types .= is_int($param) ? 'i' : (is_float($param) ? 'd' : 's');
            }
            if (!$stmt->bind_param($types, ...$params)) {
                $error = $stmt->error;
                error_log("Bind parameters error: $error");
                $stmt->close();
                return false;
            }
        }

        $executed = $stmt->execute();
        if (!$executed) {
            $error = $stmt->error;
            error_log("Query execute error: $error");
            $stmt->close();
            return false;
        }
        $recordObj = $stmt->get_result();
        $stmt->close();

        return $recordObj;
    }

    public function getRecordCount($tableName)
    {
        if (!is_string($tableName) || !preg_match('/^[A-Za-z0-9_]+$/', $tableName)) {
            return 0;
        }

        $qry = "SELECT COUNT(*) as total FROM `$tableName`";
        $result = $this->FetchQuery($qry);
        if ($result === false) {
            return 0;
        }
        $row = $result->fetch_assoc();
        return (int) ($row['total'] ?? 0);
    }

    //fetch
    public function fetch($table, $where = '')
    {
        $qry = "SELECT * FROM $table";
        if (!empty($where)) {
            $qry .= " WHERE $where";
        }
        $conn = db();
        $recordObj = $conn->query($qry);

        if ($recordObj === FALSE) {
            echo "Error: " . $conn->error;
            return null;
        }

        $result = [];
        while ($row = $recordObj->fetch_assoc()) {
            $result[] = $row;
        }

        return $result;
    }

    public function fetchAltText($fileName)
    {
        $conn = db();
        $stmt = $conn->prepare('SELECT description FROM tbl_media WHERE file_name = ?');
        if (!$stmt) {
            die('Error in preparing statement: ' . $conn->error);
        }
        $stmt->bind_param('s', $fileName);
        $stmt->execute();
        $result = $stmt->get_result();
        $description = null;

        if ($row = $result->fetch_assoc()) {
            $description = $row['description'];
        } else {
            $description = 'No description available';
        }
        $stmt->close();
        return $description;
    }
}
