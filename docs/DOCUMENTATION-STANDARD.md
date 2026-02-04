# WPSeed Documentation Standard

## Overview

WPSeed uses a **dual-documentation system** that separates technical reference from user training, with automatic sync to plugin websites.

## Documentation Types

### 1. Technical Documentation (Files)
**Format**: Markdown (.md) or Text (.txt)  
**Location**: `/docs/` folder  
**Audience**: Developers, system administrators  
**Version Control**: Git tracked

**Files**:
- `INSTALLATION.md` - Setup instructions
- `UNINSTALLATION.md` - Removal procedures
- `TROUBLESHOOTING.md` - Common issues & fixes
- `API-REFERENCE.md` - Code documentation
- `DEVELOPER-GUIDE.md` - Architecture & patterns
- `CHANGELOG.md` - Version history

### 2. Training Documentation (Database)
**Format**: Database records  
**Location**: `wp_wpseed_lessons` table  
**Audience**: End users, plugin users  
**Version Control**: Exported via REST API

**Content Types**:
- Getting Started tutorials
- Feature walkthroughs
- Video embeds
- Interactive guides
- Best practices
- FAQ

## Built-in Learning Center

**Access**: WPSeed → Learning Center  
**Export**: `/wp-json/wpseed/v1/education/export`

## Website Sync

Website fetches lessons via REST API and displays as HTML pages. Single source of truth in plugin, auto-syncs to website.

## Benefits

✅ Single source of truth  
✅ Auto-sync to website  
✅ Version controlled (technical)  
✅ User-friendly (training)  
✅ SEO-friendly website display
