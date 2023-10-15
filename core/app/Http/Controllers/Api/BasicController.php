<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Language;

class BasicController extends Controller
{
    public function generalSetting()
    {
        $general = GeneralSetting::first();
        $notify = 'General setting data';
        return responseJson(200, 'success', $notify, $general);
    }

    public function unauthenticate()
    {
        $notify = 'Unauthenticated user';

        return responseJson(403, 'unauthorized', $notify);
    }

    public function languages()
    {
        $languages = Language::get();
        $notify = 'Language Data';
        $data = [
            'languages' => $languages,
            'image_path' => imagePath()['language']['path']
        ];
        return responseJson(200, 'success', $notify, $data);
    }

    public function languageData($code)
    {
        $language = Language::where('code', $code)->first();
        if (!$language) {
            $notify = 'Language not found';
            return responseJson(404, 'error', $notify);
        }
        $jsonFile = strtolower($language->code) . '.json';
        $fileData = resource_path('lang/') . $jsonFile;
        $languageData = json_decode(file_get_contents($fileData));
        $notify = 'Language Data';
        return responseJson(200, 'success', $notify, $languageData);
    }

    public function countries()
    {
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $notify = 'Countries Data';
        return responseJson(200, 'success', $notify, $countries);
    }
}
