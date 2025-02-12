<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

function draw_calendar($month, $year, $projects) {
    $calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
    
    // Calendar header
    $headings = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $calendar .= '<thead><tr class="calendar-row"><th class="calendar-day-head">' . implode('</th><th class="calendar-day-head">', $headings) . '</th></tr></thead>';
    
    // Days and weeks vars now...
    $running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
    $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
    $days_in_this_week = 1;
    $day_counter = 0;
    
    // Row for week one
    $calendar .= '<tr class="calendar-row">';
    
    // Print "blank" days until the first of the current week
    for ($x = 0; $x < $running_day; $x++) {
        $calendar .= '<td class="calendar-day-np"> </td>';
        $days_in_this_week++;
    }
    
    // Keep going with days...
    for ($list_day = 1; $list_day <= $days_in_month; $list_day++) {
        $calendar .= '<td class="calendar-day">';
        
        // Add the day number
        $calendar .= '<div class="day-number">' . $list_day . '</div>';
        
        // Fetch project data for this day
        $date_string = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($list_day, 2, '0', STR_PAD_LEFT);
        if (isset($projects[$date_string])) {
            foreach ($projects[$date_string] as $project) {
                $calendar .= '<div class="project">' . htmlspecialchars($project['title']) . '<br>' . htmlspecialchars($project['location']) . '</div>';
            }
        }
        
        $calendar .= '</td>';
        if ($running_day == 6) {
            $calendar .= '</tr>';
            if (($day_counter + 1) != $days_in_month) {
                $calendar .= '<tr class="calendar-row">';
            }
            $running_day = -1;
            $days_in_this_week = 0;
        }
        $days_in_this_week++;
        $running_day++;
        $day_counter++;
    }
    
    // Finish the rest of the days in the week
    if ($days_in_this_week < 8) {
        for ($x = 1; $x <= (8 - $days_in_this_week); $x++) {
            $calendar .= '<td class="calendar-day-np"> </td>';
        }
    }
    
    // Final row
    $calendar .= '</tr>';
    
    // End the table
    $calendar .= '</table>';
    
    return $calendar;
}

// Fetch projects from the database
$query = "SELECT title, date, location FROM projects";
$result = $conn->query($query);

$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[$row['date']][] = $row;
}

// Set the current month and year
$month = date('m');
$year = date('Y');
if (isset($_GET['month']) && isset($_GET['year'])) {
    $month = $_GET['month'];
    $year = $_GET['year'];
}

// Generate the calendar
$calendar = draw_calendar($month, $year, $projects);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Projects Calendar</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include('nav_bar_organizers.php'); ?>
    <div class="container">
        <h1>Projects Calendar</h1>
        <div class="calendar-navigation">
            <a href="?month=<?php echo $month - 1; ?>&year=<?php echo $year; ?>" class="btn">Previous</a>
            <span class="current-date"><?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?></span>
            <a href="?month=<?php echo $month + 1; ?>&year=<?php echo $year; ?>" class="btn">Next</a>
        </div>
        <div class="calendar-container">
            <?php echo $calendar; ?>
        </div>
    </div>
</body>
</html>
