<?php
include("session.php");

function get_sum($con, $userid, $interval) {
    $query = "SELECT SUM(expense) AS total FROM expenses WHERE user_id = '$userid' AND expensedate >= DATE_SUB(CURDATE(), INTERVAL $interval)";
    $result = mysqli_query($con, $query);
    return '0' + mysqli_fetch_assoc($result)['total'];
}


$user_query = "SELECT balance, `remaining balance` FROM users WHERE user_id = '$userid'";
$user_result = mysqli_query($con, $user_query);
$user_data = mysqli_fetch_assoc($user_result);
$user_balance = $user_data['balance'];
$remaining_balance = $user_data['remaining balance'];

$total_expenses = get_sum($con, $userid, '1 YEAR'); 
$remaining_balance = $user_balance - $total_expenses; 

$update_query = "UPDATE users SET `remaining balance` = '$remaining_balance' WHERE user_id = '$userid'";
mysqli_query($con, $update_query);

$one_month_ago = date("Y-m-d", strtotime("-1 month"));
$exp_category_dc = mysqli_query($con, "SELECT expensecategory FROM expenses WHERE user_id = '$userid' AND expensedate >= '$one_month_ago' GROUP BY expensecategory");
$exp_amt_dc = mysqli_query($con, "SELECT SUM(expense) FROM expenses WHERE user_id = '$userid' AND expensedate >= '$one_month_ago' GROUP BY expensecategory");

$one_week_ago = date("Y-m-d", strtotime("-1 week"));
$exp_date_line = mysqli_query($con, "SELECT DATE_FORMAT(expensedate, '%b %d') AS day_month FROM expenses WHERE user_id = '$userid' AND expensedate >= '$one_week_ago' GROUP BY expensedate");
$exp_amt_line = mysqli_query($con, "SELECT SUM(expense) FROM expenses WHERE user_id = '$userid' AND expensedate >= '$one_week_ago' GROUP BY expensedate");

$yearly_expenses_result = mysqli_query($con, "SELECT YEAR(expensedate) AS year, SUM(expense) AS total_expense FROM expenses WHERE user_id = '$userid' GROUP BY YEAR(expensedate) ORDER BY year");
$year_labels = [];
$yearly_expense_data = [];
while ($row = mysqli_fetch_assoc($yearly_expenses_result)) {
    $year_labels[] = $row['year'];
    $yearly_expense_data[] = $row['total_expense'];
}

$monthly_expenses_result = mysqli_query($con, "SELECT DATE_FORMAT(expensedate, '%Y-%m') AS month_year, SUM(expense) AS total_expense FROM expenses WHERE user_id = '$userid' AND expensedate >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) GROUP BY month_year ORDER BY expensedate");
$monthly_labels = [];
$monthly_expense_data = [];
while ($row = mysqli_fetch_assoc($monthly_expenses_result)) {
    $monthly_labels[] = $row['month_year'];
    $monthly_expense_data[] = $row['total_expense'];
}

$today_expense_amount = get_sum($con, $userid, '0 DAY');
$yesterday_expense_amount = get_sum($con, $userid, '1 DAY');
$this_week_expense_amount = get_sum($con, $userid, '1 WEEK');
$this_month_expense_amount = get_sum($con, $userid, '1 MONTH');
$this_year_expense_amount = get_sum($con, $userid, '1 YEAR');
$total_expense_amount = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(expense) AS total FROM expenses WHERE user_id = '$userid'"))['total'] ?: '0';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Expense Manager - Dashboard</title>
  <link href="css/bootstrap.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <script src="js/feather.min.js"></script>
  <style>
    .card a {
      color: #000;
      font-weight: 500;
    }
    .card a:hover {
      color: #28a745;
      text-decoration: dotted;
    }
    .try {
      font-size: 28px;
      color: #333;
      padding: 5px 0px 0px 0px;
    }
    .container {
      padding:0px 20px 20px 20px;
    }
    .card.text-center {
      border: 3px solid #ccc;
      padding: 10px;
      margin: 10px;
      background-color: #f8f9fa;
      border-radius: 5px;
    }
    .card-title {
      font-size: 17.5px;
      margin-bottom: 1px;
      color: #333;
    }
    .card-text {
      font-size: 24px;
      font-weight: bold;
      color: #6c757d;
    }
  </style>
</head>
<body>
  <div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div class="border-right" id="sidebar-wrapper">
      <div class="user">
        <img class="img img-fluid rounded-circle" src="uploads\default_profile.png" width="120">
        <h5><?php echo $username ?></h5>
        <p><?php echo $useremail ?></p>
      </div>
      <div class="sidebar-heading">Management</div>
      <div class="list-group list-group-flush">
        <a href="index.php" class="list-group-item list-group-item-action sidebar-active"><span data-feather="home"></span> Dashboard</a>
        <a href="add_expense.php" class="list-group-item list-group-item-action "><span data-feather="plus-square"></span> Add Expenses</a>
        <a href="manage_expense.php" class="list-group-item list-group-item-action "><span data-feather="dollar-sign"></span> Manage Expenses</a>
        <a href="addbalance.php" class="list-group-item list-group-item-action "><span data-feather="dollar-sign"></span> Update Balance</a>

      </div>
      <div class="sidebar-heading">Settings </div>
      <div class="list-group list-group-flush">
        <a href="profile.php" class="list-group-item list-group-item-action "><span data-feather="user"></span> Profile</a>
        <a href="logout.php" class="list-group-item list-group-item-action "><span data-feather="power"></span> Logout</a>
      </div>
    </div>
    <div id="page-content-wrapper">
      <nav class="navbar navbar-expand-lg navbar-light border-bottom">
        <button class="toggler" type="button" id="menu-toggle" aria-expanded="false">
          <span data-feather="menu"></span>
        </button>
        <div class="col-md-0 text-center">
          <h3 class="try">Dashboard</h3>
        </div>
      </nav>
      <div class="container-fluid">
        <h4 class="mt-4">Full-Expenses Report</h4>
        <div class="row">
          <div class="container mt-4">
            <div class="row">
              <div class="col-md-3">
                <div class="card text-center">
                  <div class="card-body">
                    <h5 class="card-title">Today's Expense</h5>
                    <p class="card-text">$<?php echo $today_expense_amount; ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card text-center">
                  <div class="card-body">
                    <h5 class="card-title">Yesterday's Expense</h5>
                    <p class="card-text">$<?php echo $yesterday_expense_amount; ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card text-center">
                  <div class="card-body">
                    <h5 class="card-title">Last 7 Day's Expense</h5>
                    <p class="card-text">$<?php echo $this_week_expense_amount; ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card text-center">
                  <div class="card-body">
                    <h5 class="card-title">Balance</h5>
                    <p class="card-text">$<?php echo $user_balance; ?></p>
                  </div>
                </div>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-6">
                <div class="card text-center">
                  <div class="card-body">
                    <h5 class="card-title">Expenses for the Month</h5>
                    <p class="card-text">$<?php echo $this_month_expense_amount; ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card text-center">
                  <div class="card-body">
                    <h5 class="card-title">Remaining Balance</h5>
                    <p class="card-text">$<?php echo $remaining_balance; ?></p>
                  </div>
                </div>
              </div>
            </div>
            <div class="row mt-4">
              <div class="col-md-12">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
