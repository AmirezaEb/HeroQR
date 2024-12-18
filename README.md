# HeroQR Library

HeroQR is an advanced and modular PHP library designed to simplify the creation, customization, and management of QR codes. Whether you need a basic QR code or a highly customized one with embedded logos and colors, HeroQR has you covered.

## Features

- **Unmatched Customization :**
  - Adjust logo size and add logos to the QR code.
  - Add labels with customizable color, size, text alignment, and margin.
  - Support for various encoding formats such as UTF-8 and other standard formats.
  - Change the color of the QR code along with the background color.
  - Automatically adjust the QR code layout and margin.

- **Multi-Format Data Encoding :** Effortlessly encode URLs, text, emails, business cards, and payment information.
- **Data Validation :** The library supports validation for various data types, including URL, text, email, phone number, IP, and Wi-Fi, ensuring the accuracy of input data.
- **Flexible Export Options :** Save QR codes in multiple formats, including PDF, SVG, PNG, Binary, GIF, EPS, and WebP, ensuring compatibility with different projects.
- **Framework Ready :** Seamlessly integrates with frameworks like Laravel, making it a perfect fit for modern applications.

## Installation

Use [Composer](https://getcomposer.org/) to install the library. Also make sure you have enabled and configured the
[GD extension](https://www.php.net/manual/en/book.image.php) if you want to generate images.

```bash
composer require amirezaeb/heroqr
```

## Getting Started

### 1. Basic Usage

Generate a simple QR code in just a few lines of code:

#### Example :

```php
require 'vendor/autoload.php';

use HeroQR\Core\QRCodeGenerator;

$qrCodeManager = new QRCodeGenerator();

$qrCode = $qrCodeManager
    # Set the data to be encoded in the QR code
    ->setData('https://test.org') 
    # Generate the QR code in PNG format
    ->generate('png') ;

# Save the generated QR code to a file named 'qrcode.png'
$qrCode->saveTo('qrcode'); 
```

### 2. Advanced Customization

**Fully customize the appearance and functionality of your QR code while ensuring data validation:**

- **Customization Options**: You can modify various parameters such as size, color, logo, and other visual aspects.
- **Automatic Data Validation**: By using `DataType` (optional), the library automatically validates the type of data being encoded (Url, Email, Phone, Location, Wifi , Text).

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

### 3. Advanced Output Options

HeroQR supports advanced output options for greater flexibility when generating QR codes:

- **Matrix Output** : Represent the QR code as a matrix (2D array) of bits, where each block of the matrix corresponds to a specific piece of the encoded data. You can retrieve the matrix in two forms:
  - As a `Matrix` object.
  - As a 2D array, which makes it easier to manipulate or display directly in some applications.
  
- **Base64 Encoding** : Generate the QR code as a Base64-encoded string, which is ideal for embedding directly in HTML, emails, or other media.

- **Data URI** : Get the QR code as a Data URI, which is a compact string representation of the image that can be embedded directly into HTML.

- **Saving to Different Formats** : You can save the QR code in a variety of formats such as PNG, SVG, GIF, WebP, EPS, PDF, Binary, and more. The format is automatically determined based on the desired output type.

#### Example :

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
The modular structure of HeroQR ensures ease of use and scalability:

- **Contracts :** Defines interfaces for the core components, ensuring consistency across the system.
- **Core :** Contains the main logic for generating and managing QR codes.
- **DataTypes :** Handles definitions for the various types of data (Location, Url, Email, Phone, Text) and performs automatic validation for each type, so users don’t need to validate data manually.
- **Managers :** Manages the customization and processing of QR codes, providing maximum flexibility for users.

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

Have questions or suggestions? Let’s connect:

- **Author :** Amirreza Ebrahimi
- **Email :** aabrahimi1718@gmail.com
- **GitHub :** [AmirrezaEb](https://github.com/AmirezaEb)

---

Transform your projects with HeroQR today! 🚀
