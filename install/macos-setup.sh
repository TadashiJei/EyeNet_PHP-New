#!/bin/bash

# EyeNet MacOS Setup Script
# This script automates the setup process for EyeNet on MacOS

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored messages
print_message() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check parameters
if [ "$#" -ne 2 ]; then
    print_error "Usage: $0 <api_key> <server_url>"
    exit 1
fi

API_KEY=$1
SERVER_URL=$2
INSTALL_DIR="$HOME/eyenet-agent"

# Create installation directory
print_message "Creating installation directory..."
mkdir -p "$INSTALL_DIR"
mkdir -p "$INSTALL_DIR/logs"
mkdir -p "$INSTALL_DIR/config"

# Download required components
print_message "Downloading agent components..."
curl -s "$SERVER_URL/assets/agent/eyenet-agent.php" -o "$INSTALL_DIR/eyenet-agent.php"
curl -s "$SERVER_URL/assets/agent/config.template.php" -o "$INSTALL_DIR/config/config.php"

# Configure the agent
print_message "Configuring agent..."
sed -i '' "s/API_KEY_HERE/$API_KEY/g" "$INSTALL_DIR/config/config.php"
sed -i '' "s#SERVER_URL_HERE#$SERVER_URL#g" "$INSTALL_DIR/config/config.php"

# Create launch agent plist
cat > "$HOME/Library/LaunchAgents/com.eyenet.agent.plist" << EOL
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>Label</key>
    <string>com.eyenet.agent</string>
    <key>ProgramArguments</key>
    <array>
        <string>/usr/local/bin/php</string>
        <string>${INSTALL_DIR}/eyenet-agent.php</string>
    </array>
    <key>RunAtLoad</key>
    <true/>
    <key>StartInterval</key>
    <integer>300</integer>
    <key>StandardErrorPath</key>
    <string>${INSTALL_DIR}/logs/error.log</string>
    <key>StandardOutPath</key>
    <string>${INSTALL_DIR}/logs/output.log</string>
</dict>
</plist>
EOL

# Check if PHP is installed via Homebrew
if ! command -v php >/dev/null 2>&1; then
    print_warning "PHP not found in path. Installing via Homebrew..."
    if ! command -v brew >/dev/null 2>&1; then
        print_message "Installing Homebrew..."
        /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
    fi
    brew install php
fi

# Load the launch agent
print_message "Loading launch agent..."
# Unload existing agent if it exists
launchctl unload "$HOME/Library/LaunchAgents/com.eyenet.agent.plist" 2>/dev/null

# Load the new agent
launchctl load "$HOME/Library/LaunchAgents/com.eyenet.agent.plist"

print_message "Installation completed successfully!"
print_message "Agent installed at: $INSTALL_DIR"
print_message "Logs directory: $INSTALL_DIR/logs"
print_message "Configuration file: $INSTALL_DIR/config/config.php"

# Test the connection
print_message "Testing connection to server..."
php "$INSTALL_DIR/eyenet-agent.php" --test

exit 0
