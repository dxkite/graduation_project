<?php
namespace dxkite\openuser\exception;

use RuntimeException;

class UserException extends RuntimeException
{
    const ERR_NAME_FORMAT = 1;
    const ERR_EMAIL_FORMAT = 2;
    const ERR_NAME_EXISTS = 3;
    const ERR_EMAIL_EXISTS = 4;
    const ERR_ACCOUNT_NOT_FOUND = 5;
    const ERR_USER_FREEZED = 6;
    const ERR_CODE = 7;
    const ERR_ACCOUNT_IS_NOT_ACTIVE = 8;
    const ERR_MOBILE_FORMAT = 9;
    const ERR_MOBILE_EXISTS = 10;
    const ERR_PASSWORD_OR_ACCOUNT = 11;
    const ERR_ACCOUNT_EXISTS = 12;
    const ERR_MOBILE_NOT_EXISTS  = 13;
    const ERR_EMAIL_NOT_EXISTS  = 14;
    const ERR_CODE_FREQUENCY = 15;
    const ERR_MOBILE_OWN = 16;
    const ERR_EMAIL_OWN = 17;
    const ERR_MOBILE_NOT_CHECKED = 18;
    const ERR_EMAIL_NOT_CHECKED = 19;
    const ERR_CHECK_CODE = 20;
    const ERR_SAME_PASSWORD = 21;
}
