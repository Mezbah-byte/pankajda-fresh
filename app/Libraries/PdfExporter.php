<?php

namespace App\Libraries;

/**
 * PdfExporter - thin wrapper around dompdf/dompdf.
 *
 * SETUP REQUIRED:
 *   composer require dompdf/dompdf
 *
 * Usage:
 *   $pdf = new PdfExporter();
 *   $pdf->loadHtml($html)->setPaper('A4')->stream('invoice.pdf');   // send to browser
 *   $pdf->loadHtml($html)->setPaper('A4')->save(WRITEPATH . 'tmp/invoice.pdf');
 *
 * The class defers instantiation of Dompdf so that pages that never
 * produce PDFs don't pay the Dompdf bootstrap cost.
 */
class PdfExporter
{
    private ?object $dompdf = null;

    // ------------------------------------------------------------------
    // Builder-style API
    // ------------------------------------------------------------------

    /**
     * Load raw HTML into the renderer.
     */
    public function loadHtml(string $html): static
    {
        $this->boot();
        $this->dompdf->loadHtml($html, 'UTF-8');
        return $this;
    }

    /**
     * Load a CI4 view and pass data to it, then hand it to dompdf.
     *
     * @param string $view  e.g. 'admin/sales/invoice_pdf'
     * @param array  $data  Same array you'd pass to view()
     */
    public function loadView(string $view, array $data = []): static
    {
        return $this->loadHtml(view($view, $data));
    }

    /**
     * @param string       $size        'A4', 'letter', …
     * @param string|array $orientation 'portrait' | 'landscape'  or [w, h] in pts
     */
    public function setPaper(string $size = 'A4', string $orientation = 'portrait'): static
    {
        $this->boot();
        $this->dompdf->setPaper($size, $orientation);
        return $this;
    }

    // ------------------------------------------------------------------
    // Output
    // ------------------------------------------------------------------

    /**
     * Render and stream the PDF directly to the browser.
     *
     * @param string $filename  Suggested download filename.
     * @param bool   $inline    true = display in browser, false = force download
     */
    public function stream(string $filename = 'document.pdf', bool $inline = true): void
    {
        $this->render();
        $disposition = $inline ? 'inline' : 'attachment';
        $this->dompdf->stream($filename, ['Attachment' => ! $inline]);
    }

    /**
     * Render and save to an absolute file path.
     * Returns number of bytes written.
     */
    public function save(string $absolutePath): int
    {
        $this->render();
        $output = $this->dompdf->output();
        $dir = dirname($absolutePath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return (int) file_put_contents($absolutePath, $output);
    }

    /**
     * Render and return the raw PDF bytes.
     */
    public function output(): string
    {
        $this->render();
        return $this->dompdf->output();
    }

    // ------------------------------------------------------------------
    // Convenience static factories
    // ------------------------------------------------------------------

    /**
     * Render a view as a PDF and stream it to the browser.
     * One-liner for controllers.
     */
    public static function streamView(string $view, array $data = [], string $filename = 'document.pdf'): void
    {
        (new static())->loadView($view, $data)->setPaper('A4')->stream($filename);
    }

    /**
     * Render a view as a PDF and return the bytes.
     */
    public static function outputView(string $view, array $data = []): string
    {
        return (new static())->loadView($view, $data)->setPaper('A4')->output();
    }

    // ------------------------------------------------------------------
    // Internal
    // ------------------------------------------------------------------

    private function boot(): void
    {
        if ($this->dompdf !== null) {
            return;
        }

        if (! class_exists(\Dompdf\Dompdf::class)) {
            throw new \RuntimeException(
                'dompdf/dompdf is not installed. Run: composer require dompdf/dompdf'
            );
        }

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);  // keep false in production

        $this->dompdf = new \Dompdf\Dompdf($options);
    }

    private function render(): void
    {
        $this->boot();
        $this->dompdf->render();
    }
}
