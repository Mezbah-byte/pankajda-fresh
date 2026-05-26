<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * FileUploader - centralised file upload handler for the ERP.
 *
 * Usage:
 *   $uploader = new FileUploader();
 *   $result   = $uploader->upload('photo', 'employees');
 *   // $result = ['filename' => '...', 'path' => '...', 'size' => 12345, 'mime' => '...']
 *
 * Files land in  writable/uploads/{subdir}/{filename}
 * The returned 'path' is the relative path from writable/uploads/ root.
 */
class FileUploader
{
    /** Base directory (absolute). */
    private string $baseDir;

    /** Max file size in bytes (default 5 MB). */
    private int $maxSize;

    /** Default allowed MIME types grouped by preset. */
    private array $presets = [
        'image'    => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'document' => ['application/pdf', 'application/msword',
                       'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'any'      => [], // empty = skip MIME check
    ];

    public function __construct(?string $baseDir = null, int $maxSize = 5 * 1024 * 1024)
    {
        $this->baseDir = $baseDir ?? WRITEPATH . 'uploads';
        $this->maxSize = $maxSize;
    }

    /**
     * Upload a file from a form field.
     *
     * @param string   $fieldName     HTML input[name]
     * @param string   $subdir        Sub-directory inside writable/uploads/ (e.g. 'employees')
     * @param string[] $allowedMimes  MIME types, or a preset key: 'image', 'document', 'any'
     *
     * @return array{filename:string,path:string,size:int,mime:string}
     * @throws \RuntimeException on validation / move failure
     */
    public function upload(string $fieldName, string $subdir, array|string $allowedMimes = 'image'): array
    {
        $request = service('request');
        /** @var UploadedFile|null $file */
        $file = $request->getFile($fieldName);

        if (! $file || ! $file->isValid()) {
            $error = $file?->getErrorString() ?? 'No file received.';
            throw new \RuntimeException('Upload error: ' . $error);
        }

        if ($file->hasMoved()) {
            throw new \RuntimeException('File already moved.');
        }

        // Size check
        if ($file->getSize() > $this->maxSize) {
            $mb = round($this->maxSize / 1024 / 1024, 1);
            throw new \RuntimeException("File exceeds maximum size of {$mb} MB.");
        }

        // MIME check
        $mimes = $this->resolveMimes($allowedMimes);
        if ($mimes && ! in_array($file->getMimeType(), $mimes, true)) {
            throw new \RuntimeException('File type not allowed: ' . $file->getMimeType());
        }

        // Ensure target directory exists
        $targetDir = rtrim($this->baseDir, '/') . '/' . trim($subdir, '/');
        if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true)) {
            throw new \RuntimeException("Cannot create upload directory: {$targetDir}");
        }

        // Generate unique filename keeping original extension
        $ext      = strtolower($file->getClientExtension() ?: 'bin');
        $filename = generate_un_id('UPL') . '.' . $ext;

        $file->move($targetDir, $filename);

        return [
            'filename' => $filename,
            'path'     => trim($subdir, '/') . '/' . $filename,
            'size'     => $file->getSize(),
            'mime'     => $file->getMimeType(),
        ];
    }

    /**
     * Delete a previously uploaded file by its relative path.
     *
     * @param string $relativePath  e.g. 'employees/UPL-xxx.jpg'
     */
    public function delete(string $relativePath): bool
    {
        $abs = rtrim($this->baseDir, '/') . '/' . ltrim($relativePath, '/');
        if (is_file($abs)) {
            return unlink($abs);
        }
        return false;
    }

    /**
     * Return the public URL for a stored file (uses site_url helper).
     * Assumes writable/uploads/ is NOT web-accessible by default;
     * route a controller method to serve protected files if needed.
     */
    public function url(string $relativePath): string
    {
        return site_url('uploads/' . ltrim($relativePath, '/'));
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function resolveMimes(array|string $allowedMimes): array
    {
        if (is_string($allowedMimes)) {
            return $this->presets[$allowedMimes] ?? [];
        }
        return $allowedMimes;
    }
}
