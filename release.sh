#!/bin/bash

# Prompt the user for a version number
echo "Release picostrap5:"


# Define a subfolder name
SUBFOLDER="../picostrap5-deploy"
NAMEZIP="picostrap5"

# Create the subfolder if it doesn't exist
mkdir -p "$SUBFOLDER"

# Copy the files into the subfolder
cp -R . "$SUBFOLDER/"

rm -rf "$SUBFOLDER"/.git
rm -rf "$SUBFOLDER"/.gitignore
rm -rf "$SUBFOLDER"/.DS_Store

# Zip the subfolder
zip -r "${NAMEZIP}.zip" "$SUBFOLDER/"

# Optionally, remove the subfolder after zipping
rm -rf "$SUBFOLDER"