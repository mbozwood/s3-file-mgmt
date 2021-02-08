<?php

namespace App\Models;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 * @package App\Models
 * @property int id
 * @property int user_id
 * @property string s3_filename
 * @property string original_filename
 * @property string endpoint_url
 * @property string bucketname
 * @property string content_type
 * @property int download_count
 */
class File extends Model
{
    public $table = 'files';
    public $fillable = [
        'user_id', 's3_filename', 'original_filename', 'endpoint_url', 'bucketname', 'content_type', 'download_count'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function downloadFromS3()
    {
        $s3Client = $this->s3Client();

        return $s3Client->getObject([
            'Bucket' => env('AWS_BUCKET', ''),
            'Key' => $this->s3_filename
        ]);
    }

    public function deleteFromS3()
    {
        $msg = null;
        try {
            $s3Client = $this->s3Client();
            $s3Client->deleteObject([
                'Bucket' => env('AWS_BUCKET', ''),
                'Key' => $this->s3_filename
            ]);
        } catch (S3Exception $e) {
            $msg = $e->getMessage();
        }
        return $msg;
    }

    public function uploadToS3($path)
    {
        $msg = null;
        try {
            $s3Client = $this->s3Client();

            $obj = $s3Client->putObject([
                'Bucket' => env('AWS_BUCKET', ''),
                'Key' => $this->s3_filename,
                'SourceFile' => $path
            ]);

            $this->update([
                'endpoint_url' => $obj['ObjectURL']
            ]);
        } catch (S3Exception $e) {
            $msg = $e->getMessage();
        }
        return $msg;
    }

    protected function s3Client(): S3Client
    {
        return new S3Client([
            'region' => env('AWS_DEFAULT_REGION', ''),
            'version' => 'latest'
        ]);
    }
}
