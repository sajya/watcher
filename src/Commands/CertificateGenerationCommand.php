<?php

declare(strict_types=1);

namespace Sajya\Server\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CertificateGenerationCommand extends Command
{
    /**
     * Certificate lifetime in days
     */
    private const LIFETIME = 1095;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sajya:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SSL certificate generation';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Generate a new private (and public) key pair
        $privateKey = openssl_pkey_new(config('server.key'));

        // Generate a certificate signing request
        $csr = openssl_csr_new(config('server.dm'), $privateKey, config('server.csr'));

        // Generate a self-signed cert, valid for 365 days
        $x509 = openssl_csr_sign($csr, null, $privateKey, self::LIFETIME, config('server.csr'));

        // Save your private key, CSR and self-signed cert for later use
        openssl_csr_export($csr, $csrout);
        openssl_x509_export($x509, $certout);
        openssl_pkey_export($privateKey, $pkeyout);


        $storage = Storage::disk('local');

        $storage->put('sajya.csr', $csrout);
        $storage->put('sajya.key', $pkeyout);
        $storage->put('sajya.cer', $certout);
        $storage->put('sajya.pem', $certout . $pkeyout);

        $this->info('Success');
    }
}