<?php
include 'db.php';

$stmt = $conn->prepare("SELECT * FROM results");
$stmt->execute();
$results = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GRADING SYSTEM</title>
</head>
<body>
     <h1>STUDENT GRADING SYSTEM</h1>

     <a href="add.php">Add Result</a>

     <br><br>


        <label for="search">Search:</label>
     <input type="text" name="search" placeholder="Search by Student Name, matrix number, subject name and code" 
        value="">
    <input type="submit" value="Search">
    <a href="index.php">Show All Results</a>

    <br><br>

     <table border="1">
        <tr>
            <th>id</th>
            <th>Student Name</th>
            <th>Matrix Number</th>
            <th>Subject</th>
            <th>Subject code</th>
            <th>Test 1</th>
            <th>Test 2</th>
            <th>Final Exam</th>
            <th>Total Marks</th>
            <th>Grade</th>     
            <th>Actions</th>
        </tr>

       <?php
       
       $no = 1;
       while ($row = $results->fetch_assoc()) {
           echo "<tr>";
           echo "<td>" . htmlspecialchars($no++) . "</td>";
           echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
           echo "<td>" . htmlspecialchars($row['matric_no']) . "</td>";
           echo "<td>" . htmlspecialchars($row['subject_name']) . "</td>";
           echo "<td>" . htmlspecialchars($row['subject_code']) . "</td>";
           echo "<td>" . number_format($row['test1'], 2) . "</td>";
           echo "<td>" . number_format($row['test2'], 2) . "</td>";
           echo "<td>" . number_format($row['final_exam'], 2) . "</td>";
           echo "<td>" . number_format($row['total_Marks'], 2) . "</td>";
           echo "<td>" . htmlspecialchars($row['grade']) . "</td>";
           echo "<td>
                   <a href='edit.php?id=" . htmlspecialchars($row['id']) . "'>Edit</a> | 
                   <a href='delete.php?id=" . htmlspecialchars($row['id']) . "' onclick=\"return confirm('Are you sure you want to delete this result?')\">Delete</a>
                 </td>";
           echo "</tr>";
       }
       ?>
    </table>
</body>
</html>