<script setup>
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref, watch } from 'vue';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    locales: { type: Object, required: true }, // { en: 'English', ... }
    projects: { type: Array, required: true },
});

const page = usePage();
const errors = computed(() => page.props.errors ?? {});
const fields = ['title', 'meta', 'body'];
const codes = computed(() => Object.keys(props.locales));

const blank = () => Object.fromEntries(fields.map((f) => [f, Object.fromEntries(codes.value.map((l) => [l, '']))]));
const copy = (project) => Object.fromEntries(fields.map((f) => [f, { ...blank()[f], ...(project[f] ?? {}) }]));

// Editable copies per project id, refreshed when the server sends new data.
const drafts = reactive({});
function sync() {
    for (const project of props.projects) drafts[project.id] = copy(project);
}
sync();
watch(() => props.projects, sync);

const isDirty = (project) => JSON.stringify(drafts[project.id]) !== JSON.stringify(copy(project));
const busy = ref(null);
const options = (id) => ({ preserveScroll: true, onStart: () => (busy.value = id), onFinish: () => (busy.value = null) });

function save(project) {
    router.put(`/admin/projects/${project.id}`, drafts[project.id], options(project.id));
}

function remove(project) {
    if (!confirm(`Remove "${project.title.en}"? This cannot be undone.`)) return;
    router.delete(`/admin/projects/${project.id}`, options(project.id));
}

function move(project, direction) {
    router.post(`/admin/projects/${project.id}/move`, { direction }, options(project.id));
}

const adding = ref(false);
const form = useForm(blank());
function add() {
    form.post('/admin/projects', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            adding.value = false;
        },
    });
}

const rowsFor = (text) => Math.min(14, Math.max(3, Math.ceil((text ?? '').length / 42) + 1));
</script>

<template>
    <AdminLayout>
        <Head title="Projects" />
        <h1>Projects</h1>
        <p class="muted texts-help">
            The "Projects" list on the home page, in this order. English title and text are required; an empty Dutch or
            Hungarian field shows the English text. Meta is the grey part in brackets, e.g. "Optimy, 2026".
        </p>
        <p v-for="(message, field) in errors" :key="field" class="error">{{ message }}</p>

        <section v-for="(project, index) in projects" :key="project.id" class="text-row">
            <div class="project-head">
                <strong>{{ index + 1 }}. {{ project.title.en }}</strong>
                <span v-if="isDirty(project)" class="tag">unsaved</span>
                <span class="project-tools">
                    <button type="button" class="btn" :disabled="index === 0 || busy === project.id" @click="move(project, 'up')" aria-label="Move up">↑</button>
                    <button type="button" class="btn" :disabled="index === projects.length - 1 || busy === project.id" @click="move(project, 'down')" aria-label="Move down">↓</button>
                    <button type="button" class="btn" :disabled="busy === project.id" @click="remove(project)">remove</button>
                </span>
            </div>
            <div class="text-locales">
                <div v-for="(label, locale) in locales" :key="locale" class="text-cell">
                    <label :for="`p${project.id}-${locale}-title`">{{ locale }} · title</label>
                    <input :id="`p${project.id}-${locale}-title`" v-model="drafts[project.id].title[locale]" type="text" :lang="locale" />
                    <label :for="`p${project.id}-${locale}-meta`" class="mt-2">meta</label>
                    <input :id="`p${project.id}-${locale}-meta`" v-model="drafts[project.id].meta[locale]" type="text" :lang="locale" />
                    <label :for="`p${project.id}-${locale}-body`" class="mt-2">text</label>
                    <textarea :id="`p${project.id}-${locale}-body`" v-model="drafts[project.id].body[locale]" :lang="locale" :rows="rowsFor(drafts[project.id].body[locale])" />
                </div>
            </div>
            <div v-if="isDirty(project)" class="text-actions">
                <button type="button" class="btn btn-primary" :disabled="busy === project.id" @click="save(project)">save</button>
                <button type="button" class="btn" @click="drafts[project.id] = copy(project)">undo</button>
            </div>
        </section>

        <section class="text-row">
            <button v-if="!adding" type="button" class="btn" @click="adding = true">+ add project</button>
            <form v-else @submit.prevent="add">
                <div class="project-head"><strong>New project</strong> <span class="muted">(added at the end)</span></div>
                <div class="text-locales">
                    <div v-for="(label, locale) in locales" :key="locale" class="text-cell">
                        <label :for="`new-${locale}-title`">{{ locale }} · title</label>
                        <input :id="`new-${locale}-title`" v-model="form.title[locale]" type="text" :lang="locale" :required="locale === 'en'" />
                        <label :for="`new-${locale}-meta`" class="mt-2">meta</label>
                        <input :id="`new-${locale}-meta`" v-model="form.meta[locale]" type="text" :lang="locale" />
                        <label :for="`new-${locale}-body`" class="mt-2">text</label>
                        <textarea :id="`new-${locale}-body`" v-model="form.body[locale]" :lang="locale" rows="5" :required="locale === 'en'" />
                    </div>
                </div>
                <p v-for="(message, field) in form.errors" :key="field" class="error">{{ message }}</p>
                <div class="text-actions">
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">add</button>
                    <button type="button" class="btn" @click="adding = false; form.reset()">cancel</button>
                </div>
            </form>
        </section>
    </AdminLayout>
</template>
