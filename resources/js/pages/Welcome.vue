<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ChevronLeft, UserRound, Church, Droplet } from '@lucide/vue';

import { dashboard, login } from '@/routes';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage();

const dashboardUrl = computed(() =>
    page.props.currentTeam ? dashboard(page.props.currentTeam.slug).url : '/',
);

const backUrl = computed(() =>
    page.props.auth.user ? dashboardUrl.value : '/',
);
</script>

<template>
    <Head title="Selecionar perfil" />

    <main
        class="relative flex min-h-screen flex-col overflow-hidden bg-[#F8F7F4] px-5 py-6 text-[#666666] sm:px-8 sm:py-8"
    >
        <!-- Conteúdo central -->
        <section
            class="flex flex-1 flex-col items-center justify-center pt-12 pb-10 sm:pt-6"
        >
            <!-- Logo -->
            <div class="mb-1 flex items-center justify-center rounded-md">
                <AppLogoIcon
                    class="size-40 fill-current text-[var(--foreground)] dark:text-white"
                />
            </div>

            <!-- Título -->
            <h1
                class="mb-12 font-signika text-5xl font-extrabold tracking-tight text-sidebar-foreground sm:text-5xl"
            >
                Igravs
            </h1>
            <h2
                class="mb-8 text-center font-signika text-2xl font-medium text-[#686868] sm:mb-10 sm:text-3xl"
            >
                Entrar como...
            </h2>

            <!-- Opções de perfil -->
            <div
                class="grid w-full max-w-[640px] grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-10"
            >
                <!-- Membro -->
                <Link
                    :href="login()"
                    class="group flex min-h-[210px] flex-col items-center justify-center gap-4 rounded-[24px] border border-[#EAE8E5] bg-white px-6 py-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[#48C6D2] hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#48C6D2] sm:min-h-[240px]"
                >
                    <UserRound
                        :size="88"
                        :stroke-width="1.35"
                        class="text-[#6A6A6A] transition-colors duration-300 group-hover:text-[#2ABCCB]"
                    />

                    <span
                        class="font-signika text-2xl font-medium text-[#686868] transition-colors duration-300 group-hover:text-[#269BA8] sm:text-3xl"
                    >
                        Membro
                    </span>
                </Link>

                <!-- Administrador -->
                <Link
                    :href="login()"
                    class="group flex min-h-[210px] flex-col items-center justify-center gap-4 rounded-[24px] border border-[#EAE8E5] bg-white px-6 py-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[#A42682] hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#A42682] sm:min-h-[240px]"
                >
                    <Church
                        :size="88"
                        :stroke-width="1.35"
                        class="text-[#6A6A6A] transition-colors duration-300 group-hover:text-[#A42682]"
                    />

                    <span
                        class="font-signika text-2xl font-medium text-[#686868] transition-colors duration-300 group-hover:text-[#A42682] sm:text-3xl"
                    >
                        Administrador
                    </span>
                </Link>
            </div>
        </section>
    </main>
</template>
