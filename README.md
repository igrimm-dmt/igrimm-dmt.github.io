# Apache Buzzer App

A real-time buzzer application for trivia games, classrooms, or any interactive event where participants need to buzz in.

## Requirements

- Apache 2.4+
- PHP 7.4+ (PHP 8.0+ recommended)
- mod_rewrite enabled (optional, for clean URLs)

## Installation

1. Copy all files to your Apache web directory (e.g., `/var/www/html/buzzer/` or `C:\xampp\htdocs\buzzer\`)

2. Ensure the `data` directory is writable by the web server:
   ```bash
   chmod 777 data/
   ```
   Or on Windows, give the web server user write permissions to the `data` folder.

3. Make sure PHP has write permissions to create the `data` directory if it doesn't exist.

## Configuration

### Apache Configuration

No special Apache configuration is required. However, for better performance, you may want to adjust these settings in your `php.ini`:

```ini
max_execution_time = 60
memory_limit = 128M
```

### Optional: Clean URLs with .htaccess

Create a `.htaccess` file for cleaner URLs (optional):

```apache
RewriteEngine On
RewriteBase /buzzer/

# Redirect index to index.php
RewriteRule ^$ index.php [L]

# Handle other routes
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ $1.php [L,QSA]
```

## Usage

### Starting a Session (Host)

1. Navigate to `host.php` (or `http://yourserver/buzzer/host.php`)
2. Click "Create Session"
3. Share the 6-character session key with participants

### Joining a Session (Participant)

1. Navigate to `participant.php` (or `http://yourserver/buzzer/participant.php`)
2. Enter your name
3. Enter the session key provided by the host
4. Click "Join Session"
5. Press the buzzer button when you want to buzz in!

## Features

- **Real-time updates** using long-polling (works on any Apache server)
- **Scoring system** - hosts can award or remove points
- **Leaderboard** - see who's in the lead
- **Answer submission** - participants can optionally submit answers when buzzing
- **Automatic cleanup** - inactive participants are removed after 60 seconds
- **Session management** - hosts can reset buzzers or end sessions

## How It Works

Unlike the .NET version which uses SignalR/WebSockets, this PHP version uses **long-polling** to achieve real-time updates:

1. Clients send requests to the server asking for updates
2. The server holds the request until there's new data (up to 30 seconds)
3. When data changes, the server immediately responds
4. Client receives the update and immediately sends a new poll request

This approach:
- Works on **any** Apache server (no WebSocket support needed)
- Requires no special server configuration
- Uses file-based storage (JSON) for simplicity
- Scales reasonably well for small to medium groups

## File Structure

```
buzzer/
??? index.php           # Landing page
??? host.php            # Host interface
??? participant.php     # Participant interface
??? api.php             # API endpoint for all actions
??? SessionManager.php  # Session management logic
??? data/               # Data storage (auto-created)
?   ??? sessions.json   # Session data file
??? README.md           # This file
```

## Troubleshooting

### Participants not connecting
- Ensure the `data/` directory exists and is writable
- Check Apache error logs for PHP errors
- Verify PHP version is 7.4 or higher

### Updates not appearing in real-time
- Check that `max_execution_time` in PHP is set to at least 60 seconds
- Ensure your Apache configuration doesn't have overly aggressive timeouts
- Check browser console for JavaScript errors

### Session data lost after server restart
- This is expected - sessions are stored in files, not a database
- For persistent storage, modify `SessionManager.php` to use a database

## Scaling Considerations

For larger deployments:

1. **Use a database** instead of JSON files (MySQL, PostgreSQL, SQLite)
2. **Implement Redis** for faster session storage
3. **Use WebSockets** with Apache modules like `mod_proxy_wstunnel`
4. **Load balancing** - you'll need sticky sessions or shared storage

## Security Notes

- Input is sanitized using `htmlspecialchars()` equivalent in JavaScript
- Session keys are randomly generated
- Consider adding rate limiting for production use
- Add HTTPS for secure connections

## Differences from .NET Version

| Feature | .NET Version | PHP Version |
|---------|-------------|-------------|
| Real-time | SignalR (WebSockets) | Long-polling |
| Storage | In-memory | File-based (JSON) |
| Server | Kestrel/IIS | Apache |
| Language | C# | PHP + JavaScript |
| Scaling | Vertical/Horizontal | Primarily Vertical |

## License

Free to use and modify for any purpose.
