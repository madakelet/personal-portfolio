<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    locales: { type: Object, required: true }, // { en: 'English', ... }
    sections: { type: Array, required: true },
});

const page = usePage();
const errors = computed(() => page.props.errors ?? {});
const filter = ref('');
const saving = ref(null);

const id = (row, locale) => `${row.group}.${row.key}.${locale}`;
const current = (row, locale) => row.overrides[locale] ?? row.defaults[locale];

// Editable copies, keyed by group.key.locale. They survive the reload after a save.
const drafts = reactive({});
for (const section of props.sections) {
    for (const row of section.rows) {
        for (const locale of Object.keys(props.locales)) drafts[id(row, locale)] = current(row, locale);
    }
}

const isDirty = (row, locale) => drafts[id(row, locale)] !== current(row, locale);
const isEdited = (row, locale) => row.overrides[locale] !== null;

// ":github", ":email" etc. A missing one breaks a link or leaves a gap.
const placeholders = (text) => new Set(text.match(/:[a-z_]+/g) ?? []);
function missingPlaceholders(row, locale) {
    const draft = placeholders(drafts[id(row, locale)] ?? '');
    return [...placeholders(row.defaults[locale])].filter((p) => !draft.has(p));
}

const visibleSections = computed(() => {
    const q = filter.value.trim().toLowerCase();
    if (!q) return props.sections;
    return props.sections
        .map((s) => ({
            ...s,
            rows: s.rows.filter((row) =>
                [`${row.group}.${row.key}`, row.context ?? '', ...Object.keys(props.locales).map((l) => drafts[id(row, l)])]
                    .some((text) => text.toLowerCase().includes(q)),
            ),
        }))
        .filter((s) => s.rows.length);
});

function payload(row, locale) {
    return { locale, group: row.group, key: row.key };
}

function save(row, locale) {
    saving.value = id(row, locale);
    router.put('/admin/texts', { ...payload(row, locale), value: drafts[id(row, locale)] }, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => (saving.value = null),
    });
}

function reset(row, locale) {
    saving.value = id(row, locale);
    router.delete('/admin/texts', {
        data: payload(row, locale),
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (drafts[id(row, locale)] = row.defaults[locale]),
        onFinish: () => (saving.value = null),
    });
}

function revert(row, locale) {
    drafts[id(row, locale)] = current(row, locale);
}

const rowsFor = (text) => Math.min(14, Math.max(2, Math.ceil((text ?? '').length / 42) + 1));
</script>

<template>
    <AdminLayout>
        <Head title="Texts" />
        <div class="texts-head">
            <h1>Texts</h1>
            <input v-model="filter" type="search" placeholder="filter by key or text" aria-label="Filter texts" />
        </div>
        <p class="muted texts-help">
            Every text on the site, per language. Saving changes the live site immediately. "edited" marks a text that differs
            from the default in the repo; reset brings the default back. Some texts contain HTML links: keep the
            <code>&lt;a&gt;</code> tags and the <code>:placeholders</code>.
        </p>
        <p v-for="(message, field) in errors" :key="field" class="error">{{ message }}</p>

        <section v-for="section in visibleSections" :key="section.name">
            <h2>{{ section.name }}</h2>
            <div v-for="row in section.rows" :key="`${row.group}.${row.key}`" class="text-row">
                <div class="text-key">
                    <code>{{ row.key }}</code>
                    <span v-if="row.context" class="muted"> · {{ row.context }}</span>
                </div>
                <div class="text-locales">
                    <div v-for="(label, locale) in locales" :key="locale" class="text-cell">
                        <label :for="id(row, locale)">
                            {{ locale }}
                            <span v-if="isEdited(row, locale)" class="tag">edited</span>
                            <span v-if="isDirty(row, locale)" class="tag">unsaved</span>
                        </label>
                        <textarea
                            :id="id(row, locale)"
                            v-model="drafts[id(row, locale)]"
                            :lang="locale"
                            :rows="rowsFor(drafts[id(row, locale)])"
                            @keydown.meta.enter.prevent="isDirty(row, locale) && save(row, locale)"
                            @keydown.ctrl.enter.prevent="isDirty(row, locale) && save(row, locale)"
                        />
                        <p v-for="p in missingPlaceholders(row, locale)" :key="p" class="warn">{{ p }} is missing</p>
                        <div v-if="isDirty(row, locale) || isEdited(row, locale)" class="text-actions">
                            <button
                                type="button"
                                class="btn btn-primary"
                                :disabled="!isDirty(row, locale) || saving === id(row, locale)"
                                @click="save(row, locale)"
                            >
                                save
                            </button>
                            <button v-if="isDirty(row, locale)" type="button" class="btn" @click="revert(row, locale)">undo</button>
                            <button
                                v-if="isEdited(row, locale)"
                                type="button"
                                class="btn"
                                :disabled="saving === id(row, locale)"
                                @click="reset(row, locale)"
                            >
                                reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <p v-if="!visibleSections.length" class="muted">No texts match "{{ filter }}".</p>
    </AdminLayout>
</template>
