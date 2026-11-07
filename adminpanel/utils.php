<?php

/** Escape dynamic values for HTML text and quoted attribute contexts. */
function h($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
// include '../include/connection.php';
class utils
{
    public $db;

    public function stringToUrl($string)
    {
        return str_replace(' ', '-', (trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($string)), '-')));
    }

    function getURLById($conn, $table, $id, $type = "i")
    {
        $url = '';
        $stmt = $conn->prepare("SELECT url FROM $table WHERE id = ?");
        $stmt->bind_param($type, $id);
        $stmt->execute();
        $stmt->bind_result($url);
        $stmt->fetch();
        $stmt->close();

        return $url;
    }

    function generateUniqueURL($conn, $table, $url)
    {
        $url = $this->stringToUrl($url);
        $original_url = $url;
        $counter = 1;

        while (true) {
            $stmt = $conn->prepare("SELECT id FROM $table WHERE url = ?");
            $stmt->bind_param("s", $url);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->num_rows;
            $stmt->close();

            if ($num_rows == 0) {
                // URL is unique, return it
                return $url;
            } else {
                // URL already exists, generate new URL with postfix
                $url = $original_url . '-' . $counter;
                $counter++;
            }
        }
    }

    public function moveBannerPosition($conn, $table, $direction, $rowId)
    {
        $response = array();

        // Fetch the current position of the row
        $getCurrentPositionQuery = "SELECT position FROM $table WHERE id = ?";
        $stmt = $conn->prepare($getCurrentPositionQuery);
        $stmt->bind_param("i", $rowId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $currentPosition = $row['position'];

            if ($direction === 'up' && $currentPosition > 1) {
                // Move the position up
                $newPosition = $currentPosition - 1;

                // Update the current row position
                $updateCurrentPositionQuery = "UPDATE $table SET position = ? WHERE id = ?";
                $stmt = $conn->prepare($updateCurrentPositionQuery);
                $stmt->bind_param("ii", $newPosition, $rowId);
                $stmt->execute();

                // Find the row with the position to swap
                $findSwapRowQuery = "SELECT id FROM $table WHERE position = ? AND id != ?";
                $stmt = $conn->prepare($findSwapRowQuery);
                $stmt->bind_param("ii", $newPosition, $rowId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $row = $result->fetch_assoc();
                    $swapRowId = $row['id'];

                    // Swap the position with the found row
                    $updateSwapRowPositionQuery = "UPDATE $table SET position = ? WHERE id = ?";
                    $stmt = $conn->prepare($updateSwapRowPositionQuery);
                    $stmt->bind_param("ii", $currentPosition, $swapRowId);
                    $stmt->execute();

                    $response['success'] = true;
                } else {
                    $response['success'] = false;
                    $response['error'] = "No row found to swap position with.";
                }
            } elseif ($direction === 'down') {
                // Move the position down
                // Check if the row is not the last one
                $getMaxPositionQuery = "SELECT MAX(position) AS max_position FROM $table";
                $maxPositionResult = $conn->query($getMaxPositionQuery);
                $maxPositionRow = $maxPositionResult->fetch_assoc();
                $maxPosition = $maxPositionRow['max_position'];

                if ($currentPosition < $maxPosition) {
                    $newPosition = $currentPosition + 1;

                    // Update the current row position
                    $updateCurrentPositionQuery = "UPDATE $table SET position = ? WHERE id = ?";
                    $stmt = $conn->prepare($updateCurrentPositionQuery);
                    $stmt->bind_param("ii", $newPosition, $rowId);
                    $stmt->execute();

                    // Find the row with the position to swap
                    $findSwapRowQuery = "SELECT id FROM $table WHERE position = ? AND id != ?";
                    $stmt = $conn->prepare($findSwapRowQuery);
                    $stmt->bind_param("ii", $newPosition, $rowId);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows === 1) {
                        $row = $result->fetch_assoc();
                        $swapRowId = $row['id'];

                        // Swap the position with the found row
                        $updateSwapRowPositionQuery = "UPDATE $table SET position = ? WHERE id = ?";
                        $stmt = $conn->prepare($updateSwapRowPositionQuery);
                        $stmt->bind_param("ii", $currentPosition, $swapRowId);
                        $stmt->execute();

                        $response['success'] = true;
                    } else {
                        $response['success'] = false;
                        $response['error'] = "No row found to swap position with.";
                    }
                } else {
                    $response['success'] = false;
                    $response['error'] = "Row cannot be moved further down.";
                }
            } else {
                $response['success'] = false;
                $response['error'] = "Row cannot be moved further $direction.";
            }
        } else {
            $response['success'] = false;
            $response['error'] = "Row not found.";
        }

        return $response;
    }

    public function get_webinfo()
    {
        $qry = "SELECT * FROM tbl_config WHERE id = 1";
        $conn      = db();
        $recordObj = $conn->query($qry);
        return $recordObj;
        $conn->close();
    }
}
