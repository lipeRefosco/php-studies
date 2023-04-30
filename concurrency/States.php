<?php

namespace Lipe\php;

enum States {
    case Pending;
    case Running;
    case Fulfilled;
    case Rejected;
}