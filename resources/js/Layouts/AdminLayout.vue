<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const status = computed(() => page.props.flash?.status);

function logout() {
    router.post('/logout');
}
</script>

<template>
    <div class="wrap wrap-admin">
        <header class="site-header">
            <Link class="name" href="/admin">admin</Link>
            <nav v-if="user" aria-label="Admin">
                <Link href="/admin/posts" :aria-current="$page.url.startsWith('/admin/posts') ? 'page' : undefined">posts</Link>
                <Link href="/admin/texts" :aria-current="$page.url.startsWith('/admin/texts') ? 'page' : undefined">texts</Link>
                <Link href="/admin/projects" :aria-current="$page.url.startsWith('/admin/projects') ? 'page' : undefined">projects</Link>
                <a :href="$page.props.siteUrl">view site</a>
                <button type="button" class="link-button" @click="logout">log out</button>
            </nav>
        </header>
        <p v-if="status" class="flash" role="status">{{ status }}</p>
        <main>
            <slot />
        </main>
    </div>
</template>
