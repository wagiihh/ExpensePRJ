<?php
include("session.php");

$successmsg = "";
$errormsg = "";

if (isset($_POST['update_balance'])) {
    $new_balance = $_POST['balance'];

    // Validate inputs
    if ( empty($new_balance)) {
        $errormsg = "balance is required!";
    } else {
        // Update the balance in the database
        $query = "UPDATE users SET balance='$new_balance' WHERE user_id='$userid'";
        if (mysqli_query($con, $query)) {
            $successmsg = "Balance updated successfully!";
        } else {
            $errormsg = "Error updating balance: " . mysqli_error($con);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Balance</title>
</head>
<body>
    
    <h1>Update User Balance</h1>
    <?php if ($successmsg): ?>
        <p style="color: green;"><?php echo $successmsg; ?></p>
    <?php endif; ?>
    <?php if ($errormsg): ?>
        <p style="color: red;"><?php echo $errormsg; ?></p>
    <?php endif; ?>
    
    <form method="POST" action="">
        
        <label for="balance">Add Balance:</label>
        <input type="number" id="balance" name="balance" required><br><br>
        
        <input type="submit" name="update_balance" value="Update Balance">
    </form>
</body>
</html>
