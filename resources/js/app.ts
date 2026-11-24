import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
            case name.startsWith('teams/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    withApp: (app) => {
        app.directive('focus', {
            mounted: (el: HTMLElement, shouldFocus) => {
                if (shouldFocus.value !== false) {
                    el.focus();
                }
            },
        });
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();

/**
 * app.ts — Bootstrap do front-end (Vue + Inertia)
 *
 * Roda uma única vez, no carregamento da página. Responsável por:
 *
 * 1. Criar a aplicação Inertia (createInertiaApp), conectando o roteamento
 *    do Laravel com os componentes Vue.
 *
 * 2. Definir o título da aba: "Nome da página - Igravs".
 *
 * 3. Resolver o layout por pasta (dentro do resolve, em page.default.layout):
 *    - Welcome       → sem layout
 *    - auth/*        → AuthLayout
 *    - settings/*    → AppLayout + SettingsLayout
 *    - teams/*       → AppLayout + SettingsLayout
 *    - resto         → AppLayout
 *    É aqui que a página "sabe" qual moldura usar, sem importar nada.
 *
 * 4. Registrar serviços globais: diretivas (v-focus) no withApp, e no fim
 *    initializeTheme() (tema claro/escuro) e initializeFlashToast() (toasts).
 *
 * QUANDO MEXER AQUI:
 * - Nova pasta em pages/ que precise de layout próprio → novo case no resolve
 * - Nova diretiva global (v-mask, v-tooltip) → registrar no withApp
 * - Novo serviço que roda uma vez no boot → junto de initializeTheme
 *
 * QUANDO NÃO MEXER:
 * - Lógica de negócio, chamadas de API, estado global → página ou composable
 * - Imports de páginas → o import.meta.glob já carrega dinamicamente
 *
 * Regra: se precisa rodar uma vez no boot, é aqui.
 *        Se é comportamento de tela, é na página ou no composable.
 */
