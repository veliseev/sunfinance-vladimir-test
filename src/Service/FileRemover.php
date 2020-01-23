<?php
/**
 * @author        Vladimir Eliseev <vladimir.eliseev@sibers.com>
 * @copyright (c) 2020, Sibers
 */

namespace App\Service;


use App\Interfaces\RemoveAwareInterface;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class FileRemover implements RemoveAwareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var
     */
    private $uploadDir;

    private $fileSystem;

    /**
     * FileUploader constructor.
     *
     * @param LoggerInterface $logger
     * @param string          $uploadDir
     */
    public function __construct(LoggerInterface $logger, string $uploadDir, Filesystem $filesystem)
    {
        $this->logger    = $logger;
        $this->uploadDir = $uploadDir;
        $this->fileSystem = $filesystem;
    }

    public function remove(string $filename)
    {
        try{
            $this->fileSystem->remove(sprintf('%s/%s', $this->uploadDir, $filename));
        }catch (IOException $ex){
            $this->logger->error('Failed to remove file: '.$ex->getMessage());
        }
    }
}
