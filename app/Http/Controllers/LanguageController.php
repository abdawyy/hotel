<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Available languages with their native names and flags.
     */
    public static function getAvailableLanguages(): array
    {
        return [
            'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇺🇸'],
            'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦', 'rtl' => true],
            'es' => ['name' => 'Spanish', 'native' => 'Español', 'flag' => '🇪🇸'],
            'fr' => ['name' => 'French', 'native' => 'Français', 'flag' => '🇫🇷'],
            'de' => ['name' => 'German', 'native' => 'Deutsch', 'flag' => '🇩🇪'],
            'pt' => ['name' => 'Portuguese', 'native' => 'Português', 'flag' => '🇧🇷'],
            'zh' => ['name' => 'Chinese', 'native' => '中文', 'flag' => '🇨🇳'],
            'ja' => ['name' => 'Japanese', 'native' => '日本語', 'flag' => '🇯🇵'],
            'ko' => ['name' => 'Korean', 'native' => '한국어', 'flag' => '🇰🇷'],
            'ru' => ['name' => 'Russian', 'native' => 'Русский', 'flag' => '🇷🇺'],
            'hi' => ['name' => 'Hindi', 'native' => 'हिन्दी', 'flag' => '🇮🇳'],
            'tr' => ['name' => 'Turkish', 'native' => 'Türkçe', 'flag' => '🇹🇷'],
            'nl' => ['name' => 'Dutch', 'native' => 'Nederlands', 'flag' => '🇳🇱'],
            'it' => ['name' => 'Italian', 'native' => 'Italiano', 'flag' => '🇮🇹'],
        ];
    }

    /**
     * Get RTL languages.
     */
    public static function getRtlLanguages(): array
    {
        return ['ar'];
    }

    /**
     * Check if a locale is RTL.
     */
    public static function isRtl(string $locale): bool
    {
        return in_array($locale, self::getRtlLanguages());
    }

    /**
     * Switch the application language.
     */
    public function switch($locale)
    {
        $availableLanguages = array_keys(self::getAvailableLanguages());
        
        if (in_array($locale, $availableLanguages)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        }
        
        return redirect()->back();
    }
}
