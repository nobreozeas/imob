<script setup lang="ts">
import { Building2, ShieldCheck, TrendingUp, Zap } from 'lucide-vue-next';
</script>

<template>
    <div class="min-h-screen flex">
        <!-- Painel esquerdo: marketing -->
        <div class="hidden lg:flex lg:w-1/2 bg-primary flex-col justify-between p-12 relative overflow-hidden">
            <!-- Imagem de fundo com overlay -->
            <div class="absolute inset-0 bg-primary/80 z-10"></div>
            <div
                class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=1200&auto=format&fit=crop&q=80')"
            ></div>

            <!-- Conteúdo sobre o overlay -->
            <div class="relative z-20 flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <Building2 class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <p class="text-white font-semibold text-lg leading-none">ImobGestor</p>
                        <p class="text-white/70 text-xs">Gestão Imobiliária</p>
                    </div>
                </div>

                <!-- Tagline central -->
                <div class="flex-1 flex flex-col justify-center">
                    <h1 class="text-white text-4xl font-bold leading-tight mb-4">
                        Sua imobiliária,<br>totalmente organizada.
                    </h1>
                    <p class="text-white/80 text-lg mb-10">
                        Gerencie imóveis, contratos, recebimentos e repasses de forma simples, segura e eficiente.
                    </p>

                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <Zap class="w-4 h-4 text-white" />
                            </div>
                            <div>
                                <p class="text-white font-medium">Visão completa do seu negócio</p>
                                <p class="text-white/60 text-sm">Dashboard com todos os indicadores em tempo real</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <ShieldCheck class="w-4 h-4 text-white" />
                            </div>
                            <div>
                                <p class="text-white font-medium">Segurança e confiabilidade</p>
                                <p class="text-white/60 text-sm">Seus dados protegidos com tecnologia de ponta</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <TrendingUp class="w-4 h-4 text-white" />
                            </div>
                            <div>
                                <p class="text-white font-medium">Mais produtividade</p>
                                <p class="text-white/60 text-sm">Automatize processos e ganhe tempo</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Rodapé -->
                <p class="text-white/40 text-xs">
                    © {{ new Date().getFullYear() }} ImobGestor — Todos os direitos reservados
                </p>
            </div>
        </div>

        <!-- Painel direito: formulário -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-base-100">
            <div class="w-full max-w-md">
                <!-- Logo mobile -->
                <div class="flex items-center gap-3 mb-8 lg:hidden">
                    <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center">
                        <Building2 class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <p class="font-semibold text-lg leading-none">ImobGestor</p>
                        <p class="text-base-content/50 text-xs">Gestão Imobiliária</p>
                    </div>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>
