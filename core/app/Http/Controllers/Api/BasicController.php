<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountriesResource;
use App\Http\Resources\HomeFaqResource;
use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function merchantUnauthenticate()
    {
        $notify = 'Unauthenticated merchant';

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
        $data = [];
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countries = new Collection($countries);
        foreach ($countries as $key => $country) {
            $data[] = [
                'key' => $key,
                'country' => $country->country,
                'dial_code' => $country->dial_code,
            ];
        }
        $notify = 'Countries Data';
        return responseJson(200, 'success', $notify, $data);
    }

    public function submitContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|max:191',
            'subject' => 'required|max:100',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth('api')->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = 2;


        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = 0;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth('api')->user() ? auth('api')->user()->id : 0;
        $adminNotification->title = 'A new support ticket has opened ';
        $adminNotification->click_url = urlPath('admin.user.ticket.view', $ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->supportticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $notify = 'ticket created successfully!';
        return responseJson(200, 'success', $notify);
    }

    public function termsConditions()
    {
        $policys = getContent('policy_pages.element')[0];
        $data = [
            'title' => $policys->data_values->title,
            'details' => $policys->data_values->details,
        ];

        $notify = 'Terms of Service Data';
        return responseJson(200, 'success', $notify, $data);
    }

    public function privacyPolicy()
    {
        $policys = getContent('policy_pages.element')[1];
        $data = [
            'title' => $policys->data_values->title,
            'details' => $policys->data_values->details,
        ];

        $notify = 'Privacy Policy Data';
        return responseJson(200, 'success', $notify, $data);
    }

    public function contactContent()
    {
        $policys = getContent('contact_us.content')[0];

        $data = [
            'title' => $policys->data_values->title,
            'short_details' => $policys->data_values->short_details,
            'email_address' => $policys->data_values->email_address,
            'contact_details' => $policys->data_values->contact_details,
            'contact_number' => $policys->data_values->contact_number,
        ];

        $faq = getContent('faq.content', true);
        $data['faqs']['heading'] = $faq->data_values->heading;
        $data['faqs']['subheading'] = $faq->data_values->subheading;
        $faqs = getContent('faq.element');
        $data['faqs']['lists'] = HomeFaqResource::collection($faqs);

        $notify = 'Contact Content Data';
        return responseJson(200, 'success', $notify, $data);
    }

    public function aboutContent()
    {
        $policys = getContent('about.content')[0];
        $abouts = getContent('about.element');


        $data = [
            "has_image" => $policys->data_values->has_image,
            "heading" => $policys->data_values->heading,
            "subheading" => $policys->data_values->subheading,
            "description" => $policys->data_values->description,
            "video_url" => $policys->data_values->video_url,
            "about_image" => getImage('assets/images/frontend/about/' . $policys->data_values->about_image, '800x530'),
        ];

        foreach ($abouts as $about) {
            $data['about_list'][]['title'] = $about->data_values->about_list;
        }

        $notify = 'About Content Data';
        return responseJson(200, 'success', $notify, $data);
    }
}
