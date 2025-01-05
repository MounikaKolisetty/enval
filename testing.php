<?php
header('Content-Type: application/json');
if (extension_loaded('mongodb')) {
    echo json_encode(['status' => 'success', 'message' => 'MongoDB extension is loaded!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'MongoDB extension is NOT loaded.']);
}
?>
