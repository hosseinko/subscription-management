<?php


namespace App\Libs;

/**
 * Class Token
 * @package App\Libs
 */
class Token extends BaseLib
{

    /**
     * @param int $length
     * @return string
     */
    public function generateToken($length = 32): string
    {
        $allowedCharacters = "abcdefghijklmnopqrstuvwxyABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        $password = "";
        for ($i = 0; $i < $length; $i++) {
            $password .= substr($allowedCharacters, mt_rand(0, strlen($allowedCharacters) - 1), 1);
        }

        return $password;
    }

}
