<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';
</script>

<template>
    <AdminLayout>
        <Head title="Admin" />
        <h1>Admin</h1>
        <ul class="mt-4">
            <li><Link href="/admin/texts">Texts</Link>: edit every text on the site, per language.</li>
        </ul>
    </AdminLayout>
</template>
