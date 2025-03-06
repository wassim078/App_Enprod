<?php
// src/Service/DeepLTranslationService.php

namespace App\Service;

use DeepL\DeepLException;
use DeepL\Translator;

class DeepLTranslationService
{
    private Translator $translator;
    
    public function __construct(string $apiKey)
    {
        $this->translator = new Translator($apiKey);
    }
    
    /**
     * Translate text to target language
     */
    public function translate(string $text, string $targetLang = 'EN'): string
    {
        if (empty($text)) {
            return $text;
        }
        
        try {
            $result = $this->translator->translateText($text, null, $targetLang);
            return $result->text;
        } catch (DeepLException $e) {
            // Log error but return original text to avoid breaking the application
            error_log('DeepL translation error: ' . $e->getMessage());
            return $text;
        }
    }
    
    /**
     * Translate an array of texts
     */
    public function translateBatch(array $texts, string $targetLang = 'EN'): array
    {
        if (empty($texts)) {
            return $texts;
        }
        
        try {
            $results = $this->translator->translateText($texts, null, $targetLang);
            
            return array_map(function($result) {
                return $result->text;
            }, $results);
        } catch (DeepLException $e) {
            error_log('DeepL translation error: ' . $e->getMessage());
            return $texts;
        }
    }
    
    /**
     * Translate object properties
     */
    public function translateObject(object $object, array $properties, string $targetLang = 'EN'): object
    {
        $textsToTranslate = [];
        $propertyMap = [];
        
        // Prepare texts for batch translation
        foreach ($properties as $property) {
            $getter = 'get' . ucfirst($property);
            if (method_exists($object, $getter)) {
                $value = $object->$getter();
                if (is_string($value) && !empty($value)) {
                    $textsToTranslate[] = $value;
                    $propertyMap[] = $property;
                }
            }
        }
        
        // Translate texts in batch
        if (!empty($textsToTranslate)) {
            $translatedTexts = $this->translateBatch($textsToTranslate, $targetLang);
            
            // Update object with translated texts
            foreach ($translatedTexts as $index => $translatedText) {
                $property = $propertyMap[$index];
                $setter = 'set' . ucfirst($property);
                if (method_exists($object, $setter)) {
                    $object->$setter($translatedText);
                }
            }
        }
        
        return $object;
    }
}