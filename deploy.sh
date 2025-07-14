#!/bin/bash

# Countdown Timer for WooCommerce - WordPress.org Deployment Script
# This script handles the Git to SVN deployment process

set -e

# Configuration
PLUGIN_SLUG="countdown-timer-for-woocommerce"
GITHUB_REPO="twoelevenjay/woocommerce-countdown-timer"
SVN_URL="https://plugins.svn.wordpress.org/${PLUGIN_SLUG}"
TEMP_DIR="/tmp/${PLUGIN_SLUG}-deploy"
ASSETS_DIR="assets"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Starting deployment process for ${PLUGIN_SLUG}${NC}"

# Check if we're on master/main branch
CURRENT_BRANCH=$(git branch --show-current)
if [[ "$CURRENT_BRANCH" != "master" && "$CURRENT_BRANCH" != "main" ]]; then
    echo -e "${RED}Error: You must be on master or main branch to deploy${NC}"
    exit 1
fi

# Check for uncommitted changes
if ! git diff-index --quiet HEAD --; then
    echo -e "${RED}Error: You have uncommitted changes${NC}"
    exit 1
fi

# Get version from main plugin file
VERSION=$(grep "Version:" countdown-timer-for-woocommerce.php | awk -F' ' '{print $NF}' | tr -d '\r')
echo -e "${YELLOW}Deploying version: ${VERSION}${NC}"

# Check if tag exists
if git rev-parse "v${VERSION}" >/dev/null 2>&1; then
    echo -e "${RED}Error: Tag v${VERSION} already exists${NC}"
    exit 1
fi

# Create and push tag
echo -e "${GREEN}Creating tag v${VERSION}${NC}"
git tag -a "v${VERSION}" -m "Version ${VERSION}"
git push origin "v${VERSION}"

# Clean up any existing temp directory
rm -rf "$TEMP_DIR"
mkdir -p "$TEMP_DIR"

# Checkout SVN repository
echo -e "${GREEN}Checking out SVN repository...${NC}"
svn checkout "$SVN_URL" "$TEMP_DIR" --depth immediates
svn update --quiet "$TEMP_DIR/trunk" --set-depth infinity
svn update --quiet "$TEMP_DIR/assets" --set-depth infinity
svn update --quiet "$TEMP_DIR/tags" --set-depth infinity

# Copy files to trunk
echo -e "${GREEN}Copying files to trunk...${NC}"
rsync -rc --exclude-from=".distignore" ./ "$TEMP_DIR/trunk/" --delete --delete-excluded

# Copy assets
echo -e "${GREEN}Copying assets...${NC}"
if [ -d "$ASSETS_DIR" ]; then
    # Copy WordPress.org assets (banner, icon, screenshots)
    cp -f "$ASSETS_DIR"/*.png "$TEMP_DIR/assets/" 2>/dev/null || true
    cp -f "$ASSETS_DIR"/*.jpg "$TEMP_DIR/assets/" 2>/dev/null || true
    cp -f "$ASSETS_DIR"/*.jpeg "$TEMP_DIR/assets/" 2>/dev/null || true
fi

# Add any new files
cd "$TEMP_DIR"
svn status | grep -v "^.[ \t]*\..*" | grep "^?" | awk '{print $2}' | xargs -I {} svn add {}@

# Create tag
echo -e "${GREEN}Creating SVN tag...${NC}"
svn copy "trunk" "tags/${VERSION}"

# Commit to SVN
echo -e "${GREEN}Committing to WordPress.org SVN...${NC}"
svn commit -m "Version ${VERSION}" --username "$SVN_USERNAME" --password "$SVN_PASSWORD" --non-interactive

# Clean up
rm -rf "$TEMP_DIR"

echo -e "${GREEN}✓ Deployment complete!${NC}"
echo -e "${GREEN}Plugin URL: https://wordpress.org/plugins/${PLUGIN_SLUG}/${NC}"