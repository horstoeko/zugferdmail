<?php

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidEncodingException;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Exception\ValidationException;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use horstoeko\zugferdmail\config\ZugferdMailConfig;

class ExampleHelper
{
    /**
     * Create a mail account from a .env file
     *
     * @param ZugferdMailConfig $config
     * @return ZugferdMailAccount
     * @throws RuntimeException
     * @throws InvalidEncodingException
     * @throws InvalidFileException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public static function createMailAccountFromDotEnv(ZugferdMailConfig $config): ZugferdMailAccount
    {
        $dotEnv = Dotenv::createImmutable(dirname(__FILE__));
        $dotEnv->safeLoad();
        $dotEnv->required('MAIL_PORT')->isInteger();
        $dotEnv->required('MAIL_VALIDATECERT')->isBoolean();

        $mailAccount = $config->addAccount(
            'myaccount-1',
            $_ENV['MAIL_HOST'],
            filter_var($_ENV['MAIL_PORT'], FILTER_VALIDATE_INT),
            $_ENV['MAIL_PROTOCOL'],
            $_ENV['MAIL_ENCRYPTION'],
            filter_var($_ENV['MAIL_VALIDATECERT'], FILTER_VALIDATE_BOOLEAN),
            $_ENV['MAIL_USER'],
            $_ENV['MAIL_PASSWORD']
        );

        $mailAccount->setFoldersToWatch(explode(",", $_ENV['MAIL_FOLDERS']));
        $mailAccount->setMimeTypesToWatch(explode(",", $_ENV['MAIL_MIMETYPES']));

        return $mailAccount;
    }
}
