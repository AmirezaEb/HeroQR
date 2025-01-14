# HeroQR Library

HeroQR is an advanced and modular PHP library designed to simplify the creation, customization, and management of QR codes. Whether you need a basic QR code or a highly customized one with embedded logos, colors, markers, and cursors, HeroQR has you covered. You can fully customize the appearance of your QR code by adjusting the markers (for corner customization) and cursors (for design enhancements). This level of customization allows you to tailor your QR codes to fit your needs precisely.

## Table of Contents

- [HeroQR Library](#heroqr-library)
  - [Features](#features)
  - [Installation](#installation)
  - [Getting Started](#getting-started)
    - [1. Basic Usage](#1-basic-usage)
    - [2. Advanced Customization](#2-advanced-customization)
    - [3. Customizing Markers and Cursors (PNG Only)](#3customizing-markers-and-cursors-png-only)
    - [4. Advanced Output Options](#4-advanced-output-options)
  - [Project Structure](#project-structure)
  - [Contributing](#contributing)
  - [License](#license)
  - [Contact](#contact)


## Features

- **Unmatched Customization :**
  - Adjust logo size and add logos to the QR code.
  - Add labels with customizable color, size, text alignment, and margin.
  - Support for various encoding formats such as BASE64, UTF-8, UTF-16, and other standard formats.
  - Change the color of the QR code along with the background color, with the ability to adjust the transparency of the colors.
  - Automatically adjust the QR code layout and margin.

- **Customizable Markers and Cursors :** HeroQR allows you to customize the QR code markers and cursors, giving you enhanced control over the design. This feature is available exclusively for PNG output. To customize the markers and cursors, use generate('png-M1-C1'), where:

    - `M1` specifies the marker type.
    - `C1` specifies the cursor type.
    - Available marker and cursor types:
      - **Markers :** `M1` `M2` `M3`
      - **Cursors :** `C1` `C2` `C3`

    - **Note** : This feature is currently limited to PNG format due to its advanced rendering capabilities, but support for other formats will be available in future releases.


- **Multi-Format Data Encoding :** Easily encode various data types, including URLs, text, emails, business cards, and payment information, providing versatility for your QR code needs.

- **Data Validation :** The library supports validation for various data types, including URL, text, email, phone number, IP, and Wi-Fi, ensuring the accuracy of input data.

- **Flexible Export Options :** Save QR codes in multiple formats, including PDF, SVG, PNG, Binary, GIF, EPS, and WebP. If you don't require custom markers or cursors, you can choose from these formats for your output.
- 
- **Framework Ready :** Seamlessly integrates with frameworks like Laravel, making it a perfect fit for modern applications.

## Installation

Use [Composer](https://getcomposer.org/) to install the library. Also make sure you have enabled and configured the
[GD extension](https://www.php.net/manual/en/book.image.php) if you want to generate images.

```bash
composer require amirezaeb/heroqr
```

## Getting Started

### 1. Basic Usage

- Generate a simple QR code in just a few lines of code:

#### Example :

```php
require 'vendor/autoload.php';

use HeroQR\Core\QRCodeGenerator;

$qrCodeManager = new QRCodeGenerator();

$qrCode = $qrCodeManager
    # Set the data to be encoded in the QR code
    ->setData('https://test.org') 
    # Generate the QR code in PNG format (default)
    ->generate() ;

# Save the generated QR code to a file named 'qrcode.png'
$qrCode->saveTo('qrcode'); 
```

### 2. Advanced Customization

**Fully customize the appearance and functionality of your QR code while ensuring data validation:**

- **Customization Options :** You can modify various parameters such as size, color, logo, and other visual aspects.
- **Automatic Data Validation :** By using `DataType` (optional), the library automatically validates the type of data being encoded (Url, Email, Phone, Location, Wifi , Text).

#### Example :

```php
use HeroQR\DataTypes\DataType;

$qrCode = $qrCodeManager
    # Set the data to be encoded and validation Email
    ->setData('aabrahimi1718@gmail.com', DataType::Email)  
    # Set the background color
    ->setBackgroundColor('#000000')
    # Set the QR code's color
    ->setColor('#b434eb')
    # Set the size
    ->setSize(350)
    # Set the logo to be embedded at the center
    # Set the logo size default value is 40
    ->setLogo('../assets/HeroExpert.png', 30 )
    # Set the margin around
    ->setMargin(10)
    # Set the character encoding
    ->setEncoding('UTF-8')
    # Set the label 
    ->setLabel(
        # Label Text
        label: 'My Email',
        # Label align
        textAlign: 'center',
        # Label text color
        textColor: '#a503fc',
        # Label size default value is 20
        fontSize: 15,
        # Label margin default value is (0, 10, 10, 10)
        margin: [15, 15, 15, 15] 
    )
    # Generate the QR code in WebP format
    ->generate('webp');

# Save the generated QR code to a file
$qrCode->saveTo('custom-qrcode'); 
```

With these options, you can create visually appealing QR codes that align with your design needs.

### 3. Customizing Markers and Cursors (PNG Only)

- HeroQR allows you to customize the markers and cursors of the QR code. This feature is exclusive to PNG output, and you must specify the output format using generate('png-M1-C1'), where M1 is the marker type and C1 is the cursor type. Currently, only the following options are available:
  - **Markers :** `M1`, `M2`, `M3`
  - **Cursors :** `C1`, `C2`, `C3`

#### Example :

```php
use HeroQR\Core\QRCodeGenerator;

$qrCode = $qrCodeManager
    ->setData('https://example.com')
    ->setSize(800)
    ->setBackgroundColor('#ffffffFF')
    ->setColor('#000000')
    # Customize the markers and Cursors (M1 for marker, C1 for Cursor)
    ->generate('png-M1-C1');

# Save the generated QR code with custom markers and Cursors
$qrCode->saveTo('custom-markers-qr');
```
- **Example Output :** The following images showcase QR codes generated with different marker and cursor configurations in this order : ['M1-C1', 'M2-C2', 'M3-C3'].

    <img src="https://raw.githubusercontent.com/AmirezaEb/AmirezaEb/main/assets/img/QrCode/Qr-M1-C1.png" width="100" height="100" />
   <img src="https://raw.githubusercontent.com/AmirezaEb/AmirezaEb/main/assets/img/QrCode/Qr-M2-C2.png" width="100" height="100" />
   <img src="https://raw.githubusercontent.com/AmirezaEb/AmirezaEb/main/assets/img/QrCode/Qr-M3-C3.png" width="100" height="100" />

### 4. Advanced Output Options

HeroQR provides advanced output capabilities, offering flexibility and compatibility for various use cases, from web embedding to raw data manipulation:
- **Matrix Output :** Represent the QR code as a matrix (2D array) of bits, where each block of the matrix corresponds to a specific piece of the encoded data. You can retrieve the matrix in two forms:
  - As a `Matrix` object.
  - As a 2D array, which makes it easier to manipulate or display directly in some applications.
  
- **Base64 Encoding :** Generate the QR code as a Base64-encoded string, which is ideal for embedding directly in HTML, emails, or other media.

- **Data URI :** Get the QR code as a Data URI, which is a compact string representation of the image that can be embedded directly into HTML.

- **Saving to Different Formats :** You can save the QR code in a variety of formats such as PNG, SVG, GIF, WebP, EPS, PDF, Binary, and more. The format is automatically determined based on the desired output type.

#### Example

```php

# Get the QR code as a string representation
$string = $qrCode->getString();

# Get the QR code as a matrix object
$matrix = $qrCode->getMatrix();

# Get the matrix as a 2D array
$matrixArray = $qrCode->getMatrixAsArray();

# Get the QR code as Base64 encoding for embedding in HTML
$dataUri = $qrCode->getDataUri();

# Save the QR code to a file in the desired format (WebP, GIF, Binary, Esp, PNG, SVG, PDF)
$qrCode->saveTo('qr_code_output');

```

## Project Structure  

- The modular structure of HeroQR is designed to enhance efficiency and scalability, making it easier to use, maintain, and expand:

  - **Contracts :** Defines interfaces for the core components, ensuring consistency and reusability across the system.
  - **Core :** Houses the primary logic for generating and managing QR codes, acting as the foundation of the library.
  - **DataTypes :** Provides definitions and automatic validation for various data types (wifi, Location, URL, Email, Phone, Text). This eliminates the need for users to manually validate their input.
  - **Managers :** Oversees the customization and processing of QR codes, enabling users to have full control over the appearance and functionality of their QR codes.
  - **Customs :** The Customs module allows advanced QR code customization, including cursors, markers, line colors, and other visual elements, perfect for creating unique and tailored designs.

## Contributing

We welcome your contributions! Here’s how you can get involved:

1. Fork the repository.
2. Create a feature branch: `git checkout -b feature-name`.
3. Commit your changes with clear messages: `git commit -m 'Add feature-name'`.
4. Push your branch: `git push origin feature-name`.
5. Open a pull request for review.

## License

HeroQR is released under the [MIT License](LICENSE), giving you the freedom to use, modify, and distribute it.

## Contact

For inquiries or feedback, feel free to reach out via email or GitHub issues :
- **Author :** Amirreza Ebrahimi
- **Email :** aabrahimi1718@gmail.com
- **GitHub Issues :** [GitHub Repository](https://github.com/AmirezaEb/HeroQR/issues)

---

Transform your projects with HeroQR today! 🚀
