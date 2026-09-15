<?php
$configFilePath = "/boot/config/plugins/RAM-DISK-Dockerlog/settings.cfg";
$modcheck = "/tmp/RAM-DISK-Dockerlog/modified";
if (!file_exists($configFilePath)) {
    die("Configuration file not found.");
}

function readSetting($file) {
    $content = file_get_contents($file);
    $lines = preg_split('/\r\n|\r|\n/', trim($content));

    return [
        'interval' => isset($lines[0]) ? trim($lines[0]) : '30',
        'metadata' => isset($lines[1]) ? trim($lines[1]) : '0'
    ];
}

function writeSetting($file, $value, $metadata) {
    file_put_contents($file, $value . PHP_EOL . $metadata);
    shell_exec('sed -i "s/\\$sync_interval_minutes=[0-9]*;/\\$sync_interval_minutes=' . $value . ';/g" /tmp/RAM-DISK-Dockerlog/monitor');
    shell_exec('sed -i "s/\\$sync_metadata=[0-9]*;/\\$sync_metadata=' . $metadata . ';/g" /tmp/RAM-DISK-Dockerlog/monitor');
}

$settings = readSetting($configFilePath);
$settingValue = $settings['interval'];
$metadataValue = $settings['metadata'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userInput = isset($_POST['setting']) ? intval($_POST['setting']) : 30;
    $metadataInput = isset($_POST['metadata']) ? 1 : 0;

    if ($userInput >= 0 && $userInput <= 60) {
        writeSetting($configFilePath, $userInput, $metadataInput);
        $settings = readSetting($configFilePath);
	$settingValue = $settings['interval'];
	$metadataValue = $settings['metadata'];
        $message = "Setting updated successfully.";
	} else {
        $message = "Please enter a value between 0 and 60.";
    }
}


function check_file_exists($modcheck) {
    if (file_exists($modcheck)) {
        // File exists
        echo '<div style="text-align: left; margin-top: 1em;">
                <span style="margin-left: 0em;">RAM-Disk is running</span>
                <input type="checkbox" checked style="accent-color: green; pointer-events: none;"/> 
              </div>';
    } else {
        // File does not exist
        echo '<div style="text-align: left; margin-top: 1em;">
                <span style="margin-left: 0em;">RAM-Disk is not running</span>
                <span style="color: red; font-size: 1.2em;">&#10006;</span>
              </div>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAM-Disk for Docker logs settings</title>
</head>
<body>
    <h1>RAM-Disk Backup Interval Setting</h1>
    <?php if (isset($message)): ?>
        <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>
<div style="width:49%; float:left; border: 0em solid black;">
<p>The interval is set in minutes (1-60, 0 disables the automatic backup)</p>
    <form method="post" action="">
        <br>
        <input type="number" id="setting" name="setting" min="0" max="60" value="<?php echo htmlspecialchars($settingValue); ?>" required>
	<br><br>
	<label>
	<input type="checkbox" id="metadata" name="metadata" value="1" <?php echo ($metadataValue == 1) ? 'checked' : ''; ?>>Sync metadata</label>
        <br><br>
	<input type="submit" value="Save">
    </form>
</div>
<div style="width: 49%; float:right; border: 0em solid black;">
<blockquote class="inline_help" style="display: block; align=right">
    <p>If you install this for the <b>first time</b> and your array was already running at that point, you can simply <b>stop and start the array </b> to use it. Alternatively perfom a <b>reboot.</b></p>
    <p><b style="color: #ff0000;">ATTENTION:</b><b> To remove the plugin, please stop your array and then remove the plugin. If you remove the plugin without stopping the array, you have to do it later.</b></p>
    <p>This is only possible thanks to MGutt and his script. <a href="https://forums.unraid.net/topic/136087-ram-disk-for-docker-statuslog-files" target="_blank">https://forums.unraid.net/topic/136087-ram-disk-for-docker-statuslog-files</a><p>
</blockquote>
</div>
<p>&nbsp;</p>
<div style="width:99%; float:left; border: 0em solid black;">
    <h2><?php check_file_exists($modcheck); ?></h2>
</div>
</body>
</html>
