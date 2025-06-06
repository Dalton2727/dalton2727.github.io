<?php 
session_start();
include 'dbconnection.php';

// Get user ID from session or GET parameter
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : (isset($_GET['userid']) ? $_GET['userid'] : '');

if (empty($userid)) {
    die("Error: User not logged in");
}

// Get selected month from GET parameter or use current month
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$selected_month_start = $selected_month . '-01';
$selected_month_end = date('Y-m-t', strtotime($selected_month_start));

// Get list of available months from purchases
$months_sql = "SELECT DISTINCT DATE_FORMAT(created_at, '%Y-%m') as month 
               FROM purchases 
               WHERE user_id = ? 
               AND created_at IS NOT NULL 
               ORDER BY month DESC";
$months_stmt = mysqli_prepare($db, $months_sql);
if (!$months_stmt) {
    die("Error preparing statement: " . mysqli_error($db));
}

if (!mysqli_stmt_bind_param($months_stmt, "s", $userid)) {
    die("Error binding parameters: " . mysqli_stmt_error($months_stmt));
}

if (!mysqli_stmt_execute($months_stmt)) {
    die("Error executing statement: " . mysqli_stmt_error($months_stmt));
}

$months_result = mysqli_stmt_get_result($months_stmt);
$available_months = [];
while ($row = mysqli_fetch_assoc($months_result)) {
    $available_months[] = $row['month'];
}

// Get spending data by location
$sql = "SELECT location, SUM(item_price) as total_spent 
        FROM purchases 
        WHERE user_id = ? 
        AND created_at >= ? 
        AND created_at <= ?
        GROUP BY location 
        ORDER BY total_spent DESC";
$stmt = mysqli_prepare($db, $sql);
if (!$stmt) {
    die("Error preparing statement: " . mysqli_error($db));
}

if (!mysqli_stmt_bind_param($stmt, "sss", $userid, $selected_month_start, $selected_month_end)) {
    die("Error binding parameters: " . mysqli_stmt_error($stmt));
}

if (!mysqli_stmt_execute($stmt)) {
    die("Error executing statement: " . mysqli_stmt_error($stmt));
}

$result = mysqli_stmt_get_result($stmt);
$location_spending_data = [];
$total_spent = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $location_spending_data[] = [
        'location' => $row['location'],
        'amount' => floatval($row['total_spent'])
    ];
    $total_spent += floatval($row['total_spent']);
}

mysqli_stmt_close($stmt);

// Convert data to JavaScript array format
$js_location_data = [];
foreach ($location_spending_data as $item) {
    $js_location_data[] = "['" . addslashes($item['location']) . "', " . $item['amount'] . "]";
}
$js_location_string = implode(",\n", $js_location_data);

// Get data for line graph (cumulative)
$sql_time_cumulative = "SELECT DATE(created_at) as date, SUM(item_price) as spent_day
        FROM purchases 
        WHERE user_id = ? 
        AND created_at >= ? 
        AND created_at <= ?
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at) ASC";     
$stmt_time_cumulative = mysqli_prepare($db, $sql_time_cumulative);
if (!$stmt_time_cumulative) {
    die("Error preparing statement: " . mysqli_error($db));
}

if (!mysqli_stmt_bind_param($stmt_time_cumulative, "sss", $userid, $selected_month_start, $selected_month_end)) {
    die("Error binding parameters: " . mysqli_stmt_error($stmt_time_cumulative));
}

if (!mysqli_stmt_execute($stmt_time_cumulative)) {
    die("Error executing statement: " . mysqli_stmt_error($stmt_time_cumulative));
}

$result_time_cumulative = mysqli_stmt_get_result($stmt_time_cumulative);
$time_spending_data_cumulative = [];
$running_total = 0;

while ($row = mysqli_fetch_assoc($result_time_cumulative)) {
    $date = new DateTime($row['date']);
    $day = intval($date->format('j'));
    $running_total += floatval($row['spent_day']);
    
    $time_spending_data_cumulative[] = [
        'day' => $day,
        'amount' => $running_total
    ];
}

