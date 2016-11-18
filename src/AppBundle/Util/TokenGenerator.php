<?php

namespace AppBundle\Util;

use Psr\Log\LoggerInterface;

class TokenGenerator
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * TokenGenerator constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function generateToken()
    {
        return rtrim(
            strtr(base64_encode($this->getRandomNumber()), '+/', '-_'), '='
        );
    }

    /**
     * @return string
     */
    private function getRandomNumber()
    {
        $nbBytes = 32;
        $bytes = openssl_random_pseudo_bytes($nbBytes, $strong);

        if (false !== $bytes && true === $strong) {
            return $bytes;
        }

        if (null !== $this->logger) {
            $this->logger->info('OpenSSL did not produce a secure random number.');
        }

        return hash('sha256', uniqid(mt_rand(), true), true);
    }
}
