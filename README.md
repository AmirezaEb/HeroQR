# HeroQR - A Powerful PHP QR Code Library

<p align="center">
    <a href="https://php.net"><img alt="PHP 8.2" src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php"></a>
</p>


> [!TIP]
> Packages in the same major version series are compatible.
> See the migration guide for breaking changes between versions.

## Table of Contents

- [HeroQR Library](#heroqr---a-powerful-php-qr-code-library)
    - [Features](#features)
    - [Getting Started](#getting-started)
        - [1. Installation](#1-installation)
        - [2. Basic Usage](#2-basic-usage)
        - [3. Advanced Customization](#3-advanced-customization)
        - [4. Customizing Shapes Markers and Cursors (PNG Only)](#4-customizing-shapes-markers-and-cursors-png-only)
        - [5. Advanced Output Options](#5-advanced-output-options)
    - [Contributing](#contributing)
    - [License](#license)
    - [Contact](#contact)

## Features

- **Advanced Customization Options:**
    - Adjust logo size and embed logos into QR codes.
    - Automatically adjust QR code layout and margins to ensure proper scaling.
    - Change QR code and background colors (RGB or RGBA), including transparency options.
    - Add customizable labels with options for color (RGB or RGBA), size, alignment, and margin.
    - Support for various encoding formats such as BASE64, UTF-8, UTF-16, and others.

- **Shape, Marker, and Cursor Customization:**
  HeroQR offers enhanced design control by allowing customization of the geometric shape of QR code points, as well as markers and cursors.These features are support:
    - `S` for Shape type.
    - `M` for Marker type.
    - `C` for Cursor type.
    - Available marker and cursor types:
        - **Shapes:** `S1` - `S2` - `S3` - `S4`
        - **Markers:** `M1` - `M2` - `M3` - `M4` - `M5` - `M6` - `M7`
        - **Cursors:** `C1` - `C2` - `C3` - `C4` - `C5` - `C6` - `C7`
    - **Note:** This feature is available only for PNG format at the moment, but additional formats will be supported in future updates.

- **Multi-Format Data Encoding:**  
  Support for encoding a wide range of data types — including URLs, plain text, emails, business cards, and payment information — so you can generate the right QR code for any use case.

- **Built-In Data Validation:** 
  To prevent errors and ensure reliability, the library automatically validates input for common types such as URLs, emails, phone numbers, IP addresses, and Wi-Fi credentials before encoding.

- **Flexible Export Options:**
  QR codes can be exported in various formats, including PDF, SVG, PNG, Binary, GIF, EPS, and WebP. If custom shapes, markers, or cursors are not required, you can choose from these formats for the output.

- **Framework Ready:**
  Seamlessly integrates with modern frameworks like Laravel, making it a perfect fit for contemporary web applications.

## Getting Started

### 1. Installation

Use [Composer](https://getcomposer.org/) to install the library. Also make sure you have enabled and configured the
[GD extension](https://www.php.net/manual/en/book.image.php) if you want to generate images.

```bash
composer require amirezaeb/heroqr:^1.0
```

### 2. Basic Usage

- **Generate a simple QR code in just a few lines of code:**

#### Example :

```php
require 'vendor/autoload.php';
use HeroQR\Core\QRCodeGenerator;

# Create a QRCodeGenerator instance
$qrCodeManager = new QRCodeGenerator();

$qrCode = $qrCodeManager
    # Set the data to be encoded in the QR code
    ->setData('https://test.org') 
    # Generate the QR code in PNG format (default)
    ->generate();

# Save the generated QR code to a file named 'qrcode.png'
$qrCode->saveTo('qrcode'); 
```

### 3. Advanced Customization

- **Fully customize the appearance and functionality of your QR code with automatic data validation:**

    - **Customization Options:** Modify various parameters such as size, color, logo, and more.
    - **Automatic Data Validation:** By using `DataType` (optional), the library automatically validates the type of data being encoded (e.g., URL, Email, Phone, Location, Wifi, Text).

#### Example :

```php
require 'vendor/autoload.php';

use HeroQR\Core\QRCodeGenerator;
use HeroQR\DataTypes\DataType;

# Create a QRCodeGenerator instance
$qrCodeManager = new QRCodeGenerator();

$qrCode = $qrCodeManager
    # Set data to be encoded and validate it as an Email
    ->setData('aabrahimi1718@gmail.com', DataType::Email)  
    # Set background and QR code colors
    ->setBackgroundColor('#ffffffFF')
    # Set the QR code's color
    ->setColor('#fc031c')
    # Set the size of the QR code (default size is 800)
    ->setSize(900)
    # Set the logo to be embedded at the center (default size is 80)
    ->setLogo('../assets/HeroExpert.png', 100 )
    # Set the margin around the QR code (default size is 10)
    ->setMargin(0)
    # Set character encoding for the QR code (default encoding is UTF-8)
    ->setEncoding('CP866')
    # Set the error correction level for the QR code (default is "High")
    ->setErrorCorrectionLevel('Medium')
    # Set the block size mode to "None" (default is "Margin")
    ->setBlockSizeMode('None')
    # Customize the label with text, alignment, color, and font size
    ->setLabel(
        # Label Text
        label: 'To Contact Me, Just Scan This QRCode',
        # Label align (default: center)
        textAlign: 'right',
        # Label text color (default: #000000)
        textColor: '#fc031c',
        # Label size (default: 50)
        fontSize: 35,
        # Label margin (default: 0, 10, 10, 10)
        margin: [15, 15, 15, 15] 
    )
    # Generate the QR code in WebP format
    ->generate('webp');

# Save the generated QR code to a file
$qrCode->saveTo('custom-qrcode');
``` 

With these options, you can create visually appealing QR codes that align with your design needs.

### 4. Customizing Shapes, Markers, and Cursors (PNG Only)

HeroQR allows you to fully customize the markers, cursors, and shapes of your QR codes. This feature is exclusive to PNG output. To use this feature, specify the output format with `generate('png', [ ... ])` and include the desired parameters for shape, marker, and cursor types.

#### Available Options:

- **Shapes**: `S1` - `S2` - `S3` - `S4`
- **Markers**: `M1` - `M2` - `M3` - `M4` - `M5` - `M6` - `M7`
- **Cursors**: `C1` - `C2` - `C3` - `C4` - `C5` - `C6` - `C7`

These options allow you to modify the appearance of the QR code, making it more personalized or stylish. Shapes modify the corners, markers change the patterns on the dots, and cursors adjust the positioning pointers.

#### Example :

```php
require 'vendor/autoload.php';

use HeroQR\Core\QRCodeGenerator;

# Create a QRCodeGenerator instance
$qrCodeManager = new QRCodeGenerator();

$qrCode = $qrCodeManager
    ->setData('https://example.com')
    ->setSize(800)
    ->setBackgroundColor('#ffffffFF')
    ->setColor('#000000')
    # Customize the Shape (Circle), Marker (Circle), and Cursor (Circle)
    ->generate('png',[
            'Shape' => 'S2',
            'Marker' => 'M2',
            'Cursor' => 'C2'
        ]);

# Save the generated QR code with customizations
$qrCode->saveTo('custom-qr');
```

### 5. Advanced Output Options

HeroQR provides advanced output capabilities, offering flexibility and compatibility for various use cases, from web embedding to raw data manipulation:

- **String Representation :** Retrieve the QR code as a raw string, which can be useful for direct processing or custom transformations.

- **Matrix Output :** Represent the QR code as a matrix (2D array) of bits, where each block of the matrix corresponds to a specific piece of the encoded data. You can retrieve the matrix in two forms:
  - As a `Matrix` object.
  - As a 2D array, which makes it easier to manipulate or display directly in some applications.
  
- **Base64 Encoding :** Generate the QR code as a Base64-encoded string, which is ideal for embedding directly in HTML, emails, or other media.

- **Saving to Different Formats :** You can save the QR code in a variety of formats such as PNG, SVG, GIF, WebP, EPS, PDF, Binary, and more. The format is automatically determined based on the desired output type.

#### Example

```php
require 'vendor/autoload.php';

use HeroQR\Core\QRCodeGenerator;

# Create a QRCodeGenerator instance
$qrCodeManager = new QRCodeGenerator();

$qrCode = $qrCodeManager
    # Set the data to be encoded in the QR code
    ->setData('https://test.org') 
    # Generate the QR code in PNG format (default)
    ->generate();

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

## Contributing

We welcome contributions and appreciate your interest in improving the project! Here's how you can contribute:

1. **Fork the repository:** Create your own copy of the repository by forking it.

2. **Clone your fork:** Clone the repository to your local machine:
  ```bash
  git clone https://github.com/AmirezaEb/HeroQR.git
  ```
3. **Create a feature branch:** Create a new branch for your feature or bug fix.

4. **Make changes:** Work on your feature or fix the issue.

5. **Write tests:** Ensure your changes are covered by tests. If you're fixing a bug, add a test to verify the fix.

6. **Commit your changes:** Commit your changes with clear, descriptive messages following a conventional format.

```bash
git commit -m '<type>[optional scope]: <description>'
```

7. **Push your branch:** Push your changes to your forked repository branch.
   
```bash
git push origin feature-name
```

8. **Open a Pull Request:** Once your branch is pushed, open a Pull Request on GitHub for review. Be sure to:
    - Provide a clear description of what your changes do.
    - Include any relevant issue numbers (e.g., Fixes #123).

9. **Participate in the review process:** After submitting the pull request, review any feedback and make necessary changes.

We’ll review and merge your changes as soon as possible. Thank you for contributing!

## License

HeroQR is released under the [MIT License](LICENSE), giving you the freedom to use, modify, and distribute it.

## Contact

For inquiries or feedback, feel free to reach out via email, GitHub issues, or LinkedIn:

- **Author :** Amirreza Ebrahimi
- **Email :** aabrahimi1718@gmail.com
- **GitHub Issues :** [GitHub Repository](https://github.com/AmirezaEb/HeroQR/issues)
- **LinkedIn :** [My LinkedIn](https://www.linkedin.com/in/amireza-eb)
- **Telegram :** [My Telegram](https://t.me/a_m_b_r)
---

Transform your projects with HeroQR today! 🚀
