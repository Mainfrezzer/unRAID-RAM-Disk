<?php
$configFilePath = '/boot/config/plugins/RAM-DISK-Dockerlog/settings.cfg';
$modcheck = "/tmp/RAM-DISK-Dockerlog/modified";
if (!file_exists($configFilePath)) {
    die("Configuration file not found.");
}

function readSetting($file) {
    $content = file_get_contents($file);
    return trim($content);
}

function writeSetting($file, $value) {
    file_put_contents($file, $value);
    shell_exec('value=$(cat /boot/config/plugins/RAM-DISK-Dockerlog/settings.cfg) && sed -i "s/\\\$sync_interval_minutes=[0-9]\\+;/\\\$sync_interval_minutes="$value";/g" /tmp/RAM-DISK-Dockerlog/monitor');
}

$settingValue = readSetting($configFilePath);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userInput = isset($_POST['setting']) ? intval($_POST['setting']) : 30;

    if ($userInput >= 1 && $userInput <= 60) {
        writeSetting($configFilePath, $userInput);
        $settingValue = readSetting($configFilePath);
        $message = "Setting updated successfully.";
		if (file_exists($modcheck)){
		shell_exec('sed -i "/include_once(\x27\/tmp\/RAM-DISK-Dockerlog\/monitor\x27);/d" /usr/local/emhttp/plugins/dynamix/scripts/monitor');
		shell_exec('sed -i "/^<?PHP$/a include_once(\x27\/tmp/RAM-DISK-Dockerlog/monitor\x27\);" /usr/local/emhttp/plugins/dynamix/scripts/monitor');
		}
	} elseif ($userInput == 0) {
		writeSetting($configFilePath, $userInput);
		$settingValue = readSetting($configFilePath);
		shell_exec('sed -i "/include_once(\x27\/tmp\/RAM-DISK-Dockerlog\/monitor\x27);/d" /usr/local/emhttp/plugins/dynamix/scripts/monitor');
		$message = "Setting updated successfully. Backups are now disabled";
    } else {
        $message = "Please enter a value between 0 and 60.";
    }
}


$confversion = trim(shell_exec('source /etc/unraid-version && echo $version'));

$filePath = '/usr/local/emhttp/plugins/RAM-DISK-Dockerlog/script/' . $confversion;

if(file_exists($filePath)) {$content = file_get_contents($filePath);}

function check_file_exists($modcheck) {
    if (file_exists($modcheck)) {
        // File exists
        echo '<div style="text-align: left; margin-top: 1em;">
                <span style="margin-left: 0em;">Script was executed</span>
                <input type="checkbox" checked style="accent-color: green; pointer-events: none;"/> 
              </div>';
    } else {
        // File does not exist
        echo '<div style="text-align: left; margin-top: 1em;">
                <span style="margin-left: 0em;">Modifications have not been applied</span>
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
        <input type="submit" value="Save">
    </form>
</div>
<div style="width: 49%; float:right; border: 0em solid black;">
<blockquote class="inline_help" style="display: block; align=right">
    <p>This Plugin runs the script below only once. You can see the status, if it ran or not, on the left.<br/></p>
    <p>If you install this for the <b>first time</b> or you updated the plugin to support your current Unraid version, you can simply <b>stop and start the array</b> to use it. Alternatively perfom a <b>reboot.</b></p>
    <p><b style="color: #ff0000;">ATTENTION:</b><b> To remove the script, remove the plugin and perform a reboot.</b></p>
    <p>There are multiple checks to ensure nothing breaks, you can update to a new version or downgrade to any version without issue</p>
    <p>If you see "No script found for this version", this means the plugin has no support for your current Unraid version. An update might change that.</p>
    <p>This is only possible thanks to MGutt and his script. <a href="https://forums.unraid.net/topic/136087-ram-disk-for-docker-statuslog-files" target="_blank">https://forums.unraid.net/topic/136087-ram-disk-for-docker-statuslog-files</a><p>
</blockquote>
</div>
<p>&nbsp;</p>
<div style="width:99%; float:left; border: 0em solid black;">
    <h2>Script for Version: <?php echo htmlspecialchars($confversion); ?></h2>
    <p><?php check_file_exists($modcheck); ?></p>
    <pre><?php echo htmlspecialchars($content ?: 'No script found for this version'); ?></pre>
</div>
</body>
</html>
