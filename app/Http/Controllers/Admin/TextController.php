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

    /**
     * Save one text in one language. Saving the file default removes the
     * override. CV dates are saved for every language at once.
     */
    public function update(Request $request): RedirectResponse
    {
        $isDate = $this->catalog->isDate((string) $request->input('key'));
        $data = $this->validated($request, ['value' => $isDate
            ? [$this->isEndDate($request) ? 'nullable' : 'required', 'string', 'regex:'.TextCatalog::DATE_FORMAT]
            : ['required', 'string', 'max:10000'],
        ]);
        $value = $data['value'] ?? ''; // an empty end date means "present"

        foreach ($this->targetLocales($data['locale'], $isDate) as $locale) {
            $default = $this->catalog->defaults($locale, $data['group'])[$data['key']] ?? '';
            $match = ['locale' => $locale, 'group' => $data['group'], 'key' => $data['key']];

            if ($value === $default) {
                Translation::where($match)->first()?->delete();
            } else {
                Translation::updateOrCreate($match, ['value' => $value]);
            }
        }

        return back()->with('status', "Saved {$this->label($data, $isDate)}.");
    }

    /** Reset one text in one language (a date: in all languages) to its lang file default. */
    public function destroy(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $isDate = $this->catalog->isDate($data['key']);

        Translation::query()
            ->where(['group' => $data['group'], 'key' => $data['key']])
            ->whereIn('locale', $this->targetLocales($data['locale'], $isDate))
            ->get()->each->delete();

        return back()->with('status', "Reset {$this->label($data, $isDate)} to the default.");
    }

    /** @return list<string> */
    private function targetLocales(string $locale, bool $isDate): array
    {
        return $isDate ? $this->catalog->locales() : [$locale];
    }

    private function isEndDate(Request $request): bool
    {
        return str_ends_with((string) $request->input('key'), '.to') || $request->input('key') === 'to';
    }

    private function label(array $data, bool $isDate): string
    {
        return "{$data['group']}.{$data['key']} (".($isDate ? 'all languages' : $data['locale']).')';
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
