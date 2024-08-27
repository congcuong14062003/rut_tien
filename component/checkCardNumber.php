<?php
include './validateCardNumber.php';

header('Content-Type: application/json');

if (isset($_POST['card_number'])) {
    $card_number = $_POST['card_number'];
    $is_valid = is_valid_luhn($card_number);
    echo json_encode(array('valid' => $is_valid));
} else {
    echo json_encode(array('valid' => false));
}
?>
