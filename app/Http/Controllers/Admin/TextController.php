<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use App\Translation\TextCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TextController extends Controller
{
    public function __construct(private TextCatalog $catalog) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Texts', [
            'locales' => config('site.locales'),
            'sections' => $this->catalog->sections(),
        ]);
    }

    /** Save one text in one language. Saving the file default removes the override. */
    public function update(Request $request): RedirectResponse
    {
        $data = $this->validated($request, ['value' => ['required', 'string', 'max:10000']]);
        $default = $this->catalog->defaults($data['locale'], $data['group'])[$data['key']] ?? null;
        $match = ['locale' => $data['locale'], 'group' => $data['group'], 'key' => $data['key']];

        if ($data['value'] === $default) {
            Translation::where($match)->first()?->delete();
        } else {
            Translation::updateOrCreate($match, ['value' => $data['value']]);
        }

        return back()->with('status', "Saved {$data['group']}.{$data['key']} ({$data['locale']}).");
    }

    /** Reset one text in one language to its lang file default. */
    public function destroy(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Translation::where($data)->first()?->delete();

        return back()->with('status', "Reset {$data['group']}.{$data['key']} ({$data['locale']}) to the default.");
    }

    private function validated(Request $request, array $extra = []): array
    {
        return $request->validate([
            'locale' => ['required', Rule::in($this->catalog->locales())],
            'group' => ['required', Rule::in(TextCatalog::GROUPS)],
            'key' => ['required', 'string', function ($attribute, $value, $fail) use ($request) {
                if (! $this->catalog->isEditable((string) $request->input('group'), (string) $value)) {
                    $fail('This text cannot be edited.');
                }
            }],
            ...$extra,
        ]);
    }
}
