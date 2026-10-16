<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineProps({
    posts: { type: Array, required: true },
});

const formatDate = (iso) =>
    iso ? new Date(iso).toLocaleString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';
</script>

<template>
    <AdminLayout>
        <Head title="Posts" />
        <div class="texts-head">
            <h1>Posts</h1>
            <Link href="/admin/posts/create" class="btn btn-primary">+ new post</Link>
        </div>

        <p v-if="!posts.length" class="muted mt-4">No posts yet.</p>
        <ul v-else class="post-list">
            <li v-for="post in posts" :key="post.id">
                <span class="post-status" :class="`is-${post.status}`">{{ post.status }}</span>
                <span class="post-title">
                    <Link :href="`/admin/posts/${post.id}/edit`">{{ post.title }}</Link>
                    <span class="muted"> · {{ post.locale }}</span>
                </span>
                <span class="muted post-date">{{ formatDate(post.published_at) }}</span>
                <a v-if="post.url" :href="post.url" class="muted post-view" target="_blank" rel="noopener">view</a>
            </li>
        </ul>
    </AdminLayout>
</template>
