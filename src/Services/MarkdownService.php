<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;

class MarkdownService
{
    private $converter;
    private $docsPath;

    public function __construct(?string $docsPath = null)
    {
        $config = [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ];
        
        $environment = new Environment($config);
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new FrontMatterExtension());
        
        $this->converter = new CommonMarkConverter($config, $environment);
        $this->docsPath = $docsPath ?? __DIR__ . '/../../docs';
        
        // Ensure docs directory exists
        if (!is_dir($this->docsPath)) {
            mkdir($this->docsPath, 0755, true);
        }
    }

    public function convertToHtml(string $markdown): string
    {
        return $this->converter->convert($markdown)->getContent();
    }

    public function getDocumentList(): array
    {
        if (!is_dir($this->docsPath)) {
            error_log("Documents directory not found: " . $this->docsPath);
            return [];
        }

        $files = glob($this->docsPath . '/*.md');
        if ($files === false) {
            error_log("Failed to read documents directory: " . $this->docsPath);
            return [];
        }
        
        error_log("Found files: " . print_r($files, true));
        
        $documents = [];
        
        foreach ($files as $file) {
            if (!is_readable($file)) {
                error_log("File not readable: " . $file);
                continue;
            }
            
            $documents[] = [
                'filename' => basename($file),
                'title' => $this->extractTitle($file),
                'modified' => filemtime($file)
            ];
        }
        
        // Sort by modified date, newest first
        usort($documents, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });
        
        return $documents;
    }

    public function getDocument(string $filename): ?array
    {
        if (!str_ends_with($filename, '.md')) {
            $filename .= '.md';
        }

        $filepath = $this->docsPath . '/' . $filename;
        
        if (!file_exists($filepath) || !is_readable($filepath)) {
            return null;
        }

        $content = file_get_contents($filepath);
        $html = $this->converter->convert($content);

        return [
            'filename' => basename($filepath),
            'title' => $this->extractTitle($filepath),
            'content' => $html,
            'modified' => filemtime($filepath)
        ];
    }

    public function saveDocument(string $filename, string $content): bool
    {
        if (!str_ends_with($filename, '.md')) {
            $filename .= '.md';
        }

        // Sanitize filename
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
        
        $filepath = $this->docsPath . '/' . $filename;
        
        // Ensure directory exists
        $directory = dirname($filepath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $result = file_put_contents($filepath, $content);
        
        if ($result === false) {
            throw new \RuntimeException("Failed to save document: " . error_get_last()['message'] ?? 'Unknown error');
        }
        
        return true;
    }

    private function extractTitle(string $filepath): string
    {
        $content = file_get_contents($filepath);
        $lines = explode("\n", $content);
        
        // Look for # Title or --- frontmatter ---
        foreach ($lines as $line) {
            if (preg_match('/^#\s+(.+)$/', $line, $matches)) {
                return trim($matches[1]);
            }
        }
        
        // If no title found, use filename
        return pathinfo($filepath, PATHINFO_FILENAME);
    }
}
