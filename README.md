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
├── index.php
├── bill.php
├── connect.php
├── styles/
│ └── style.css
├── scripts/
│ └── main.js
├── assets/
│ └── [images/icons]
├── database/
│ └── fuelbill.sql
└── README.md

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

## 🧠 How It Works

- User selects fuel type and quantity
- PHP calculates total price based on pre-set fuel rates
- The bill is generated and optionally stored in the database

## 📈 Future Enhancements

- Admin dashboard for fuel rate updates
- Print/download bill as PDF
- Add authentication (login system)
- Mobile-friendly improvements

## 👩‍💻 Author
Nidhi Dwivedi
GitHub: @Nidhi-dwivedi




