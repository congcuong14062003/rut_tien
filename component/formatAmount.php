<?php 
  function formatAmount($amount) {
    if ($amount) {
        return number_format($amount, 0, ',', '.');
    } 
}
?>