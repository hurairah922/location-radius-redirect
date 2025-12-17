#!/bin/bash

# Define the name of the zip file
ZIP_NAME="location-radius-redirect.zip"
rm -rf ZIP_NAME

# Find and zip all files and folders except .git, .idea, and *.zip
zip -r "$ZIP_NAME" . -x "*.sh" "*.git*" "*.idea*" "*.zip" "generate.sh" "package.json" "composer.lock" "composer.json" "wp-config" "debug.log" "webpack.config.js" "node_modules/*"
# Output message

mv $ZIP_NAME ~/Downloads/
echo "Created and Moved $ZIP_NAME successfully!"
