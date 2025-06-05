# To use the contact us form locally:

## For Windows:

### Step 1. Cloning the repository either directly or using Git (if installed on your device)

#### (i) If directly (Recommended for all users):

Click the green "<> Code" button on the repository page.  
Under “Local,” click the **Download ZIP** button.  
Download the ZIP to your desired folder.  
Extract it, open the folder, then move to **Step 2**.

#### (ii) If using Git:

Open terminal in the directory where you want to clone the repository.  
Run the following command:

```bash
git clone https://github.com/TernCoders/TernCoders.github.io
```

Open the cloned folder in File Explorer and move to **Step 2**.

### Step 2. Installing necessary programs:

#### First, verify whether these tools are already installed:

```bash
php --version
node --version
mysql --version
```

If any of them are missing, follow the steps below:

#### Installing PHP:

- Open the `Imp packages` folder and extract the PHP zip file into your preferred location.
- Press the Windows key → search for “Environment Variables” → click **Edit the system environment variables**.
- In **System Properties**, click **Environment Variables** → under _System Variables_, find and edit `Path`.
- Click **New**, then **Browse** to your PHP folder and select it.
- Press **OK** on all dialogs to apply the changes.

#### Installing Node.js and npm:

- Open the `Imp packages` folder and run the Node.js `.msi` installer.
- During installation, enable **"Automatically install necessary tools..."**.
- Click **Next**, install, and then **Finish**.
- Wait for additional tool installations in the terminal popup that follows.

#### Installing MySQL:

- Open the `Imp packages` folder and run the MySQL `.msi` installer.
- Choose custom and click next.
- Then go MySQL Servers > MySQL Server > MySQL Server 8.0 and choose the topmost latest version and click the top green arrow to add.
- Then go Applications > MySQL Workbench > MySQL Workbench 8.0 and choose the topmost latest version and click the same top green arrow to add.
- Then click next and execute to download the chosen programs and follow up with the setup and chose your password and remember to keep it somewhere safe but accessible for yourself and complete the rest of the setup.
- After installation, press the Windows key → search for “Environment Variables” → click **Edit the system environment variables**.
- In **System Properties**, click **Environment Variables** → under _System Variables_, find and edit `Path`.
- Click **New**, then **Browse** to your MySQL folder then MySQL server 8.0 folder then select the bin folder.
- Press **OK** on all dialogs to apply the changes.
- Important - Restart the terminal
- Open contact.php located in the cloned folder and go to the following line and **change the (your_password)** to your chosen MySQL root password.

```php
$pdo = new PDO("mysql:host=localhost;dbname=contact_form;charset=utf8", "root", "(your_password)");
```

Then open MySQL workbench and choose local instance under MySQL connection and sign in with your chosen password. Then, paste the following into query 1 and click the thunderbolt to execute:

```sql
CREATE DATABASE contact_form;
USE contact_form;
CREATE TABLE messages (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) DEFAULT 'Not Given',
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(10) DEFAULT 'Not Given',
    subject VARCHAR(255) NOT NULL,
    message TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Then, close the program and go to the next part.

#### Then verify Composer and Browser-Sync:

```bash
composer --version
browser-sync --version
```

#### Installing Composer:

- Run the Composer installer from the `Imp packages` folder.
- Choose Developer Mode and install to your preferred directory.
- When prompted for PHP, browse to and select `php.exe` from your installed PHP folder.
- Complete the installation. To install PHPMailer in the project:

```bash
composer require phpmailer/phpmailer
```

#### Installing Browser-Sync:

Use this command:

```bash
npm install -g browser-sync
```

### Step 3. Running and testing:

Open terminal, navigate to the cloned project folder, and start the PHP server:

```bash
php -S localhost:8000
```

In another terminal window, navigate to the same folder and start Browser-Sync:

```bash
browser-sync start --proxy "localhost:8000" --files "**/*"
```

Now, open your browser and scroll to the footer of the `index.html` page.  
Fill in the form and submit.  
A browser alert will confirm the message was sent.  
You will receive a confirmation email as well.

### To view stored submissions in the database:

Login again to MySQL Workbench and paste the following by replacing the code that was used to create the databse and table as it is already executed:

```sql
USE contact_form;
SELECT * FROM messages;
```

Then click the thunderbolt button ad you will able to view stored submissions. **Tip:** To maximise the view of stored submissions close the three sidebars by using the three buttons on top right.

## For MacOS/Linux:

We unfortunately don’t have contributors with these operating systems yet, so we cannot offer help at this time.

## For any issues, contact:

- [terncoders@gmail.com](mailto:terncoders@gmail.com)
