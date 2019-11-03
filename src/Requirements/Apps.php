<?php

namespace App\Requirements;

class Apps
{
    /**
     * @return array
     */
    public static function check()
    {
        $checks = [];

        // PHP CLI
        $php = trim(shell_exec('which php'));

        $checks[] = new Check([
            'name'  => 'PHP',
            'state' => $php ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // Composer
        $composer = trim(shell_exec('which composer'));

        $checks[] = new Check([
            'name'  => 'Composer',
            'state' => $composer ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // FFMPEG BIN
        $ffmpegBin = trim(shell_exec('which ffmpeg'));

        $checks[] = new Check([
            'name'  => 'FFMPEG',
            'state' => $ffmpegBin ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        // WKHTMLTOIMAGE BIN
        $wkhtmltopdfBin = trim(shell_exec('which wkhtmltoimage'));

        $checks[] = new Check([
            'name'  => 'wkhtmltoimage',
            'state' => $wkhtmltopdfBin ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        // HTML2TEXT BIN
        $html2textBin = trim(shell_exec('which html2text'));

        $checks[] = new Check([
            'name'  => 'html2text (mbayer)',
            'state' => $html2textBin ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        // ghostscript BIN
        $ghostscriptBin = trim(shell_exec('which gs'));

        $checks[] = new Check([
            'name'  => 'Ghostscript',
            'state' => $ghostscriptBin ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        // LibreOffice BIN
        $libreofficeBin = trim(shell_exec('which soffice'));

        $checks[] = new Check([
            'name'  => 'LibreOffice',
            'state' => $libreofficeBin ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        // image optimizer
        foreach (['zopflipng', 'pngcrush', 'jpegoptim', 'pngout', 'advpng', 'cjpeg', 'exiftool'] as $optimizerName) {
            $optimizerAvailable = trim(shell_exec("which {$optimizerName}"));

            $checks[] = new Check([
                'name'  => $optimizerName,
                'state' => $optimizerAvailable ? Check::STATE_OK : Check::STATE_WARNING,
            ]);
        }

        // timeout binary
        $timeoutBin = trim(shell_exec('which timeout'));

        $checks[] = new Check([
            'name'  => 'timeout - (GNU coreutils)',
            'state' => $timeoutBin ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        // pdftotext binary
        $pdftotextBin = trim(shell_exec('which pdftotext'));

        $checks[] = new Check([
            'name'  => 'pdftotext - (part of poppler-utils)',
            'state' => $pdftotextBin ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        $sqipAvailable = trim(shell_exec('which sqip'));

        $checks[] = new Check([
            'name'  => 'SQIP - SVG Placeholder',
            'state' => $sqipAvailable ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        $facedetectAvailable = trim(shell_exec('which facedetect'));

        $checks[] = new Check([
            'name'  => 'facedetect',
            'state' => $facedetectAvailable ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        $graphvizAvailable = trim(shell_exec('which dot'));

        $checks[] = new Check([
            'name'  => 'Graphviz',
            'state' => $graphvizAvailable ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        return $checks;
    }
}