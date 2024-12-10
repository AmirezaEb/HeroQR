<?php

namespace HeroQR\DataTypes;

enum DataType: string
{
    case Text = Text::class;
    case Url = Url::class;
    case Email = Email::class;
    case Phone = Phone::class;
    case Wifi = Wifi::class;
    case Location = Location::class;
}
