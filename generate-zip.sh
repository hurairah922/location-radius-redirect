#!/bin/bash

PLUGIN_DIR="location-radius-redirect"
ZIP_NAME="${PLUGIN_DIR}.zip"

cd .. || exit

zip -r "$ZIP_NAME" "$PLUGIN_DIR" \
  -x "$PLUGIN_DIR/*.sh" \
     "$PLUGIN_DIR/.git*" \
     "$PLUGIN_DIR/.idea*" \
     "$PLUGIN_DIR/*.zip" \
     "$PLUGIN_DIR/generate.sh" \
     "$PLUGIN_DIR/package.json" \
     "$PLUGIN_DIR/composer.lock" \
     "$PLUGIN_DIR/composer.json" \
     "$PLUGIN_DIR/wp-config" \
     "$PLUGIN_DIR/debug.log" \
     "$PLUGIN_DIR/webpack.config.js" \
     "$PLUGIN_DIR/node_modules/*"

mv "$ZIP_NAME" ~/Downloads/
echo "Created and moved $ZIP_NAME successfully!"
