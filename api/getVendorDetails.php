<?php
require __DIR__ .'/../api/database.php';

if (isset($_GET['vendor_name'])) {
    $vendor_name = $_GET['vendor_name'];

    $sql = "SELECT shopname, phno, shop_addr FROM vendor WHERE shopname = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $vendor_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $vendor = $result->fetch_assoc();
        echo json_encode($vendor);
    } else {
        echo json_encode(['error' => 'Vendor not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Vendor name not provided']);
}

$conn->close();
?>
