<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;

class UploadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private $region = 'sa-east-1';
    private $key = 'AKIAI7IQEMIDVWKVQRBQ';
    private $secret = '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';
    private $awsUrl = 'https://s3-sa-east-1.amazonaws.com/ups3/';
    private $projeto = 'cdn';
    private $s3;

    public function __construct()
    {
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region'  => $this->region,
            'credentials' => array(
                'key' => $this->key,
                'secret'  => $this->secret,
              )
        ]);
    }

    public function uploadS3($arquivo)
    {
        try{

            $nome = str_random(9).'.'.$arquivo->getClientOriginalExtension();

            $path = Storage::putFileAs(
                'uploads', $arquivo, $nome
            );
                
            $upload = $this->s3->putObject([
                'Bucket' => 'ups3',
                'Key'    => $this->projeto.'/'.$nome,
                'Body'   => Storage::get('uploads/'.$nome),
                'ACL'    => 'public-read',
            ]);
            
            Storage::delete('uploads/'.$nome);

            return $this->awsUrl.$this->projeto.'/'.$nome;

        }catch (Aws\S3\Exception\S3Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
    
    public function deleteS3($arquivo)
    {
        try{
            $delete = $this->s3->deleteMatchingObjects([
                'Bucket' => 'ups3',
                'Regex'    => $arquivo,
            ]);

            return $delete;
        }catch (Aws\S3\Exception\S3Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
