<!-- resources/js/pages/onboarding/Church.vue -->
<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';

const form = useForm({
    name: '',
    city: '',
    denomination: '',
});
defineOptions({
    layout: {
        title: 'Dados da igreja',
        description: 'Conte um pouco sobre a sua igreja para começar.',
    },
});

function submit() {
    form.post('/onboarding/igreja');
}
</script>

<template>
    <Head title="Dados da igreja" />

    <form class="space-y-4" @submit.prevent="submit">
        <div>
            <Label for="name">Nome da igreja</Label>
            <Input
                id="name"
                v-model="form.name"
                placeholder="Igreja Batista Central"
                required
            />
            <InputError :message="form.errors.name" />
        </div>

        <div>
            <Label for="city">Cidade</Label>
            <Input
                id="city"
                v-model="form.city"
                placeholder="São Paulo"
                required
            />
            <InputError :message="form.errors.city" />
        </div>

        <div>
            <Label for="denomination">Denominação (opcional)</Label>
            <Input
                id="denomination"
                v-model="form.denomination"
                placeholder="Batista"
            />
            <InputError :message="form.errors.denomination" />
        </div>

        <Button type="submit" class="w-full" :disabled="form.processing">
            Criar igreja
        </Button>
    </form>
</template>
