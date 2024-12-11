<?php

define('BASEPATH', realpath(__DIR__ . '/../') . '/');
require BASEPATH . 'vendor/autoload.php';


use HeroQR\Core\QRCodeGenerator;
use HeroQR\DataTypes\DataType;

$qrCodeManager = new QRCodeGenerator();

$qrCode = $qrCodeManager
    ->setData('aabrahimi1718@gmail.com', DataType::Email)  
    ->setBackgroundColor('#000000')
    ->setColor('#b434eb')
    ->setSize(350)
    ->setLogo('../assets/HeroExpert.png', 30)
    ->setMargin(10)
    ->setEncoding('UTF-8')
    ->setLabel(
        label: 'My Email',
        textAlign: 'center',
        textColor: '#a503fc',
        fontSize: 15,
        margin: [15, 15, 15, 15]
    )->generate('png');

$qrCode->saveTo('custom_qrcode');
    /*
try {
    $builder = new QRCodeGenerator();
    $builder->setData('56.0,58.38',DataType::Location)
        ->setBackgroundColor('#ffffff')
        ->setColor('#000000')
        ->setSize(350)
        ->setLogo(BASEPATH . 'assets/HeroExpert.png', 30)
        ->setMargin(10)
        ->setEncoding('UTF-8')
        ->setLabel(
            label: 'Wifi',
            textAlign: 'center',
            textColor: '#499999',
            fontSize: 15,
            margin: [15, 15, 15, 15]
        )
        ->generate('png')
        ->saveTo('./1s'); #Save To...
    // ->getDataUri() #Base64 Text
    // ->getMatrix() #getMatrix
    // ->getMatrixAsArray() #getMatrixToArray
    // ->getString() #getString
} catch (Exception $e) {
    echo $e->getMessage();
} ?>

<a href="<?= $builder ?>"><img src="<?= $builder ?>" alt="QR Code"></a>


*/

