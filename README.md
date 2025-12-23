# SCMI Costing System

> ระบบคำนวณต้นทุนและวิเคราะห์กำไรสำหรับ SCMI

[![PHP Version](https://img.shields.io/badge/PHP-8.2-blue.svg)](https://www.php.net/)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-3.x-orange.svg)](https://codeigniter.com/)
[![License](https://img.shields.io/badge/License-Private-red.svg)]()

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Recent Updates](#recent-updates)
- [Contributing](#contributing)

---

## 🎯 Overview

**SCMI Costing System** เป็นระบบบริหารจัดการต้นทุนการผลิตและวิเคราะห์กำไรสำหรับโรงงานผลิต โดยรองรับการคำนวณต้นทุนจากข้อมูล Excel และแสดงผลในรูปแบบรายงานที่เข้าใจง่าย พร้อมกราฟวิเคราะห์แบบ Interactive

### Key Capabilities
- 📊 **Cost Calculation** - คำนวณต้นทุนการผลิตจากข้อมูล Excel
- 📈 **Margin Analysis** - วิเคราะห์กำไรและแนวโน้มรายเดือน
- 🏭 **Machine Performance** - เปรียบเทียบประสิทธิภาพเครื่องจักร
- 👥 **User Management** - จัดการผู้ใช้งานและสิทธิ์การเข้าถึง
- 📁 **File Upload** - อัพโหลดและจัดการไฟล์ข้อมูลต้นทุน

---

## ✨ Features

### 1. Dashboard (การคำนวณต้นทุน)
- แสดงข้อมูลต้นทุนแบบ Real-time
- เลือกดูข้อมูลตามเดือน/ปี
- แสดงผลข้อมูลจากหลายเครื่องจักร (DBCD4, DBCD5, DBCD6, DBCD6LX, DBK6)
- Grid view พร้อมฟังก์ชัน Filter และ Search

### 2. File Upload & Management
- อัพโหลดไฟล์ Excel (.xlsx)
- ตรวจสอบความถูกต้องของไฟล์
- จัดเก็บประวัติการอัพโหลด
- รองรับหลาย Sheet ในไฟล์เดียว

### 3. Cost Calculation
- คำนวณต้นทุนอัตโนมัติจากข้อมูล Excel
- รองรับสูตรคำนวณที่ซับซ้อน
- บันทึกผลการคำนวณลงฐานข้อมูล
- แสดงผลการคำนวณแบบ Real-time

### 4. Margin Analysis Report 📊 (NEW)
- **KPI Dashboard** - ยอดขายรวม, ต้นทุนรวม, กำไรรวม, Margin เฉลี่ย
- **Margin Distribution** - การกระจายสินค้าตามช่วงกำไร (0-5%, 5-10%, 10-15%, 15-20%, 20%+)
- **6-Month Trend** - กราฟแนวโน้มกำไรย้อนหลัง 6 เดือน
- **Top Products** - สินค้าที่ทำกำไรสูงสุด 5 อันดับ
- **Low Margin Alert** - เตือนสินค้าที่มี Margin ต่ำกว่า 6% พร้อมแนะนำการปรับราคา
- **Machine Performance** - เปรียบเทียบกำไรแยกตามเครื่องจักร
- **Interactive Charts** - กราฟแบบ Interactive ด้วย Chart.js

### 5. Data Management
- จัดการข้อมูลที่อัพโหลด
- ลบข้อมูลเก่า (Soft Delete)
- ดูประวัติการอัพโหลด

### 6. User Management
- เพิ่ม/แก้ไข/ลบผู้ใช้งาน
- กำหนดสิทธิ์การเข้าถึง (Admin/User)
- ระบบ Login/Logout

---

## 🛠 Tech Stack

### Backend
- **PHP 8.2** - Server-side scripting
- **CodeIgniter 3.x** - PHP Framework
- **MySQL/MariaDB** - Database

### Frontend
- **HTML5/CSS3** - Structure & Styling
- **JavaScript/jQuery** - Client-side scripting
- **Chart.js 4.4.0** - Interactive charts
- **AG-Grid** - Data grid component
- **Bootstrap 4** - UI framework

### Design
- **macOS Glass UI Theme** - Modern glassmorphism design
- **Responsive Design** - Mobile-friendly interface

### Libraries & Tools
- **PhpSpreadsheet** - Excel file processing
- **Git** - Version control

---

## 📦 Installation

### Prerequisites
- PHP 8.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx web server
- Composer (optional)

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/samannois-ctrl/szmi--costing.git
   cd szmi--costing
   ```

2. **Configure database**
   - Create a new MySQL database
   - Import the database schema (if available)
   - Update `application/config/database.php` with your credentials

3. **Set permissions**
   ```bash
   chmod -R 755 uploads/
   chmod -R 755 application/cache/
   chmod -R 755 application/logs/
   ```

4. **Configure base URL**
   - Edit `application/config/config.php`
   - Set `$config['base_url']` to your domain

5. **Access the application**
   - Navigate to `http://your-domain.com`
   - Default login: `admin` / `1234`

---

## 🚀 Usage

### 1. Upload Cost Data
1. Navigate to **อัพโหลดไฟล์** (File Upload)
2. Select month and year
3. Upload Excel file (.xlsx)
4. System will validate and store the data

### 2. Calculate Costs
1. Go to **คำนวณต้นทุน** (Calculate Costs)
2. Select month and year
3. Click **คำนวณ** (Calculate)
4. Wait for calculation to complete

### 3. View Dashboard
1. Navigate to **การคำนวณต้นทุน** (Dashboard)
2. Select month and year
3. Choose machine sheet to view
4. Use filter and search to find specific data

### 4. View Margin Analysis Report
1. Go to **รายงาน** (Report)
2. Select month and year with calculated data
3. View interactive charts and tables
4. Analyze margin trends and product performance

---

## 📁 Project Structure

```
scmicosting.com/
├── application/
│   ├── controllers/        # Controllers
│   │   ├── Cost.php       # Main cost calculation controller
│   │   ├── Main.php       # Dashboard controller
│   │   ├── Login.php      # Authentication
│   │   └── ...
│   ├── models/            # Models
│   │   ├── Report_model.php  # Report data queries (NEW)
│   │   ├── Cost_model.php    # Cost data operations
│   │   ├── Calc_model.php    # Calculation logic
│   │   └── ...
│   ├── views/             # Views
│   │   ├── report/        # Report views
│   │   │   ├── report_view.php  # Margin analysis UI
│   │   │   └── report_js.php    # Chart.js integration
│   │   ├── main/          # Dashboard views
│   │   ├── cost/          # Cost management views
│   │   └── ...
│   ├── config/            # Configuration files
│   └── helpers/           # Helper functions
├── assets/
│   ├── css/
│   │   └── argon-custom.css  # macOS Glass UI theme
│   ├── js/
│   └── images/
├── uploads/               # Uploaded Excel files
├── system/                # CodeIgniter core
└── index.php              # Entry point
```

---

## 🆕 Recent Updates

### Version 2.0 (December 2025)

#### ✨ New Features
- **Margin Analysis Report** - Complete reporting system with interactive charts
  - KPI dashboard with month-over-month comparison
  - Margin distribution visualization
  - 6-month trend analysis
  - Top products and low margin alerts
  - Machine performance comparison

#### 🎨 UI/UX Improvements
- Implemented **macOS Glass UI Theme** across the application
- Enhanced responsive design for mobile devices
- Improved year/month selector visibility
- Added smooth animations and transitions

#### 🔧 Technical Improvements
- Created `Report_model.php` with optimized queries
- Integrated Chart.js 4.4.0 for interactive visualizations
- Fixed PHP 8.2 compatibility issues
- Improved code organization and documentation

#### 📝 Commit History
- `9e83d5f` - Add Margin Analysis Report feature
- `19a6c71` - Apply macOS Glass UI theme
- Previous commits available in Git history

---

## 👥 Contributing

This is a private project for SCMI. For internal development:

1. Create a new branch from `newtheme`
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. Make your changes and commit
   ```bash
   git add .
   git commit -m "Description of changes"
   ```

3. Push to the repository
   ```bash
   git push origin feature/your-feature-name
   ```

4. Create a Pull Request for review

---

## 📞 Support

For issues or questions, please contact the development team.

---

## 📄 License

Private - All rights reserved by SCMI

---

## 🙏 Acknowledgments

- **CodeIgniter** - PHP Framework
- **Chart.js** - Charting library
- **AG-Grid** - Data grid component
- **PhpSpreadsheet** - Excel processing

---

**Last Updated:** December 23, 2025
**Version:** 2.0
**Branch:** newtheme
