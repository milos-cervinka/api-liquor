<?php
require_once __DIR__ . '/config.php';
$stateFile = DATA_DIR . '/import_state.json';
$isFinished = false;

if (isset($_GET['start'])) 
{
    ob_start();
    include __DIR__ . '/cron.php';
    $output = ob_get_clean();
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    if (strpos($output, 'Error') !== false || strpos($output, 'ERROR') !== false) 
    {
        $isFinished = 'Error';
    } 
    elseif (strpos($output, 'Complete') !== false || !file_exists($stateFile)) 
    {
        $isFinished = 'Complete';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Import</title>
    <?php 
    if (isset($_GET['start']) && !$isFinished) 
    {
        echo "<meta http-equiv=\"refresh\" content=\"1\">";
    }
    ?>
</head>
<body>

    <p>State: <?php echo $isFinished ? $isFinished : (isset($_GET['start']) ? 'Working' : 'Waiting'); ?></p>
    <?php
        if(!isset($_GET['start'])) 
        {
            echo "<a href='run.php?start=1'>Run</a>";
        } 
        elseif ($isFinished) 
        {
            echo "<a href='run.php'>Again</a>";
        }
    ?>

</body>
</html>