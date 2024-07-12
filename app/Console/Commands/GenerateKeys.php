<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use phpseclib\Crypt\RSA;



class GenerateKeys extends Command
{
    protected $signature = 'keys:generate';

    protected $description = 'Generate RSA key pair and save as .pem files';

    public function handle()
    {
        $rsa = new RSA();
        $keyPair = $rsa->createKey(2048);

        // Lưu khóa công khai vào file public.pem
        file_put_contents(storage_path('app/public.pem'), $keyPair['publickey']);

        // Lưu khóa bí mật vào file private.pem
        file_put_contents(storage_path('app/private.pem'), $keyPair['privatekey']);

        $this->info('RSA keys generated and saved successfully.');
    }
}   
