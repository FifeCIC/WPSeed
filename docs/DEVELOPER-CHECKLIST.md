# WPSeed Developer Checklist

## Pre-Release Checklist

### Code Quality
- [ ] Run PHP_CodeSniffer with WordPress Coding Standards
  ```bash
  phpcs --standard=WordPress /path/to/plugin
  ```
- [ ] Install: [WordPress Coding Standards](https://github.com/WordPress/WordPress-Coding-Standards)
- [ ] Fix issues: `phpcbf --standard=WordPress /path/to/plugin`

### Security
- [ ] Install [Plugin Check](https://wordpress.org/plugins/plugin-check/) plugin
- [ ] Run security scan in WordPress admin
- [ ] Check for SQL injection vulnerabilities
- [ ] Verify nonce usage on forms
- [ ] Sanitize all inputs
- [ ] Escape all outputs

### Performance
- [ ] Test with [Query Monitor](https://wordpress.org/plugins/query-monitor/)
- [ ] Check database queries
- [ ] Verify no N+1 query issues
- [ ] Test asset loading (no unnecessary scripts)

### Compatibility
- [ ] Test on WordPress 5.0+
- [ ] Test on PHP 7.4, 8.0, 8.1
- [ ] Test with common themes (Twenty Twenty-Four, Astra)
- [ ] Test with common plugins (WooCommerce, Yoast SEO)
- [ ] Test multisite compatibility

### Documentation
- [ ] Update README.md
- [ ] Update CHANGELOG.md
- [ ] Sync docs to GitHub (`WPSeed → GitHub Sync`)
- [ ] Add inline code comments
- [ ] Document all hooks/filters

### Testing
- [ ] Run PHPUnit tests: `phpunit`
- [ ] Test activation/deactivation
- [ ] Test uninstall cleanup
- [ ] Test on fresh WordPress install
- [ ] Test with WP_DEBUG enabled

### WordPress.org Submission
- [ ] Follow [Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
- [ ] Create plugin banner (772×250px)
- [ ] Create plugin icon (256×256px)
- [ ] Add screenshots to `/assets/`
- [ ] Write clear description
- [ ] Set proper tags (max 12)

### GitHub
- [ ] Push to GitHub repository
- [ ] Create release tag
- [ ] Update GitHub README
- [ ] Add license file
- [ ] Enable GitHub Actions

## Recommended Tools

### WordPress Plugins
- [Plugin Check](https://wordpress.org/plugins/plugin-check/) - Official WP.org checker
- [Query Monitor](https://wordpress.org/plugins/query-monitor/) - Debug queries
- [Debug Bar](https://wordpress.org/plugins/debug-bar/) - Debug info

### Command Line
```bash
# Install WordPress Coding Standards
composer global require wp-coding-standards/wpcs
phpcs --config-set installed_paths ~/.composer/vendor/wp-coding-standards/wpcs

# Check code
phpcs --standard=WordPress /path/to/plugin

# Auto-fix issues
phpcbf --standard=WordPress /path/to/plugin

# Run tests
phpunit

# WP-CLI checks
wp plugin verify-checksums wpseed
```

### IDE Extensions
- **VS Code**: PHP Sniffer & Beautifier
- **PHPStorm**: WordPress Coding Standards inspection
- **Sublime**: PHP_CodeSniffer plugin

## Quick Commands

```bash
# Full check
phpcs --standard=WordPress --extensions=php /path/to/plugin

# Ignore warnings
phpcs --standard=WordPress --warning-severity=0 /path/to/plugin

# Check specific file
phpcs --standard=WordPress includes/classes/example.php

# Generate report
phpcs --standard=WordPress --report=summary /path/to/plugin
```

## Before Each Release

1. Update version in `wpseed.php`
2. Update version in `readme.txt`
3. Update `CHANGELOG.md`
4. Run full test suite
5. Sync docs to GitHub
6. Create Git tag
7. Build release package
8. Test package on clean install

## Resources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [Plugin Security Best Practices](https://developer.wordpress.org/plugins/security/)
- [WP-CLI Documentation](https://wp-cli.org/)
