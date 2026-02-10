<?php
class PdfToText {
    public static function extract($filename) {
        // Basic PDF Parser implementation for Core PHP
        // This is a naive implementation; for production, use smalot/pdfparser
        
        $content = file_get_contents($filename);
        if (!$content) return '';

        $text = '';
        
        // Extract text streams
        if (preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $content, $matches)) {
            foreach ($matches[1] as $stream) {
                // Determine filter
                // In a robust library, we'd checking the stream dict for /Filter /FlateDecode
                // Here we assume it might be compressed
                $uncompressed = @gzuncompress($stream);
                if ($uncompressed) {
                    $stream = $uncompressed;
                }
                
                // Extract text objects (BT...ET)
                if (preg_match_all('/\((.*?)\) Tj/', $stream, $textMatches)) {
                    foreach ($textMatches[1] as $t) {
                        $text .= $t . " ";
                    }
                }
                if (preg_match_all('/\[(.*?)\] TJ/', $stream, $textMatches)) {
                     foreach ($textMatches[1] as $t) {
                         // Clean up array format
                         $t = preg_replace('/\s*[-0-9.]+\s*/', '', $t); 
                         $t = str_replace(array('(', ')'), '', $t);
                         $text .= $t . " ";
                     }
                }
            }
        }
        
        // Cleanup
        $text = preg_replace('/\\\\\(/', '(', $text);
        $text = preg_replace('/\\\\\)/', ')', $text);
        $text = preg_replace('/[^a-zA-Z0-9\s,.-]/', '', $text); // Basic cleanup
        
        return trim($text);
    }
}
?>
