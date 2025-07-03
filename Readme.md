# 💰 Money Lend — Loan Management Web App
A simple PHP + MySQL web application to manage personal loans, logins, and user registrations.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
## 🚀 Simple Installation Guide
✅ 1. Clone or Download Project  
   └── Place it in your web server directory (e.g., `htdocs` for XAMPP)
bash
git clone https://github.com/yourusername/money_lend.git
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
✅ 2. Run Setup
Open your browser and go to:
http://localhost/money_lend/setup.php
✔️ This will automatically:
- Create the `money_lend` database
- Create all required tables (`users`, `applications`, etc.)
- Insert a sample admin user (optional)
🟢 You’ll see: `✅ Setup completed successfully!`
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
✅ 3. Delete Setup File (for security)
rm setup.php
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
✅ 4. Access the Application
Go to:
http://localhost/money_lend/1index.html
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
✅ 5. First Use
🔹 Click **"Create"** to register  
🔹 Or login with:

Email: admin@example.com
Password: admin123
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
## ⚙️ System Requirements
| Requirement | Version            |
|-------------|--------------------|
| PHP         | 7.4 or newer       |
| MySQL       | 5.7 or newer       |
| Web Server  | Apache / Nginx     |
| Disk Space  | Minimum 10MB       |
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
## 🛠️ Troubleshooting Tips
❓ MySQL not running? → Start via XAMPP Control Panel  
❓ Upload issues?     → Ensure `/uploads/` folder exists and is writable  
❓ Errors?            → Check `error_log` or browser console
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
## 📁 Project Structure
money_lend/
├── 1index.html          # Login page
├── 2login.php           # Login logic
├── 3register.html       # Registration form
├── 4register.php        # Handles registration & uploads
├── dashboard.php        # User dashboard
├── setup.php            # Auto setup script (run once)
├── init_db.php          # Database/tables creator
├── /uploads/            # Uploaded user files
├── styles.css           # Global styles
├── README.md            # This file!
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
✅ Your loan application system is now ready to use!
