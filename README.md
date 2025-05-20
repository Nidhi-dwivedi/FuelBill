# ⛽ FuelBill – Petrol Pump Billing System

FuelBill is a PHP-based billing system designed for petrol pump stations. It helps manage fuel transactions, calculate bills based on fuel types and quantity, and keep records of customer purchases.

---

## 📌 Features

- Add customer and vehicle details
- Select fuel type (Petrol, Diesel, etc.)
- Automatic bill calculation based on quantity and price
- View transaction history
- Simple and clean UI
- Responsive design

---

## 💻 Technologies Used

- **Frontend**: HTML, CSS, JavaScript, Bootstrap
- **Backend**: PHP
- **Database**: MySQL (using XAMPP or similar stack)

---

## 📁 Folder Structure
FuelBill/
  ├── index.php               # Homepage or billing interface
  ├── bill.php                # Bill calculation and display
  ├── connect.php             # Database connection logic
  ├── database/
  │   └── fuelbill.sql        # SQL file for setting up DB
  ├── styles/
  │   └── style.css           # Custom CSS styles
  ├── scripts/
  │   └── main.js             # Optional JavaScript (if used)
  ├── assets/
  │   └── [images/icons]      # Images or icons used in UI
  ├── .gitignore              # (Optional) Git ignored files
  └── README.md               # Project overview and instructions


---

## 🛠️ Setup Instructions

### 1. Clone the repository:
```bash
git clone https://github.com/Nidhi-dwivedi/FuelBill.git
```
```bash
cd FuelBill
```

### 2. Set up with XAMPP:

- Place the FuelBill folder inside C:/xampp/htdocs/
- Start Apache and MySQL from XAMPP Control Panel

### 3. Import the database:

- Open http://localhost/phpmyadmin/
- Create a new database (e.g., fuelbill)
- Import fuelbill.sql file from the database/ folder

### 4. Run the app:

- Go to your browser and open:
  http://localhost/FuelBill/

---

## 🧠 How It Works

- User selects fuel type and quantity
- PHP calculates total price based on pre-set fuel rates
- The bill is generated and optionally stored in the database

---

## 📈 Future Enhancements

- Admin dashboard for fuel rate updates
- Print/download bill as PDF
- Add authentication (login system)
- Mobile-friendly improvements

--- 

## 👩‍💻 Author
Nidhi Dwivedi
GitHub: @Nidhi-dwivedi




