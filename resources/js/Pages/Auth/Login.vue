<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const form = useForm({ email: '', password: '', remember: false });

function submit() {
    form.post('/login', { onFinish: () => form.reset('password') });
}
</script>

<template>
    <AdminLayout>
        <Head title="Log in" />
        <h1>Log in</h1>
        <form class="login" @submit.prevent="submit">
            <div>
                <label for="email">email</label>
                <input id="email" v-model="form.email" type="email" autocomplete="username" required autofocus />
                <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
            </div>
            <div>
                <label for="password">password</label>
                <input id="password" v-model="form.password" type="password" autocomplete="current-password" required />
                <p v-if="form.errors.password" class="error">{{ form.errors.password }}</p>
            </div>
            <label class="remember"><input v-model="form.remember" type="checkbox" /> remember me</label>
            <button class="btn btn-primary" type="submit" :disabled="form.processing">log in</button>
        </form>
    </AdminLayout>
</template>
