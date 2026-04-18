# Deploying Buzzer App to InfinityFree

Complete step-by-step guide to deploy your buzzer app on InfinityFree for **FREE**!

## ? What You Get with InfinityFree

- **FREE hosting** (no credit card required)
- PHP 8.2 support
- MySQL database (not needed for this app)
- Unlimited bandwidth
- Free subdomain (e.g., `buzzerapp.rf.gd`)
- FTP/File Manager access

## ?? Step 1: Sign Up for InfinityFree

1. Go to **https://www.infinityfree.com/**
2. Click **"Sign Up Now"**
3. Fill in:
   - Email address
   - Password
4. Click **"Create Account"**
5. Check your email and **verify your account**

## ?? Step 2: Create a Website Account

1. Log into **InfinityFree Client Area**
2. Click **"Create Account"**
3. Enter account details:
   - **Username**: Choose a username (e.g., `buzzerapp`)
   - **Domain**: Choose a free subdomain:
     - `yourname.rf.gd`
     - `yourname.epizy.com`
     - `yourname.wuaze.com`
     - Or use your own domain (if you have one)
4. Click **"Create Account"**
5. Wait for account to be created (usually instant)

## ?? Step 3: Upload Your Files

### Option A: Using File Manager (Easiest)

1. In InfinityFree Control Panel, click **"File Manager"**
2. Navigate to **`htdocs`** folder (this is your public web root)
3. Delete the default `index.html` and other default files
4. Click **"Upload"** button
5. Upload these files from the `apache-version/` folder:
   - ? `index.php`
   - ? `host.php`
   - ? `participant.php`
   - ? `api.php`
   - ? `SessionManager.php`
   - ? `.htaccess`
6. Create a new folder called **`data`**
7. Right-click the `data` folder ? **Permissions** ? Set to **755**

### Option B: Using FTP (FileZilla)

1. Download **FileZilla** from https://filezilla-project.org/
2. In InfinityFree Control Panel, find your FTP credentials:
   - **FTP Hostname**: (e.g., `ftpupload.net`)
   - **FTP Username**: (your account username)
   - **FTP Password**: (shown in control panel)
3. Open FileZilla and connect using these credentials
4. Navigate to **`htdocs`** on the right side (remote server)
5. On the left side (local), navigate to your `apache-version/` folder
6. Select and drag these files to `htdocs`:
   - `index.php`
   - `host.php`
   - `participant.php`
   - `api.php`
   - `SessionManager.php`
   - `.htaccess`
7. Create `data` folder on server
8. Right-click `data` ? File permissions ? Set to `755`

## ?? Step 4: Configure Permissions

This is **CRITICAL** for InfinityFree:

1. In File Manager, find the **`data`** folder
2. Right-click ? **Change Permissions**
3. Set to: **755** (Read, Write, Execute for owner)
4. Click **Apply**

## ?? Step 5: Test Your App

1. Open your browser
2. Go to your website: `http://yourusername.rf.gd/`
3. You should see the Buzzer App home page
4. Test the Host view: `http://yourusername.rf.gd/host.php`
5. Test the Participant view: `http://yourusername.rf.gd/participant.php`

### Testing Steps:
1. Click **"Host"** ? **"Create Session"**
2. Note the 6-character session key
3. Open a new browser tab/window
4. Go to **"Participant"**
5. Enter your name and the session key
6. Click **"Join Session"**
7. Try buzzing in!

## ?? Important InfinityFree Limitations

InfinityFree has some restrictions to be aware of:

### 1. **Execution Time Limit**
- Scripts timeout after 30-60 seconds
- Long-polling is optimized for 20-30 seconds

### 2. **File Uploads**
- Data is stored in JSON files (no database needed)
- Files are limited to 10MB (more than enough for sessions)

### 3. **Hits Limit**
- 50,000 hits per day (plenty for small to medium groups)

### 4. **No Ads**
- InfinityFree is completely ad-free!

### 5. **HTTPS/SSL**
- Free SSL available through Cloudflare integration

## ?? Step 6: Enable HTTPS (Optional but Recommended)

1. In InfinityFree Control Panel, go to **"Cloudflare"**
2. Click **"Enable Cloudflare"**
3. Follow the wizard to set up free SSL
4. Your site will be accessible via `https://yourusername.rf.gd/`

## ?? Troubleshooting

### Problem: "500 Internal Server Error"
**Solution:**
- Check that `.htaccess` is uploaded correctly
- Verify file permissions (644 for files, 755 for folders)
- Check PHP error logs in Control Panel

### Problem: "Session not found" error
**Solution:**
- Make sure `data` folder exists and has 755 permissions
- Verify `SessionManager.php` is uploaded
- Check that `data/sessions.json` can be created

### Problem: Updates not appearing in real-time
**Solution:**
- This is normal on free hosting with execution limits
- Refresh the page if updates seem slow
- Updates should appear within 5-10 seconds

### Problem: Participants disconnecting
**Solution:**
- InfinityFree has execution time limits
- The app automatically handles this with ping/timeout logic
- Participants stay connected for 60 seconds of inactivity

## ?? File Structure on Server

After upload, your `htdocs` folder should look like this:

```
htdocs/
??? index.php              # Home page
??? host.php               # Host interface
??? participant.php        # Participant interface
??? api.php                # API backend
??? SessionManager.php     # Session logic
??? .htaccess              # Apache config
??? data/                  # Data storage (755)
    ??? sessions.json      # Created automatically
```

## ?? Testing Checklist

- [ ] Can access home page at `http://yourusername.rf.gd/`
- [ ] Can create session as host
- [ ] Session key is displayed
- [ ] Can join session as participant
- [ ] Can buzz in as participant
- [ ] Host sees participant buzz in real-time
- [ ] Can award/remove points
- [ ] Leaderboard updates correctly
- [ ] Can reset buzzers
- [ ] Can end session

## ?? Going Live

Once everything works:

1. Share your URL: `http://yourusername.rf.gd/`
2. Participants visit: `http://yourusername.rf.gd/participant.php`
3. Host visits: `http://yourusername.rf.gd/host.php`

## ?? Tips for Best Performance

1. **Use during off-peak hours** if possible
2. **Keep sessions small** (10-20 participants max on free hosting)
3. **Clear old sessions** by deleting `data/sessions.json` periodically
4. **Enable Cloudflare** for better performance and HTTPS

## ?? Upgrade Options

If you need more power:
- **InfinityFree Premium**: $5/month, better performance
- **DigitalOcean**: $6/month, full VPS control
- **Azure App Service**: $13/month, use .NET version with SignalR

## ?? Mobile Support

The app works great on mobile devices! Participants can join from their phones and buzz in.

## ?? For Classrooms

Perfect for:
- Quiz games
- Trivia competitions
- Class participation tracking
- Team competitions

---

## Need Help?

If you encounter issues:

1. Check InfinityFree support forum: https://forum.infinityfree.com/
2. Review PHP error logs in Control Panel
3. Verify all files are uploaded correctly
4. Ensure `data` folder has correct permissions

**Your buzzer app should now be live and free! ??**
