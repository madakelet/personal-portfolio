<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';
</script>

<template>
    <AdminLayout>
        <Head title="Admin" />
        <h1>Admin</h1>
        <ul class="mt-4">
            <li><Link href="/admin/posts">Posts</Link>: write, schedule and publish blog posts.</li>
            <li><Link href="/admin/texts">Texts</Link>: edit every text on the site, per language.</li>
            <li><Link href="/admin/projects">Projects</Link>: add, edit, reorder and remove projects.</li>
        </ul>
    </AdminLayout>
</template>
