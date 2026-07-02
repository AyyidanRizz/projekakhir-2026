<?php

namespace App\Enums;

enum Courier: string
{
    case JNE = 'jne';
    case JNT = 'jnt';
    case GRAB_EXPRESS = 'grab_express';
    case GO_SEND = 'go_send';
}