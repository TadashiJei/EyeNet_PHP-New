<?php
$server = getRowById("app_servers",$_GET['id']);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title"><?php _e('Install macOS Agent'); ?></h4>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <h4><?php _e('Step 1: Install Required Dependencies'); ?></h4>
            <p><?php _e('Run these commands in Terminal to install the required dependencies:'); ?></p>
            <pre class="prettyprint">
# Install Homebrew if not already installed
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install required packages
brew install php wget</pre>

            <h4><?php _e('Step 2: Download the Agent'); ?></h4>
            <p><?php _e('Download and set up the monitoring agent:'); ?></p>
            <pre class="prettyprint">
mkdir -p ~/eyenet-agent
cd ~/eyenet-agent
wget <?php echo baseURL(); ?>agent/macos-agent.php
wget <?php echo baseURL(); ?>agent/macos-agent.conf</pre>

            <h4><?php _e('Step 3: Configure the Agent'); ?></h4>
            <p><?php _e('Edit the configuration file with your server details:'); ?></p>
            <pre class="prettyprint">
# Open the config file
nano ~/eyenet-agent/macos-agent.conf

# Add these settings:
SERVER_ID=<?php echo $server['id']; ?>
API_KEY=<?php echo getConfigValue("api_key"); ?>
API_URL=<?php echo baseURL(); ?></pre>

            <h4><?php _e('Step 4: Set Up Permissions'); ?></h4>
            <p><?php _e('Grant necessary permissions to the agent:'); ?></p>
            <pre class="prettyprint">
chmod +x ~/eyenet-agent/macos-agent.php</pre>

            <h4><?php _e('Step 5: Create Launch Agent'); ?></h4>
            <p><?php _e('Create a launch agent to run the monitoring service:'); ?></p>
            <pre class="prettyprint">
cat > ~/Library/LaunchAgents/com.eyenet.agent.plist << EOL
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>Label</key>
    <string>com.eyenet.agent</string>
    <key>ProgramArguments</key>
    <array>
        <string>/usr/local/bin/php</string>
        <string>~/eyenet-agent/macos-agent.php</string>
    </array>
    <key>RunAtLoad</key>
    <true/>
    <key>KeepAlive</key>
    <true/>
    <key>StandardErrorPath</key>
    <string>~/eyenet-agent/error.log</string>
    <key>StandardOutPath</key>
    <string>~/eyenet-agent/output.log</string>
</dict>
</plist>
EOL</pre>

            <h4><?php _e('Step 6: Start the Agent'); ?></h4>
            <p><?php _e('Load and start the monitoring agent:'); ?></p>
            <pre class="prettyprint">
launchctl load ~/Library/LaunchAgents/com.eyenet.agent.plist</pre>

            <h4><?php _e('Additional Notes'); ?></h4>
            <ul>
                <li><?php _e('The agent requires PHP 7.4 or later'); ?></li>
                <li><?php _e('Some monitoring features require admin privileges'); ?></li>
                <li><?php _e('To monitor system temperature, additional permissions may be required'); ?></li>
                <li><?php _e('Check the log files in ~/eyenet-agent/ for troubleshooting'); ?></li>
            </ul>

            <div class="alert alert-info">
                <h4><i class="icon fa fa-info"></i> <?php _e('Note'); ?></h4>
                <?php _e('After installation, the agent will automatically start sending data to the server. You should see data appearing in the dashboard within a few minutes.'); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> <?php _e('Close'); ?></button>
</div>
