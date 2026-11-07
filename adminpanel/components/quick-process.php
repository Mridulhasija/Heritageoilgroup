<?php
if ($actions['delete'] && $action == 'delete' && isset($id)) {
    // Handle single deletion (for product category, also delete child categories)
    if ($table === 'tbl_product_category') {
        $stmt = $conn->prepare("DELETE FROM `$table` WHERE `$id_col` = ? OR `parent_id` = ?");
        $stmt->bind_param($id_type . $id_type, $id, $id);
    } else {
        $stmt = $conn->prepare("DELETE FROM `$table` WHERE `$id_col` = ?");
        $stmt->bind_param($id_type, $id);
    }
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo 'success';
        exit();
    } else {
        if ($stmt->errno == 1451) {
            throw new Exception("Cannot delete record: It is being used in another table.");
        } else {
            throw new Exception("Error deleting record with ID $id: " . $stmt->error);
        }
        exit();
    }
} else if ($actions['approve'] && $action == 'approve' && isset($id)) {
    // Handle approval
    $stmt = $conn->prepare("UPDATE $table SET status = 1 WHERE $id_col = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }
    $stmt->bind_param($id_type, $id);
    $stmt->execute();

    echo 'success';
    exit();
} else if ($action == 'bulk-delete' && isset($_POST["bulkSelect"])) {
    // Handle bulk deletion
    $ids = $_POST["bulkSelect"];
    foreach ($ids as $id) {
        // For product category, also delete child categories
        if ($table === 'tbl_product_category') {
            $stmt = $conn->prepare("DELETE FROM `$table` WHERE `$id_col` = ? OR `parent_id` = ?");
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $conn->error);
            }
            $stmt->bind_param($id_type . $id_type, $id, $id);
        } else {
            $stmt = $conn->prepare("DELETE FROM `$table` WHERE `$id_col` = ?");
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $conn->error);
            }
            $stmt->bind_param($id_type, $id);
        }
        $stmt->execute();

        if ($stmt->affected_rows == 0) {
            if ($stmt->errno == 1451) {
                throw new Exception("Cannot delete record: It is being used in another table.");
            } else {
                throw new Exception("Error deleting record with ID $id: " . $stmt->error);
            }
            exit();
        }
    }

    $_SESSION["success-msg"] = "Deleted successfully";
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
