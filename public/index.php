<?php

define('BASEPATH', realpath(__DIR__ . '/../') . '/');
require BASEPATH . 'vendor/autoload.php';

use HeroQR\Core\QRCodeGenerator;


$builder = new QRCodeGenerator();
$builder->setData('https://DigiKala.com')
    ->setColor('#000000')
    ->setSize(350)
    ->setLogo(BASEPATH . 'assets/HeroExpert.png', 30)
    ->setMargin(10)
    ->setEncoding('ISO-8859-5')
    ->setLabel(
        label: 'This Is Test',
        textAlign: 'center',
        textColor: '#499999',
        fontSize: 15,
        margin: [15, 15, 15, 15]
    )
    ->generate('webp')
    ->getDataUri()
?>

<a href="<?= $builder ?>"><img src="<?=$builder ?>" alt="QR Code"></a>
