The Plugin aims to reduce the (write-) wear on your SSD(s) by incorporating the script(s) from mgutt.

Upon the first array start, when the plugin is installed, it will move the docker status and log files into your RAM.*


A backup of the logs will be sync'd, to your appdata location every 30 minutes by default. You can change the interval from every minute to every 60 minutes on the plugins setting page(or disable that feature by selecting 0). It will also let you know, if the script applied the modifications or not.

If you happen to boot into a version of unraid which is not supported yet, nothing will happen. If the plugin then recieves an update, to support your version, a simple array stop and start will apply the necessary modifications.(i do hope im fast enough to update the plugin just in time so it runs uninterrupted, but im just human^^) 


If you want to uninstall the plugin and remove the modifications, you would have to reboot your system. 

If youre curious on how it works in more detail, head over here:

https://forums.unraid.net/topic/136087-ram-disk-for-docker-statuslog-files/