// Get data for combo chart (daily)
$sql_time_daily = "SELECT DATE(created_at) as date, SUM(item_price) as spent_day
        FROM purchases 
        WHERE user_id = ?
        AND created_at >= ? 
        AND created_at <= ?
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at) ASC";     
$stmt_time_daily = mysqli_prepare($db, $sql_time_daily);
if (!$stmt_time_daily) {
    die("Error preparing statement: " . mysqli_error($db));
}

if (!mysqli_stmt_bind_param($stmt_time_daily, "sss", $userid, $selected_month_start, $selected_month_end)) {
    die("Error binding parameters: " . mysqli_stmt_error($stmt_time_daily));
}

if (!mysqli_stmt_execute($stmt_time_daily)) {
    die("Error executing statement: " . mysqli_stmt_error($stmt_time_daily));
}

$result_time_daily = mysqli_stmt_get_result($stmt_time_daily);
$time_spending_data_daily = [];

while ($row = mysqli_fetch_assoc($result_time_daily)) {
    $date = new DateTime($row['date']);
    $day = intval($date->format('j'));
    
    $time_spending_data_daily[] = [
        'day' => $day,
        'amount' => floatval($row['spent_day'])
    ];
}

// Create arrays for all days of the month
$days_in_month = date('t');
$all_days_cumulative = array_fill(1, $days_in_month, 0);
$all_days_daily = array_fill(1, $days_in_month, 0);

// Fill in the actual data
$running_total = 0;
foreach ($time_spending_data_cumulative as $data) {
    $running_total += $data['amount'];
    $all_days_cumulative[$data['day']] = $running_total;
}

// Make sure cumulative data continues to increase
for ($day = 2; $day <= $days_in_month; $day++) {
    if ($all_days_cumulative[$day] < $all_days_cumulative[$day - 1]) {
        $all_days_cumulative[$day] = $all_days_cumulative[$day - 1];
    }
}

foreach ($time_spending_data_daily as $data) {
    $all_days_daily[$data['day']] = $data['amount'];
}

// Get user's budget from session
$user_budget = isset($_SESSION['budget']) ? floatval($_SESSION['budget']) : 0;

