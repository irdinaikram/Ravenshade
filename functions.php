<?php
// Shared helper functions, used by add.php and edit.php

// Weighted total: Test 1 = 20%, Test 2 = 30%, Final Exam = 50%
// (assumes each mark is entered out of 100)
function calculateTotal($test1, $test2, $final) {
    $total = ($test1 * 0.20) + ($test2 * 0.30) + ($final * 0.50);
    return round($total, 2);
}

// Grading table from the assignment (Table 1)
function getGrade($total) {
    if ($total >= 80) return 'A';
    if ($total >= 65) return 'B';
    if ($total >= 50) return 'C';
    if ($total >= 40) return 'D';
    return 'E';
}