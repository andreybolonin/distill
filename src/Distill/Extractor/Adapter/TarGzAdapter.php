<?php

namespace Distill\Extractor\Adapter;

use Distill\File;
use Distill\Format\TarGz;

class TarGzAdapter extends AbstractAdapter
{

    /**
     * Constructor.
     */
    public function __construct($methods = null)
    {
        if (null === $methods) {
            $methods = array(
                array('self', 'extractTarCommand'),
                array('self', 'extractArchiveTar')
            );
        }

        $this->methods = $methods;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(File $file)
    {
        return $file->getFormat() instanceof TarGz &&
            (class_exists('\Archive_Tar') || $this->existsCommand('tar'));
    }

    /**
     * Extracts the tar.gz file using the tar command.
     * @param File   $file Compressed file
     * @param string $path Destination path
     *
     * @return bool Returns TRUE when successful, FALSE otherwise
     */
    protected function extractTarCommand(File $file, $path)
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            return false;
        }

        @mkdir($path);
        $command = sprintf("tar -zxvf %s -C %s", escapeshellarg($file->getPath()), escapeshellarg($path));

        return $this->executeCommand($command);
    }

    /**
     * Extracts the tar.gz file using the Archive_Tar extension.
     * @param File   $file Compressed file
     * @param string $path Destination path
     *
     * @return bool Returns TRUE when successful, FALSE otherwise
     */
    protected function extractArchiveTar(File $file, $path)
    {

        if (!class_exists('\Archive_Tar')) {
            return false;
        }

        $tar = new \Archive_Tar($file->getPath(), true);

        return $tar->extract($path);
    }

}