<?php

namespace App\Http\Controllers;

use App\Models\ProjectRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    /** Save a "Start a project" brief. The phone must be an 11-digit Bangladeshi mobile number. */
    public function store(Request $request)
    {
        $request->merge(['phone' => ProjectRequest::normalisePhone($request->input('phone'))]);

        // Bots fill the hidden "website" field. A brief that has it is still kept, under "Spam" in the admin,
        // so a real visitor whose browser filled it by mistake is never lost.
        if (filled($request->input('website'))) {
            ProjectRequest::create([
                'name' => mb_substr((string) $request->input('name'), 0, 120) ?: '(no name)',
                'phone' => mb_substr((string) $request->input('phone'), 0, 11),
                'email' => mb_substr((string) $request->input('email'), 0, 190) ?: null,
                'company' => mb_substr((string) $request->input('company'), 0, 160) ?: null,
                'message' => mb_substr((string) $request->input('message'), 0, 3000) ?: null,
                'status' => 'spam',
                'ip' => $request->ip(),
            ]);

            return redirect()->to(route('contact') . '#brief')->with('brief_sent', true);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'regex:' . ProjectRequest::PHONE_RULE],
            'email' => ['nullable', 'email', 'max:190'],
            'company' => ['nullable', 'string', 'max:160'],
            'services' => ['nullable', 'array'],
            'services.*' => [Rule::in(Service::active()->pluck('title')->all())],
            'budget' => ['nullable', Rule::in(ProjectRequest::BUDGETS)],
            'timeline' => ['nullable', Rule::in(ProjectRequest::TIMELINES)],
            'message' => ['nullable', 'string', 'max:3000'],
        ], [
            'phone.required' => 'Please enter your phone number.',
            'phone.regex' => 'Enter an 11-digit Bangladeshi mobile number, like 01712345678.',
        ]);

        ProjectRequest::create($data + ['status' => 'new', 'ip' => $request->ip()]);

        return redirect()->to(route('contact') . '#brief')->with('brief_sent', true);
    }
}
