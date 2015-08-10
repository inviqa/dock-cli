<?php

namespace Dock\IO;

class PharFileExtractor
{
    /**
     * Extract a file from the PHAR archive to make it accessible from system commands.
     *
     * @param string $filePath
     * @return string
     */
    public function extract($filePath)
    {
        $dockerRouteFileContents = file_get_contents($filePath);

        $temporaryFile = tempnam(sys_get_temp_dir(), 'PharFile');
        file_put_contents($temporaryFile, $dockerRouteFileContents);

        return $temporaryFile;
    }
}
