<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

const props = defineProps({
    post: { type: Object, default: null }, // null when creating
    locales: { type: Object, required: true },
});

const isNew = computed(() => props.post === null);

// <input type="datetime-local"> works in the browser's timezone; the server gets ISO 8601 with offset.
const pad = (n) => String(n).padStart(2, '0');
function toLocalInput(iso) {
    if (!iso) return '';
    const d = new Date(iso);
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

const form = useForm({
    title: props.post?.title ?? '',
    slug: props.post?.slug ?? '',
    locale: props.post?.locale ?? 'en',
    excerpt: props.post?.excerpt ?? '',
    body: props.post?.body ?? '',
    published_at: toLocalInput(props.post?.published_at),
});

// The slug follows the title until it is edited by hand (new posts only).
const slugify = (text) =>
    text.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
const slugTouched = ref(!isNew.value);
watch(() => form.title, (title) => {
    if (!slugTouched.value) form.slug = slugify(title);
});

function submit() {
    const options = { preserveScroll: true };
    form.transform((data) => ({ ...data, published_at: data.published_at ? new Date(data.published_at).toISOString() : null }));
    if (isNew.value) form.post('/admin/posts', options);
    else form.put(`/admin/posts/${props.post.id}`, options);
}

function publishNow() {
    form.published_at = toLocalInput(new Date().toISOString());
    submit();
}

function unpublish() {
    form.published_at = '';
    submit();
}

function destroy() {
    if (!confirm(`Delete "${props.post.title}"? This cannot be undone.`)) return;
    router.delete(`/admin/posts/${props.post.id}`);
}

const status = computed(() => {
    if (!form.published_at) return 'draft';
    return new Date(form.published_at) > new Date() ? 'scheduled' : 'published';
});

// Preview: the server renders the Markdown exactly as the site does.
const tab = ref('write');
const previewHtml = ref('');
const previewLoading = ref(false);
function xsrfToken() {
    const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/);
    return match ? decodeURIComponent(match[1]) : '';
}
async function showPreview() {
    tab.value = 'preview';
    previewLoading.value = true;
    try {
        const response = await fetch('/admin/posts/preview', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-XSRF-TOKEN': xsrfToken() },
            body: JSON.stringify({ body: form.body }),
        });
        previewHtml.value = response.ok ? (await response.json()).html : '<p>Preview failed.</p>';
    } finally {
        previewLoading.value = false;
    }
}

// Cmd/Ctrl+S saves.
function onKeydown(event) {
    if ((event.metaKey || event.ctrlKey) && event.key === 's') {
        event.preventDefault();
        if (!form.processing) submit();
    }
}
onMounted(() => window.addEventListener('keydown', onKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown));
</script>

<template>
    <AdminLayout>
        <Head :title="isNew ? 'New post' : post.title" />
        <p class="muted"><Link href="/admin/posts">← posts</Link></p>
        <div class="texts-head">
            <h1>{{ isNew ? 'New post' : 'Edit post' }}</h1>
            <span class="post-status" :class="`is-${status}`">{{ status }}</span>
        </div>

        <form class="post-form" @submit.prevent="submit">
            <div class="post-fields">
                <div class="post-field-wide">
                    <label for="title">title</label>
                    <input id="title" v-model="form.title" type="text" required />
                    <p v-if="form.errors.title" class="error">{{ form.errors.title }}</p>
                </div>
                <div class="post-field-wide">
                    <label for="slug">slug <span class="muted">(the address: /blog/{{ form.slug || '…' }})</span></label>
                    <input id="slug" v-model="form.slug" type="text" required @input="slugTouched = true" />
                    <p v-if="form.errors.slug" class="error">{{ form.errors.slug }}</p>
                </div>
                <div>
                    <label for="locale">language of the post</label>
                    <select id="locale" v-model="form.locale">
                        <option v-for="(label, code) in locales" :key="code" :value="code">{{ label }}</option>
                    </select>
                </div>
                <div>
                    <label for="published_at">publish date <span class="muted">(empty = draft)</span></label>
                    <input id="published_at" v-model="form.published_at" type="datetime-local" />
                    <p v-if="form.errors.published_at" class="error">{{ form.errors.published_at }}</p>
                </div>
                <div class="post-field-wide">
                    <label for="excerpt">excerpt <span class="muted">(one line under the title in the list, and the page description)</span></label>
                    <textarea id="excerpt" v-model="form.excerpt" rows="2" />
                    <p v-if="form.errors.excerpt" class="error">{{ form.errors.excerpt }}</p>
                </div>
            </div>

            <div class="post-tabs" role="tablist">
                <button type="button" role="tab" :aria-selected="tab === 'write'" class="btn" :class="{ 'is-active': tab === 'write' }" @click="tab = 'write'">write</button>
                <button type="button" role="tab" :aria-selected="tab === 'preview'" class="btn" :class="{ 'is-active': tab === 'preview' }" @click="showPreview">preview</button>
                <span class="muted post-hint">Markdown. Code blocks with a language (```php) are highlighted, &gt; quotes become a callout.</span>
            </div>
            <textarea v-show="tab === 'write'" id="body" v-model="form.body" class="post-body-input" rows="24" aria-label="Post body (Markdown)" />
            <div v-show="tab === 'preview'" class="post-preview">
                <p v-if="previewLoading" class="muted">Rendering…</p>
                <div v-else class="post-body" :lang="form.locale" v-html="previewHtml" />
            </div>
            <p v-if="form.errors.body" class="error">{{ form.errors.body }}</p>

            <div class="text-actions post-actions">
                <button type="submit" class="btn btn-primary" :disabled="form.processing">save</button>
                <button v-if="status === 'draft'" type="button" class="btn" :disabled="form.processing" @click="publishNow">publish now</button>
                <button v-else type="button" class="btn" :disabled="form.processing" @click="unpublish">back to draft</button>
                <a v-if="!isNew && post.status === 'published'" :href="post.url" class="btn" target="_blank" rel="noopener">view</a>
                <span class="muted post-hint">⌘S saves</span>
                <button v-if="!isNew" type="button" class="btn post-delete" @click="destroy">delete</button>
            </div>
        </form>
    </AdminLayout>
</template>