// Create budget data points for all days
$budget_data = [];
for ($day = 1; $day <= 31; $day++) {
    $budget_data[] = "[" . $day . ", " . $user_budget . "]";
}
$js_budget_string = implode(",\n", $budget_data);
?>
<!DOCTYPE html>
<html style="background-color: #f0f0f0;">
  <head>
    <body>
    <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="Stats" content="" />
        <meta name="description" content="Public Stats"/>
        <link rel="stylesheet" href="CSScode.css" />
     </head>
  <div id="navbar" class="light">
                <ul>
                  <li>User: <?php echo $userid?></li>
                  <li> <?php echo '<a href="reviews.php?userid=' . urlencode($userid) . '">Reviews</a>'?> </li>
                  <li> <?php echo '<a href="ratings.php?userid=' . urlencode($userid) . '">Ratings</a>'?> </li>
                  <li> <?php echo '<a href="Edit.php?userid=' . urlencode($userid) . '">Edit</a>'?> </li>
                  <li> <?php echo '<a href="comment.php?userid=' . urlencode($userid) . '">Comment</a>'?> </li>
                  <li> <?php echo '<a href="write.php?userid=' . urlencode($userid) . '">Write</a>'?> </li>
                  <li> <?php echo '<a href="start2.php?userid=' . urlencode($userid) . '">Home</a>'?> </li>
                  <li><?php echo '<a href="logout.php">Log Out</a>'; ?></li>
                </ul>
              </div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Location', 'Amount Spent'],
          <?php echo $js_location_string; ?>
        ]);

        var options = {
          title: 'Budget Breakdown By Location (Total Spent: <?php echo number_format($total_spent, 2); ?>)',
          is3D: true,
          chartArea: {width: '100%', height: '80%'},
          legend: {position: 'right'},
          pieSliceText: 'percentage',
          tooltip: {
            isHtml: true,
            text: 'both',
            trigger: 'selection'
          },
          backgroundColor: '#f0f0f0'
        };

        var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
        chart.draw(data, options);
      }
    </script>
    <div style="text-align: center; margin: 20px 0;">
      <form method="get">
        <select name="month" onchange="this.form.submit()">
          <?php
          foreach ($available_months as $month) {
              $selected = ($month == $selected_month) ? 'selected' : '';
              $month_display = date('F Y', strtotime($month));
              echo "<option value='$month' $selected>$month_display</option>";
          }
          ?>
        </select>
      </form>
    </div>
    <div style="text-align: center; margin: 20px 0;">
        <form action="personal_stats.php" method="get" style="display: inline;">
            <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
            <button type="submit">My Stats</button>
        </form>
        <form action="Ratings.php" method="get" style="display: inline;">
            <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>" />
            <button type="submit">Public Stats</button>
        </form>
    </div>
    <div style="text-align: center;">
    <h1>Personal Stats</h1>
      <div id="donutchart" style="width: 900px; height: 500px; margin: 0 auto;"></div>
      <div id="curve_chart" style="width: 900px; height: 500px; margin: 0 auto;"></div>
      <div id="chart_div" style="width: 900px; height: 500px; margin: 0 auto;"></div>
    </div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Day', 'Cumulative Spending', 'Budget Limit'],
          <?php 
          $combined_data = [];
          for ($day = 1; $day <= $days_in_month; $day++) {
              $combined_data[] = "[" . $day . ", " . 
                                $all_days_cumulative[$day] . ", " . 
                                $user_budget . "]";
          }
          echo implode(",\n", $combined_data);
          ?>
        ]);

        var options = {
          title: 'Cumulative Spending Over Time (Budget: <?php echo number_format($user_budget, 2); ?> points)',
          legend: { position: 'bottom' },
          hAxis: {
            title: 'Day of Month',
            format: '#',
            gridlines: {count: 31},
            ticks: [1,5,10,15,20,25,30,31]
          },
          vAxis: {
            title: 'Total Amount Spent (points)'
          },
          series: {
            0: { pointSize: 5 },
            1: { 
              lineDashStyle: [4, 4],
              color: 'red',
              pointSize: 0
            }
          },
          backgroundColor: '#f0f0f0'
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
    <div id="curve_chart" style="width: 900px; height: 500px"></div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawVisualization);

      function drawVisualization() {
        // Get the number of days in current month
        var daysInMonth = <?php echo $days_in_month; ?>;
        var dailyBudget = <?php echo $user_budget; ?> / daysInMonth;

        // Prepare the data array
        var dataArray = [['Day', 'Daily Spending', 'Daily Budget Target']];
        
        // Add data for each day of the month
        for (var day = 1; day <= daysInMonth; day++) {
            dataArray.push([day, 0, dailyBudget]);
        }

        // Add actual spending data
        <?php
        for ($day = 1; $day <= $days_in_month; $day++) {
            echo "dataArray[" . $day . "][1] = " . $all_days_daily[$day] . ";\n";
        }
        ?>

        var data = google.visualization.arrayToDataTable(dataArray);

        var options = {
          title: 'Daily Spending vs Budget Target',
          vAxis: {
            title: 'Amount (points)',
            minValue: 0
          },
          hAxis: {
            title: 'Day of Month',
            format: '#',
            gridlines: {count: daysInMonth},
            ticks: [1,5,10,15,20,25,30,31]
          },
          seriesType: 'bars',
          series: {
            0: { color: '#4285F4' },  // Blue bars for spending
            1: { 
              type: 'line',
              color: 'red',
              lineDashStyle: [4, 4],
              pointSize: 0
            }
          },
          backgroundColor: '#f0f0f0',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
</body>
</html>
